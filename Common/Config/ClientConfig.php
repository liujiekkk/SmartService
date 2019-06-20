<?php
/**
 * 客户端配置文件
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月3日
 * @time 上午10:19:14
 */
namespace Common\Config;

abstract class ClientConfig extends AbstractConfig
{
    
    /**
     * 服务端日志存储路径
     * @var string
     */
    public $log  = '/tmp/smart-client.log';
    
    /**
     * soket 类型
     * @var int
     */
    public $sock_type = SWOOLE_TCP | SWOOLE_KEEP;
    
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
    
    /**
     * 设置超时时间
     * @var float
     */
    public $timeout = 0.6;
    
    /**
     * 关闭 Nagle 算法
     * @var string
     */
    public $open_tcp_nodelay = true;
    
}

