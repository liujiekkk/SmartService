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
use Common\Request\RpcRequest;
use Common\IO\StringBuffer;
use Common\Protocol\SerilizeUtil;
use Common\Protocol\JsonRpc;

class ServerTcp extends Server {
    
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
            echo 'Can not set process title';
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
            echo 'Can not set process title';
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
            echo 'Can not set process title';
            return;
        }
    }
    
    /**
     * 客户端连接相应函数
     */
    public function onConnect($serv, $fd)
    {
        echo "Hello Client {$fd} \n";
    }
    
    /**
     * 收到客户端数据响应函数
     */
    public function onReceive($serv, $fd, $fromId, $data) 
    {
        $buffer = new StringBuffer();
        $buffer->writeTo($data);
        $request = RpcRequest::readBuffer($buffer);
        $rpcCall = $request->getProtocol()->getAction();
        // 执行调用
        $rpcCall->execute();
        // 获取执行结果
        $result = $rpcCall->getReturn();
        // @todo 处理相关业务逻辑
        echo 'receive: '.$rpcCall->encode()."\n";
        
        // @todo 将处理结果返回给客户端
        $serv->send($fd, 'Swoole: fd->'.$fd.' execute: '. $rpcCall->getReturn());
    }
    
    /**
     * 关闭客户端连接响应函数
     */
    public function onClose($serv, $fd) 
    {
        echo "Client: Close.\n";
    }
}