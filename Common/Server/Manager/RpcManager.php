<?php
/**
 * Rpc 服务管理器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月9日
 * @time 上午10:14:12
 */
namespace Common\Server\Manager;
class RpcManager extends Manager
{

    /**
     * 启动所有服务
     */
    public function start(string $serverName): bool
    {
        $config = $this->getServerConfig($serverName);
        // 实例化服务，并且运行
        return \Common\Server\ServerRpc::instance($config)->run();
    }
    
    /**
     * 停止所有服务
     */
    public function stop(string $serverName): bool
    {
        $config = $this->getClientConfig($serverName);
        $client = new \Common\Client\ClientRpc($config);
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
    public function reload(string $serverName): bool
    {
        $config = $this->getClientConfig($serverName);
        $client = new \Common\Client\ClientRpc($config);
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
    public function restart(string $serverName): bool
    {
        if ( !$this->stop($serverName) ) {
            echo 'stop server '.$serverName.PHP_EOL;
        }
        usleep(1000);
        return $this->start($serverName);
    }
}

