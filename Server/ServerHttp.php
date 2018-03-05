<?php
/**
 * Http 服务
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:43:13
 */
namespace Server;
use Server\Event\EventVector;
use Server\Event\Event;
class ServerHttp extends Server {
    
    /**
     * 实例化 http server
     * @param string $host
     * @param int $port
     */
    public static function instance(string $host, int $port) :Server
    {
        if ( !self::$instance ) {
            self::$instance = new static();
            self::$instance->server = new \swoole_http_server($host, $port);
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
        $events->addEvent(new Event('request', [$this, 'onRequest']));
        return $events;
    }
    
    public function onRequest($request, $response) 
    {
        $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
    }
}