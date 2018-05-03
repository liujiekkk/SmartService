<?php
/**
 * TCP 客户端
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:18:44
 */
namespace Common\Client;
use Common\Server\Event\EventVector;
use Common\Server\Event\Event;
use Common\IO\StringBuffer;
use Common\Log\Log;
use Config\Client\Config;

class ClientTcp extends Client 
{
    
    /**
     * 是否开启异步
     * @var bool
     */
    protected $isAsync;
    
    /**
     * 日志对象实例
     * @var Log
     */
    protected $log;
    
    /**
     * 初始化 Client
     * @param string $host 指定监听的IP地址
     * @param int $port 监听端口号
     * @param $is_sync 是否是同步客户端
     * @param $key 客户端唯一标识
     */
    public function __construct(Config $config)
    {
        
        $this->isAsync = $config->async;
        $this->log = new Log($config->log, $config->debug_mode);
        $this->client = new \swoole_client($config->sock_type, $config->async, $config->key);
    }
    
    public function initEvent() :EventVector
    {
        $events = new EventVector();
        $events->addEvent(new Event('connect', [$this, 'onConnect']));
        $events->addEvent(new Event('receive', [$this, 'onReceive']));
        $events->addEvent(new Event('close', [$this, 'onClose']));
        $events->addEvent(new Event('error', [$this, 'onError']));
        return $events;
    }
    
    public function access(): bool 
    {
        $host = $this->connection->getHeader('host');
        $port = $this->connection->getHeader('port');
        $timout = 0.5;
        $flag = 0;
        // 异步客户端，同步客户端处理方式不一样
        if ( $this->isAsync ) {
            // 初始化客户端事件
            $events = $this->initEvent();
            $this->loadEvent($events);
            return $this->client->connect($host, $port, $timout, $flag);
        } else {
            if ( !$this->client->connect($host, $port, $timout, $flag) ) {
                $this->log->error('Connect failed.');
            } else {
                self::$clients[$this->client->sock] = $this->client;
            }
            
            // 链接成功以后发送请求数据
            $buffer = new StringBuffer();
            // 写入buffer
            $this->connection->writeBuffer($buffer);
            if ( !$this->client->send($buffer->read()) ) {
                $this->log->error('Send failed.');
            }
            $data = $this->client->recv();
            if ( !$data ) {
                $this->log->error(' Recv failed.');
            }
            // 解析响应数据
            $buffer = new StringBuffer();
            $buffer->writeTo($data);
            $this->connection->setResponse(new \Common\Connection\Rpc\RpcResponse());
            $this->connection->readBuffer($buffer);
            return $this->client->close();
        }
    }
    
    /**
     * 链接建立成功相应函数
     * @param Swoole\Client $client
     * @param string $data
     */
    public function onConnect($client, $data='') 
    {
        $this->log->info(__METHOD__.' Connect success.');
        // 链接成功以后发送请求数据
        $buffer = new StringBuffer();
        // 写入buffer
        $this->connection->writeBuffer($buffer);
        $client->send($buffer->read());
    }
    
    public function onReceive($client, string $data='') 
    {
        if (!empty($data)) {
            // 解析响应数据
            $buffer = new StringBuffer();
            $buffer->writeTo($data);
            $this->connection->setResponse(new \Common\Connection\Rpc\RpcResponse());
            $this->connection->readBuffer($buffer);
            $client->close();
            return;
        } else {
            $this->log->info(__METHOD__. ' No response');
        }
    }
    
    public function onClose($client) 
    {
        $this->log->info(__METHOD__. ' Client close.');
    }
    
    public function onError($client) 
    {
        exit("error\n");
    }
}