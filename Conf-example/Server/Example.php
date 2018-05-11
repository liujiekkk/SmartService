<?php
/**
 * 服务端配置实例
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月3日
 * @time 下午1:22:40
 */
namespace Conf\Server;

use Common\Config\ServerConfig;

class Example extends ServerConfig
{
    /**
     * 是否开启debug 模式
     * @var bool
     */
    public $debug_mode = false;
    
    /**
     * 服务端日志存储路径
     * @var string
     */
    public $log  = '/tmp/smart-example.log';
    
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
     * 业务代码路径
     * @var string
     */
    public $path = '/data/projects/service_test';
    
    /**
     * 是否以守护进程方式运行
     * @var int
     */
    public $daemonize = 1;
    
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

