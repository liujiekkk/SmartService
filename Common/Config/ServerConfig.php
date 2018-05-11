<?php
/**
 * Server 配置文件
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:12:05
 */
namespace Common\Config;

abstract class ServerConfig extends AbstractConfig 
{
    
    /**
     * 服务端日志存储路径
     * @var string
     */
    public $log  = '/tmp/smart-server.log';
    
    /**
     * 服务管理器
     * @var string
     */
    public $manager = '\\Common\\Server\\Manager\\RpcManager';
    
    /**
     * 用来存储业务逻辑代码路径
     * @var string
     */
    public $path = '';
    
    public $mode = SWOOLE_PROCESS;
    
    public $sock_type = SWOOLE_SOCK_TCP;
    
    /**
     * worker 数量
     * @var int
     */
    public $worker_num = 1;
    
    /**
     * reactor 线程数量
     * @var int
     */
    public $reactor_num = 4;
    
    /**
     * task worker 数量
     * @var int
     */
    public $task_worker_num = 0;
    
    /**
     * listen backlog
     * @see https://wiki.swoole.com/wiki/page/279.html
     * @var int
     */
    public $backlog = 128;
    
    /**
     * 最大并发请求数量
     * @var int
     */
    public $max_request = 50;
    
    /**
     * 分发模式
     * @var int
     */
    public $dispach_mode = 1;
    
    /**
     * 是否以守护进程方式运行
     * @var int
     */
    public $daemonize = 0;
    
    /**
     * 数据签名秘钥
     * @var string
     */
    public $protocol_signature_key = 'ABCDEFG';
    
    /**
     * mysql 配置
     * @var array
     */
//     public $mysql = [
//         'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=test;charset=utf8mb4',
//         'user' => 'root',
//         'pass' => '',
//         'persistent' => true
//     ];
    
}