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
        // 系统命令
        $data = $client->request('', 'shutdown', [], 'system');
        if ($data->getCode()) {
            return false;
        }
        return true;
    }
    
    /**
     * 平滑重启所有服务（不停机）
     */
    public function reload(): bool
    {
        $client = new \Common\Client\ClientRpc($this->clientConfig);
        $data = $client->request('', 'reload', [], 'system');
        if ($data->getCode()) {
            return false;
        }
        return true;
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

