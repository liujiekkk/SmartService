<?php
/**
 * Rpc 服务管理器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月9日
 * @time 上午10:14:12
 */
namespace Common\Server\Manager;
use Common\Config\ServerConfig;
use Common\Config\ClientConfig;

class RpcManager extends Manager
{
    /**
     * 启动所有服务
     */
    public function start(): bool
    {
        // 实例化服务，并且运行
        return \Common\Server\ServerRpc::instance($this->serverConfig)->run();
    }
    
    /**
     * 停止所有服务
     */
    public function stop(): bool
    {
        $client = new \Common\Client\ClientRpc($this->clientConfig);
        $connection = new \Common\Connection\Rpc\RpcConnection();
        $connection->setRequest(new \Common\Connection\Rpc\RpcRequest());
        // 系统命令
        $connection->setHeader('type', 'system');
        $connection->setData(['method'=>'shutdown', 'params'=>[]]);
        $client->setConnection($connection);
        return $client->access();
    }
    
    /**
     * 平滑重启所有服务（不停机）
     */
    public function reload(): bool
    {
        $client = new \Common\Client\ClientRpc($this->clientConfig);
        $connection = new \Common\Connection\Rpc\RpcConnection();
        $connection->setRequest(new \Common\Connection\Rpc\RpcRequest());
        // 系统命令
        $connection->setHeader('type', 'system');
        $connection->setData(['method'=>'reload', 'params'=>[]]);
        $client->setConnection($connection);
        return $client->access();
    }
    
    /**
     * 重启所有服务
     * @param string $serverName 服务名称
     */
    public function restart(): bool
    {
        if ( !$this->stop() ) {
            return false;
        }
        usleep(1000);
        return $this->start();
    }
}

