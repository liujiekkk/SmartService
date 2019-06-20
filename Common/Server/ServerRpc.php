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
use Common\Connection\Connection;
use Common\IO\Db\Mysql;

class ServerRpc extends Server {
    
    /**
     * 初始化各种数据库单例
     * {@inheritDoc}
     * @see \Common\Server\Server::initDb()
     */
    protected function initDb() 
    {
        if ( $this->config->mysql ) {
            // 初始化 mysql
            $db = Mysql::instance($this->config->mysql);
        }
    }
    
    /**
     * 
     * @return Connection
     */
    protected function initConnection(): Connection 
    {
        return new RpcConnection();
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
        $serverName = $this->config->getName();
        // 设置 master 进程名称
        if ( !cli_set_process_title("[php] Smart-{$serverName} master") ) {
            $this->log->warning('Can not set process title');
            return;
        }
    }
    
    /**
     * manager 进程启动调用
     */
    public function onManagerStart()
    {
        $serverName = $this->config->getName();
        // 设置 manager 进程名称
        if ( !cli_set_process_title("[php] Smart-{$serverName} manager") ) {
            $this->log->warning('Can not set process title');
            return;
        }
    }
    
    /**
     * worker 启动调用
     */
    public function onWorkerStart($serv, $workerId)
    {
        $serverName = $this->config->getName();
        // 设置 worker 进程名称
        if ( !cli_set_process_title("[php] Smart-{$serverName} worker") ) {
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
        // 解析协议包
        $header = unpack("Ntype/Nuid/Nlen/Nserid" , $data);
        // 包长度
        $length = $header['len'];
        $msg = substr($data, $this->config->package_body_offset, $length);

        $buffer = new StringBuffer();
        $buffer->writeTo($msg);
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
            $errMsg = $this->log->format($t);
            $this->log->error($errMsg);
            // 创建响应
            $this->connection->setResponse(new RpcResponse());
            $this->connection->setHeader('code', 100000000);
            $this->connection->setHeader('message', '服务器异常');
            $this->connection->setHeader('error', $errMsg);
            $this->connection->setData([]);
            $this->connection->writeBuffer($buffer);
            $len = strlen($buffer->read());
            $msg = pack('N4', 0, 0, $len, 0) . $buffer->read();
            $serv->send($fd, $msg);
            // 关闭客户端链接
            $this->close($fd);
            return;
        }
        // 创建响应
        $this->connection->setResponse(new RpcResponse());
        $this->connection->setHeader('code', 0);
        $this->connection->setHeader('message', '成功');
        $this->connection->setHeader('error', '');
        $this->connection->setData($result);
        $this->connection->writeBuffer($buffer);
        // 将处理结果返回给客户端
        $len = strlen($buffer->read());
        $msg = pack('N4', 0, 0, $len, 0) . $buffer->read();
        $serv->send($fd, $msg);
    }
    
    /**
     * 关闭客户端连接响应函数
     */
    public function onClose($serv, $fd) 
    {
        $this->log->info("Client: Close.");
    }
}