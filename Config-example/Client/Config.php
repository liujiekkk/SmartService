<?php
/**
 * 客户端配置文件
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月3日
 * @time 上午10:19:14
 */
namespace Config\Client;

class Config
{
    
    /**
     * 是否开启debug 模式
     * @var bool
     */
    public $debug_mode = true;
    
    /**
     * 服务端日志存储路径
     * @var string
     */
    public $log  = '/tmp/smart-client.log';
    
    /**
     * soket 类型
     * @var int
     */
    public $sock_type = SWOOLE_TCP;
    
    /**
     * 是否是异步客户端
     * SWOOLE_SOCK_SYNC SWOOLE_SOCK_ASYNC
     * @var int
     */
    public $async = SWOOLE_SOCK_SYNC;
    
    /**
     * 用于长连接的Key，默认使用IP:PORT作为key。
     * 相同key的连接会被复用
     * @var string
     */
    public $key = '';
}

