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
use Common\Config\ClientConfig;
use Common\Log\Log;


class ClientRpc extends Client 
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
    
    protected $host;
    
    protected $port;
    
    protected $timeout;
    
    protected $config;
    
    /**
     * 初始化 Client
     * @param ClientConfig $config 配置文件对象
     */
    public function __construct(ClientConfig $config)
    {
        $this->config = $config;
        $this->isAsync = $config->async;
        $this->host = $config->host;
        $this->port = $config->port;
        $this->timeout = $config->timeout;
        $this->log = new Log($config->log, $config->debug_mode);
        $client = new \swoole_client($config->sock_type, $config->async, $config->key);
        $client->set([
            // 自定义协议包
            'package_max_length' => $this->config->package_max_length,
            'package_length_type' => $this->config->package_length_type,
            'package_length_offset' => $this->config->package_length_offset,
            'package_body_offset' => $this->config->package_body_offset,
//             'open_eof_split' => true,
//             'open_eof_check' => true,
//             'open_length_check' => true,
//             'package_eof' => "\r\n\r\n",
            'open_tcp_nodelay' => $this->config->open_tcp_nodelay,
        ]);
        $this->client = $client;
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
        $flag = 0;
        // 异步客户端，同步客户端处理方式不一样
        if ( $this->isAsync ) {
            // 初始化客户端事件
            $events = $this->initEvent();
            $this->loadEvent($events);
            return $this->client->connect($this->host, $this->port, $this->timeout, $flag);
        } else {
            if ( !$this->client->connect($this->host, $this->port, $this->timeout, $flag) ) {
                $this->log->error('Connect failed.');
                return false;
            } else {
                self::$clients[$this->client->sock] = $this->client;
            }
            
            // 链接成功以后发送请求数据
            $buffer = new StringBuffer();
            // 写入buffer
            $this->connection->writeBuffer($buffer);
            $len = strlen($buffer->read());
            $msg = pack('N4', 0, 0, $len, 0) . $buffer->read();
            if (!$this->client->send($msg)) {
                $this->log->error('Send failed.');
            }
            $data = $this->client->recv();
            if ($data === false) {
                // 如果设置了错误的 recv $size，会导致recv超时
                $this->log->warning('receive data time out.');
                return $this->client->close();
            } else if ($data === '') {
                // 当收到错误的包头或包头中长度值超过package_max_length设置时，recv会返回空字符串
                $this->log->warning('error data header or header size is above package_max_length.');
                return $this->client->close();
            }
            // 解析响应数据
            $header = unpack("Ntype/Nuid/Nlen/Nserid" , $data);
            // 包长度
            $length = $header['len'];
            $msg = substr($data, $this->config->package_body_offset, $length);
            
            $buffer = new StringBuffer();
            $buffer->writeTo($msg);
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