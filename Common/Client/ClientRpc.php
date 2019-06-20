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
use Common\Protocol\JsonRpc\JsonRpc;
use Common\CallFactory\JsonRpcFactory;
use Common\Protocol\FrameWriter;
use Common\Protocol\Buffer;
use Common\Protocol\FrameReader;
use Common\Protocol\JsonRpc\JsonRequest;
use Common\Protocol\DataFrame;
use Common\Protocol\JsonRpc\JsonResponse;


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
     * 
     * @var Buffer
     */
    protected $bufferWriter;
    
    /**
     * 
     * @var Buffer
     */
    protected $bufferReader;
    
    /**
     * 
     * @var FrameReader
     */
    protected $frameReader;
    
    /**
     * 
     * @var FrameWriter 
     */
    protected $frameWriter;
    
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
        $this->bufferReader = new Buffer();
        $this->bufferWriter = new Buffer();
        $this->frameReader = new FrameReader();
        $this->frameWriter = new FrameWriter();
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
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Client\Client::request()
     */
    public function request(string $class, string $method, array $params = [], $action='user'): array
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
            // 准备发送数据
            $req = new JsonRequest($class, $method, $params);
            $req->setAction($action);
            $str = $req->encode();
            
            $length = $this->bufferWriter->getLength();
            
            $frame = new DataFrame(strlen($str), $str, "\n");
            $this->frameWriter->appendFrame($frame, $this->bufferWriter);
            $s = $this->bufferWriter->consume($this->bufferWriter->getLength());
            if (!$this->client->send($s)) {
                $this->log->error('Send failed.');
            }
            $data = $this->client->recv();
            if ($data === false) {
                // 如果设置了错误的 recv $size，会导致recv超时
                $this->log->warning('receive data time out.');
                $this->client->close();
                return ['code' => 100000000, '服务器资源不可用', []];
            } else if ($data === '') {
                // 当收到错误的包头或包头中长度值超过package_max_length设置时，recv会返回空字符串
                $this->log->warning('error data header or header size is above package_max_length.');
                $this->client->close();
                return ['code' => 100000000, '服务器资源不可用', []];
            }
            // 解析响应数据
            $this->bufferReader->append($data);
            $frame = $this->frameReader->consumeFrame($this->bufferReader);
            $jsonRpc = JsonResponse::decode($frame->getBody());
            
            $ret = [
                'code' => $jsonRpc->getCode(),
                'message' => $jsonRpc->getMessage(),
                'data' => $jsonRpc->getData()
            ];
            return $ret;
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