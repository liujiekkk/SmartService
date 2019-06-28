<?php
/**
 * 服务管理器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月4日
 * @time 上午9:29:29
 */
namespace Manager;

use Server\Config\ServerConfig;
use Client\Config\ClientConfig;

abstract class Manager
{
    protected $clientConfig;
    
    protected $serverConfig;
    
    public function __construct(ServerConfig $serverConfig, ClientConfig $clientConfig) 
    {
        $this->clientConfig = $clientConfig;
        $this->serverConfig = $serverConfig;
    }
    
    /**
     * 启动所有服务
     */
    abstract public function start(): bool;
    
    /**
     * 停止所有服务
     */
    abstract public function stop(): bool;
    
    /**
     * 平滑重启所有服务（不停机）
     */
    abstract public function reload(): bool;
    
    /**
     * 重启所有服务
     * @param string $serverName 服务名称
     */
    abstract public function restart(): bool;
}

