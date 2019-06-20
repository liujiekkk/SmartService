<?php
/**
 * Server 类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:16:35
 */
namespace Common\Server;

use Common\Server\Event\EventVector;
use Common\Connection\Connection;
use Common\Config\ServerConfig;
use Common\Log\Log;
use Library\Format\Path;


abstract class Server {
    
    /**
     * 服务端客户端链接
     * @var Connection
     */
    protected $connection;
    
    /**
     * 存储单例对象
     * @var Server
     */
    protected static $instance;
    
    /**
     * 存储 Swoole\Server 对象实例
     * @var Swoole\Server
     */
    protected $server;
    
    /**
     * 日志模块
     * @var Log
     */
    protected $log;
    
    /**
     * 服务端配置
     * @var ServerConfig
     */
    protected $config;
    
    /**
     * 构造函数私有化
     */
    protected function __construct() {}
    /**
     * 克隆私有化
     */
    protected function __clone() {}
    
    /**
     * 实例化 server 对象
     * @param ServerConfig $config 服务端配置项
     * @return Server
     */
    public static function instance(ServerConfig $config) :Server
    {
        if ( !self::$instance ) {
            self::$instance = new static();
            self::$instance->config = $config;
            // 多进程模式：SWOOLE_PROCESS 基础模式：SWOOLE_BASE
            self::$instance->server = new \swoole_server($config->host, $config->port, $config->mode, $config->sock_type);
            self::$instance->server->set([
                'reactor_num' => $config->reactor_num,
                'worker_num' => $config->worker_num,
                'backlog' => $config->backlog,
                'max_request' => $config->max_request,
                'dispatch_mode' => $config->dispach_mode,
                'daemonize' => $config->daemonize,
                // 自定义协议支持
                'package_max_length' => $config->package_max_length,
                'package_length_type' => $config->package_length_type,
                'package_length_offset' => $config->package_length_offset,
                'package_body_offset' => $config->package_body_offset,
//                 'open_length_check' => true,
//                 'open_eof_check' => true,
//                 'open_eof_split' => true,
//                 'package_eof' => "\r\n\r\n"
            ]);
            // 初始化日志模块
            self::$instance->log = new Log($config->log, $config->debug_mode);
            // 初始化服务端客户端链接
            self::$instance->connection = self::$instance->initConnection();
            // 初始化数据库
            self::$instance->initDb();
            // 初始化业务代码
            $codePath = $config->path ? $config->path : Path::format(__DIR__. '/../../../');
            \Autoload::instance()->setIncludePath($codePath);
        }
        return self::$instance;
    }
    
    /**
     * 初始化数据库
     */
    abstract protected function initDb();
    
    /**
     * 初始化与客户端的链接对象
     * @return Connection
     */
    abstract protected function initConnection(): Connection;
    
    /**
     * 初始化默认事件
     */
    abstract protected function initEvent() :EventVector;
    
    /**
     * 加载事件容器中的事件
     * @param EventVector $events 事件容器
     */
    protected function loadEvent(EventVector $events) 
    {
        foreach ($events as $event) {
            $this->server->on($event->getEventName(), $event->getCallback());
        }
    }
    
    /**
     * 设置
     * @param array $settings swoole_server运行时的各项参数
     */
    protected function set(array $settings) :Server 
    {
        $this->server->set($settings);
        return $this;
    }
    
    /**
     * 设置回调方法
     * @param string $event 回调名称，大小写不敏感，时间名称字符串不要加on
     * @param callable $callback 函数名的字符串，类静态方法，对象方法数组，匿名函数
     */
    protected function on(string $event, callable $callback) :Server 
    {
        $this->server->on($event, $callback);
        return $this;
    }
    
    /**
     * 使当前worker进程停止运行，并立即触发onWorkerStop回调函数。
     * @param int $workerId
     * @param bool $waitEvent
     * @return bool
     */
    protected function stop(int $workerId = -1, bool $waitEvent = false): bool 
    {
        return $this->server->stop($workerId, $waitEvent);
    }
    
    /**
     * tick定时器，可以自定义回调函数
     * @param int $time 定时时长
     * @param callable $callback 回调函数
     */
    protected function trick(int $time, callable $callback) 
    {
        $this->server->trick($time, $callback);     
    }
    
    /**
     * 向客户端发送数据
     * @param int $fd 套接字文件描述符
     * @param string $data 发送数据（tcp 最大不能超过2M）
     * @param int $extraData 
     * @return bool
     */
    protected function send(int $fd, string $data, int $extraData = 0) :bool
    {
        $this->server->send($fd, $data, $extraData);
    }
    
    /**
     * 发送文件到TCP客户端连接
     * @param int $fd
     * @param string $filename 要发送的文件路径，如果文件不存在会返回false
     * @param int $offset 指定文件偏移量，可以从文件的某个位置起发送数据。默认为0，表示从文件头部开始发送
     * @param int $length 指定发送的长度，默认为文件尺寸
     * @return bool
     */
    protected function sendfile(int $fd, string $filename, int $offset =0, int $length = 0) :bool
    {
        return $this->server->sendfile($fd, $filename, $offset, $length);
    }

    /**
     * 向任意的客户端IP:PORT发送UDP数据包
     * @param string $ip 为IPv4字符串，如192.168.1.102。如果IP不合法会返回错误
     * @param int $port 为 1-65535的网络端口号，如果端口错误发送会失败
     * @param string $data 要发送的数据内容，可以是文本或者二进制内容
     * @param int $server_socket 服务器可能会同时监听多个UDP端口，此参数可以指定使用哪个端口发送数据包
     */
    protected function sendto(string $ip, int $port, string $data, int $server_socket = -1) 
    {
        return $this->server->sendto($ip, $port, $data, $server_socket);
    }
    
    /**
     * 阻塞地向客户端发送数据
     * sendwait目前仅可用于SWOOLE_BASE模式
     * @param int $fd 套接字文件描述符
     * @param string $sendData 发送数据
     */
    protected function sendwait(int $fd, string $sendData) 
    {
        return $this->server->sendwait($fd, $sendData);   
    }
    
    /**
     * 向任意worker进程或者task进程发送消息 
     * @param string $message 为发送的消息数据内容，没有长度限制，但超过8K时会启动内存临时文件
     * @param int $dst_worker_id 目标进程的ID，范围是0 ~ (worker_num + task_worker_num - 1)
     */
    protected function sendMessage(string $message, int $dst_worker_id) 
    {
        return $this->server->sendMessage($message, $dst_worker_id);
    }
    
    /**
     * 关闭客户端连接
     * @param int $fd
     * @param bool $reset
     */
    public function close(int $fd, bool $reset = false) :bool
    {
        return $this->server->close($fd, $reset);
    }
    
    /**
     * 检测fd对应的连接是否存在
     * @param int $fd
     * @return bool
     */
    public function exist(int $fd) :bool 
    {
        return $this->server->exist($fd);    
    }
    
    /**
     * 函数用来获取连接的信息
     * @param int $fd
     * @param int $extraData
     * @param bool $ignoreError
     * @return array
     */
    public function getClientInfo(int $fd, int $extraData, bool $ignoreError = false) :array 
    {
        return $this->server->getClientInfo($fd, $extraData, $ignoreError);
    }
    
    /**
     * 用来遍历当前Server所有的客户端连接
     * @param int $startFd
     * @param int $pagesize
     * @return array
     */
    public function getClientList(int $startFd = 0, int $pagesize = 10) :array 
    {
        return $this->server->getClientList($startFd, $pagesize);
    }
    
    /**
     * 得到当前Server的活动TCP连接数，启动时间，accpet/close的总次数等信息
     * @return array
     */
    public function stats() :array 
    {
        return $this->server->stats();
    }
    
    /**
     * 重启服务进程
     * @param $onlyReloadTaskworker 是否仅重启task进程
     * @return Server
     */
    public function reload(bool $onlyReloadTaskworker = false) :bool
    {
        if ( $this->server->reload($onlyReloadTaskworker) ) {
            $this->log->info('Server reload success.');
            return true;
        } else {
            $this->log->warning('Server reload fail.');
            return false;
        }
    }
    
    /**
     * 关闭服务器,此函数可以用在worker进程内
     */
    public function shutdown() 
    {
        $this->server->shutdown();
        $this->log->info('Server has been shutdown.');
    }
    
    /**
     * 启动 Server 服务
     */
    public function run() :bool
    {
        // 加载服务端响应事件
        $events = $this->initEvent();
        $this->loadEvent($events);
        $this->log->info('Server start run.');
        return $this->server->start();
    }
}