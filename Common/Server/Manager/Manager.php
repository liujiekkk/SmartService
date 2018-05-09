<?php
/**
 * 服务管理器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月4日
 * @time 上午9:29:29
 */
namespace Common\Server\Manager;
use Common\Config\ServerConfig;
use Common\Config\ClientConfig;

abstract class Manager
{
    protected function getServerConfig(string $serverName): ServerConfig 
    {
        $configClass = '\\Conf\\Server\\'.ucfirst($serverName);
        // 实例化配置对象
        return new $configClass();
    }
    
    protected function getClientConfig(string $serverName): ClientConfig 
    {
        $configClass = '\\Conf\\Client\\'.ucfirst($serverName);
        // 实例化配置对象
        return new $configClass();
    }
    
    /**
     * 启动所有服务
     */
    abstract public function start(string $serverName): bool;
    
    /**
     * 停止所有服务
     */
    abstract public function stop(string $serverName): bool;
    
    /**
     * 平滑重启所有服务（不停机）
     */
    abstract public function reload(string $serverName): bool;
    
    /**
     * 重启所有服务
     * @param string $serverName 服务名称
     */
    abstract public function restart(string $serverName): bool;
}

