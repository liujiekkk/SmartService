<?php
/**
 * Swoole Server
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午4:11:42
 */
namespace Common\Server;

use Common\Server\Event\EventVector;
use Common\Server\Event\Event;
use Common\IO\StringBuffer;
use Common\Connection\Rpc\RpcConnection;
use Common\Connection\Rpc\RpcRequest;
use Common\Connection\Rpc\RpcResponse;
use Common\Log\Log;
use Config\Main;

class ServerTcp extends Server {
    
    /**
     * 日志模块
     * @var Log
     */
    protected $log;
    
    /**
     * 初始化 Server
     * @param string $host 指定监听的IP地址
     * @param int $port 监听端口号
     * @param array $settings 额外配置项
     */
    public static function instance(string $host, int $port, array $settings=[]) :Server
    {
        if ( !self::$instance ) {
            
            self::$instance = new static();
            // 多进程模式：SWOOLE_PROCESS 基础模式：SWOOLE_BASE
            self::$instance->server = new \swoole_server($host, $port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
            self::$instance->server->set($settings);
            // 初始化服务端客户端链接
            self::$instance->connection = new RpcConnection();
            // 初始化日志模块
            self::$instance->log = new Log(Main::SERVER_LOG_PATH, Main::DEBUG_MODE);
        }
        return self::$instance;
    }
    
    /**
     * 初始化默认监听事件
     * {@inheritDoc}
     * @see \Server\Server::initEvent()
     */
    protected function initEvent() :EventVector
    {
        $events = new EventVector();
        $events->addEvent(new Event('Start', [$this, 'onStart']));
        $events->addEvent(new Event('ManagerStart', [$this, 'onManagerStart']));
        $events->addEvent(new Event('WorkerStart', [$this, 'onWorkerStart']));
        $events->addEvent(new Event('Connect', [$this, 'onConnect']));
        $events->addEvent(new Event('Receive', [$this, 'onReceive']));
        $events->addEvent(new Event('Close', [$this, 'onClose']));
        return $events;
    }
    
    /**
     * 主进程启动调用
     */
    public function onStart() 
    {
        // 设置 master 进程名称
        if ( !cli_set_process_title('Swoole master') ) {
            $this->log->warning('Can not set process title');
            return;
        }
    }
    
    /**
     * manager 进程启动调用
     */
    public function onManagerStart()
    {
        // 设置 manager 进程名称
        if ( !cli_set_process_title('Swoole manager') ) {
            $this->log->warning('Can not set process title');
            return;
        }
    }
    
    /**
     * worker 启动调用
     */
    public function onWorkerStart($serv, $workerId)
    {
        // 设置 worker 进程名称
        if ( !cli_set_process_title('Swoole worker') ) {
            $this->log->warning('Can not set process title');
            return;
        }
    }
    
    /**
     * 客户端连接相应函数
     */
    public function onConnect($serv, $fd)
    {
        $this->log->info("Hello Client {$fd}");
    }
    
    /**
     * 收到客户端数据响应函数
     */
    public function onReceive($serv, $fd, $fromId, $data) 
    {
        $buffer = new StringBuffer();
        $buffer->writeTo($data);
        $this->connection->setRequest(new \Common\Connection\Rpc\RpcRequest());
        $this->connection->readBuffer($buffer);
        
        
        $method = $this->connection->getHeader('type');
        $params = $this->connection->getData();
        
        // 处理调用
        $result = [];
        try {
            $class = ('\\Common\\Server\\Action\\'.ucfirst($method).'Action')::instance();
            $result = $class->execute($this, $params);
        } catch (\Throwable $t) {
            // 写入错误日志
            $this->log->error($t->getMessage());
            // 创建响应
            $this->connection->setResponse(new RpcResponse());
            $this->connection->setHeader('code', 100000000);
            $this->connection->setHeader('error', '');
            $this->connection->setHeader('message', '服务器异常');
            $this->connection->setHeader('data', $t->getMessage());
            $this->connection->setData([]);
            $this->connection->writeBuffer($buffer);
            $serv->send($fd, $buffer->read());
            // 关闭客户端链接
            $this->close($fd);
            return;
        }
        // 创建响应
        $this->connection->setResponse(new RpcResponse());
        $this->connection->setHeader('code', 0);
        $this->connection->setHeader('error', '');
        $this->connection->setHeader('message', '成功');
        $this->connection->setHeader('data', '');
        $this->connection->setData($result);
        $this->connection->writeBuffer($buffer);
        // 将处理结果返回给客户端
        $serv->send($fd, $buffer->read());
    }
    
    /**
     * 关闭客户端连接响应函数
     */
    public function onClose($serv, $fd) 
    {
        $this->log->info("Client: Close.");
    }
}