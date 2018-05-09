<?php
/**
 * 配置文件中的通用配置
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月9日
 * @time 下午1:22:29
 */
namespace Common\Config;

abstract class AbstractConfig
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

