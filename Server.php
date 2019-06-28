<?php
/**
 * 服务启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:05:32
 */
use Server\Config\ServerConfig;
use Client\Config\ClientConfig;
use Manager\Manager;

include_once 'Autoload.php';
Autoload::instance()
    ->setIncludePath(__DIR__)
    ->setIncludePath(__DIR__.'/Vendors')
    ->init();

class ManagerFactory 
{
    
    protected static function getServerConfig(string $serverName): ServerConfig
    {
        $configClass = '\\Conf\\Server\\'.ucfirst($serverName);
        // 实例化配置对象
        return new $configClass();
    }

    protected static function getClientConfig(string $serverName): ClientConfig
    {
        $configClass = '\\Conf\\Client\\'.ucfirst($serverName);
        // 实例化配置对象
        return new $configClass();
    }

    public static function instance(string $serverName): Manager
    {
        $serverConfig = self::getServerConfig($serverName);
        $clientConfig = self::getClientConfig($serverName);
        return new $serverConfig->manager($serverConfig, $clientConfig);
    }
}
// 获取服务名称
$params = getopt('s:n:');
if ( !isset($params['s']) ) {
    echo 'No signal input!'.PHP_EOL;
    exit;
}
$signals = ['start', 'stop', 'reload', 'restart'];
if ( !in_array($params['s'], $signals)) {
    echo 'Error signal.'.PHP_EOL;
    exit;
}
if ( !isset($params['n']) ) {
    echo 'No server name input!'.PHP_EOL;
    exit;
}
// 服务控制器启动服务
ManagerFactory::instance($params['n'])->{$params['s']}();


