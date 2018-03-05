<?php
/**
 * Swoole Server
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午4:11:42
 */
namespace Server;

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
     * 执行当前 server
     */
    public function run() :bool
    {
        // 链接接入响应
        $this->server->on('connect', function ($serv, $fd){
            echo "Client:Connect.\n";
        });
        // 接收客户端数据响应
        $this->server->on('receive', function ($serv, $fd, $fromId, $data) {
            $this->server->send($fd, 'Swoole: '. $data);
            $this->server->close($fd);
        });
        // 链接关闭时响应
        $this->server->on('close', function ($serv, $fd) {
            echo "Client: Close.\n";
        });
        return parent::run();
    }
}