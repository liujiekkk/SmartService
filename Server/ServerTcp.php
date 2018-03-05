<?php
/**
 * Swoole Server
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午4:11:42
 */
namespace Server;

use Config\Swoole;
use Server\Event\EventVector;
use Server\Event\Event;

class ServerTcp extends Server {
    
    /**
     * 初始化 Server
     * @param string $host 指定监听的IP地址
     * @param int $port 监听端口号
     */
    public static function instance(string $host, int $port) :Server
    {
        if ( !self::$instance ) {
            self::$instance = new static();
            // 多进程模式：SWOOLE_PROCESS 基础模式：SWOOLE_BASE
            self::$instance->server = new \swoole_server($host, $port, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        }
        return self::$instance;
    }
    
    /**
     * 初始化默认监听事件
     * {@inheritDoc}
     * @see \Server\Server::initDefaultEvent()
     */
    protected function initEvent() :EventVector
    {
        $events = new EventVector();
        $events->addEvent(new Event('connect', [$this, 'onConnect']));
        $events->addEvent(new Event('receive', [$this, 'onReceive']));
        $events->addEvent(new Event('close', [$this, 'onClose']));
        return $events;
    }
    
    /**
     * 客户端连接相应函数
     * @param \Swoole\Server $serv 服务端实例
     * @param int $fd 连接文件描述符
     */
    public function onConnect($serv, $fd)
    {
        echo "Client:Connect.\n";
    }
    
    public function onReceive($serv, $fd, $fromId, $data) 
    {
        $serv->send($fd, 'Swoole: '.$fd. $data);
        $serv->close($fd);
    }
    
    public function onClose($serv, $fd) 
    {
        echo "Client: Close.\n";
    }
}