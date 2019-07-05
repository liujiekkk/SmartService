<?php
/**
 * TCP 客户端
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:18:44
 */
namespace Client;
use Client\AbstractClient;
use Client\Config\ClientConfig;
use Common\Protocol\JsonRpc\JsonRpc;
use Common\Protocol\FrameWriter;
use Common\Protocol\Buffer;
use Common\Protocol\FrameReader;
use Common\Protocol\DataFrame;
use Common\Protocol\AbstractResponse;
use Common\Protocol\JsonRpc\JsonRequest;
use Common\Protocol\JsonRpc\JsonResponse;

class ClientRpc extends AbstractClient
{
    
    /**
     * 是否开启异步
     * @var bool
     */
    protected $isAsync;
    
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
     * 异步返回结果
     * @var array
     */
    protected static $asyncResult;
    
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
        $client = new \swoole_client($config->sock_type, $config->async, $config->key);
        $client->set([
            // 自定义协议包
            'package_max_length' => $this->config->package_max_length,
            'package_length_type' => $this->config->package_length_type,
            'package_length_offset' => $this->config->package_length_offset,
            'package_body_offset' => $this->config->package_body_offset,
            'open_tcp_nodelay' => $this->config->open_tcp_nodelay,
        ]);
        $this->client = $client;
        $this->bufferReader = new Buffer();
        $this->bufferWriter = new Buffer();
        $this->frameReader = new FrameReader();
        $this->frameWriter = new FrameWriter();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Common\Client\Client::request()
     */
    public function request(string $class, string $method, array $params = [], $action='user'): array
    {
        if ( !$this->client->connect($this->host, $this->port, $this->timeout, 0) ) {
            throw new \Exception('client connect timeout.');
        }
        // 准备发送数据
        $req = JsonRequest::instance()
            ->setId(uuid_create())
            ->setClass($class)
            ->setMethod($method)
            ->setParams($params);
        $req->setAction($action);
        $str = $req->encode();
        
        $length = $this->bufferWriter->getLength();
        
        $frame = new DataFrame(strlen($str), $str, "\n");
        $this->frameWriter->appendFrame($frame, $this->bufferWriter);
        $s = $this->bufferWriter->consume($this->bufferWriter->getLength());
        if (!$this->client->send($s)) {
            throw new \Exception('Send Error.');
        }
        // 解析数据
        $response = $this->parseRecvData($this);
        return [
            'code' => $response->getCode(),
            'message' => $response->getMessage(),
            'data' => $response->getData()
        ];
    }
    
    // 处理接受到的数据
    protected static function parseRecvData(ClientRpc $client): AbstractResponse 
    {
        $response = JsonResponse::instance();
        $data = $client->client->recv();
        if ($data === false) {
            $client->client->close();
            $response->setCode(100000000)
                ->setMessage('服务器资源不可用')
                ->setData([]);
            return $response;
        } else if ($data === '') {
            // 当收到错误的包头或包头中长度值超过package_max_length设置时，recv会返回空字符串
            $client->client->close();
            $response->setCode(100000000)
                ->setMessage('服务器资源不可用')
                ->setData([]);
            return $response;
        }
        // 解析响应数据
        $client->bufferReader->append($data);
        $frame = $client->frameReader->consumeFrame($client->bufferReader);
        $response->decode($frame->getBody());
        return $response;
    }
    
    /**
     * 发送请求不接受响应数据
     * @param string $class 请求类
     * @param string $method 请求方法
     * @param array $params 请求参数
     * @param callable $callback 回调函数
     */
    public function &requestAsync(string $class, string $method, array $params = []): array
    {
        // 创建本次请求的id
        $requestId = uuid_create();
        if ( !$this->client->connect($this->host, $this->port, $this->timeout, 0) ) {
            throw new \Exception('client connect timeout.');  
        } else {
            self::$clients[$this->client->sock] = $this;
            self::$requestIdArr[$requestId] = 1;
            self::$asyncResult[$requestId] = [];
        }
        // 准备发送数据
        $req = JsonRequest::instance()
            ->setId($requestId)
            ->setClass($class)
            ->setMethod($method)
            ->setParams($params)
            ->setAction('user');
        $str = $req->encode();
        
        $length = $this->bufferWriter->getLength();
        
        $frame = new DataFrame(strlen($str), $str, "\n");
        $this->frameWriter->appendFrame($frame, $this->bufferWriter);
        $s = $this->bufferWriter->consume($this->bufferWriter->getLength());
        if (!$this->client->send($s)) {
            throw new \Exception('Send Error.');
        }
        return self::$asyncResult[$requestId];
    }
    
    /**
     * 执行当前所有异步请求
     */
    public static function executeAsync(): void 
    {
        // 设置超时时间
        $beginTime = time();
        $endTime = time();
        while (!empty(self::$requestIdArr) && ($endTime-$beginTime)<1 )
        {
            // 设置监听套接字
            $write = $error = array();
            $read = [];
            foreach (self::$clients as $client) {
                $read[] = $client->client;
            }
            $n = swoole_client_select($read, $write, $error, 1);
            if ($n > 0)
            {
                foreach ($read as $c) {
                    $jsonRpc = self::parseRecvData(self::$clients[$c->sock]);
                    $reqestId = $jsonRpc->getId();
                    $ret = [
                        'code' => $jsonRpc->getCode(),
                        'message' => $jsonRpc->getMessage(),
                        'data' => $jsonRpc->getData()
                    ];
                    self::$asyncResult[$reqestId] = $ret;
                    unset(self::$requestIdArr[$reqestId]);
                }
            }
            $endTime = time();
        }
        // 清空异步请求响应相关数据存储
        self::$clients = [];
        self::$requestIdArr = [];
        self::$asyncResult = [];
    }
}