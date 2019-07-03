<?php
/**
 * Server 配置文件
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:12:05
 */
namespace Server\Config;

abstract class ServerConfig
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
    public $log  = '/tmp/smart-server.log';
    
    /**
     * 参数用来指定监听的ip地址，如127.0.0.1，或者外网地址，或者0.0.0.0监听全部地址
     * @var string
     */
    public $host = '127.0.0.1';
    
    /**
     * 监听的端口，如 9999
     * 监听小于1024端口需要root权限
     * @var int
     */
    public $port = 9999;
    
    /**
     * 最大包长度
     * @var integer
     */
    public $package_max_length = 81920;
    
    /**
     * 包头类型
     * @var string
     */
    public $package_length_type = 'N';
    
    /**
     * 包长度所在包头位置
     * @var integer
     */
    public $package_length_offset = 8;
    
    /**
     * 包体位置
     * @var integer
     */
    public $package_body_offset = 16;
    
    /**
     * 服务管理器
     * @var string
     */
    public $manager = '\\Manager\\RpcManager';
    
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
     * 获取服务名称
     * @return string
     */
    public function getName(): string
    {
        $classPath = get_called_class();
        $pos = strrpos($classPath, '\\');
        if ( $pos ) {
            return substr($classPath, $pos+1);
        } else {
            return $classPath;
        }
    }
}