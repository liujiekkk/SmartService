<?php
/**
 * 客户端启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:51:55
 */

include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();

// 实例化服务
$client = new \Common\Client\ClientTcp();

$connection = new Common\Connection\Rpc\RpcConnection();
$connection->setRequest(new Common\Connection\Rpc\RpcRequest());
// 系统命令
// $connection->setHeader('type', 'system');
// $connection->setHeader('host', '127.0.0.1');
// $connection->setHeader('port', '9999');
// $connection->setData(['class'=>'test', 'method'=>'reload', 'params'=>[rand(0,1000),'b']]);
// 业务代码
$connection->setHeader('type', 'user');
$connection->setHeader('host', '127.0.0.1');
$connection->setHeader('port', '9999');
$connection->setData(['class'=>'test', 'method'=>'getParam', 'params'=>[rand(0,1000),'b']]);
$client->setConnection($connection);
$client->access();
$res = $client->getConnection()->getResponse();
var_dump($res->getData());
