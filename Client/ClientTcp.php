<?php
/**
 * TCP 客户端
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:18:44
 */
namespace Client;

class ClientTcp extends Client 
{
    /**
     * 实例化客户端
     * @param int $is_sync SWOOLE_SOCK_ASYNC 或者 SWOOLE_SOCK_SYNC
     * @param string $key 客户端唯一标识
     */
    public function __construct(int $is_sync = SWOOLE_SOCK_ASYNC, string $key='') {
        $this->client = new \swoole_client(SWOOLE_TCP, $is_sync, $key);
        $this->client->on("connect", function ($cli) {
            $cli->send("hello world. show my info .\n");
        });
        $this->client->on("receive", function ($cli, $data = "") {
            if (empty($data)) {
                echo "closed\n";
            } else {
                echo "received: $data\n";
                $cli->send("hello\n");
            }
        });
        
        $this->client->on("close", function ($cli) {
            echo "close\n";
        });
        $this->client->on("error", function ($cli) {
            exit("error\n");
        });
    }
}