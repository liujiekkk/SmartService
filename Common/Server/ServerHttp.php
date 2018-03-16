<?php
/**
 * Http 服务
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:43:13
 */
namespace Common\Server;
use Common\Server\Event\EventVector;
use Common\Server\Event\Event;
class ServerHttp extends Server {
    
    /**
     * 实例化 http server 对象
     * @param string $host 主机地址
     * @param int $port 端口号
     * @param array $settings 额外配置项
     * @return Server
     */
    public static function instance(string $host, int $port, array $settings=[]) :Server
    {
        if ( !self::$instance ) {
            self::$instance = new static();
            // 实例化 swoole server
            self::$instance->server = new \swoole_http_server($host, $port);
            // 加载额外配置项
            self::$instance->server->set($settings);
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