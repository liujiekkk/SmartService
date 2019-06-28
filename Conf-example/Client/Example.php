<?php
/**
 * 服务端配置实例
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月3日
 * @time 下午1:22:40
 */
namespace Conf\Client;

use Client\Config\ClientConfig;

class Example extends ClientConfig
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
}

