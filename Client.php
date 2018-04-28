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
$client = \Common\Client\ClientTcp::instance();

$connection = new Common\Connection\Rpc\RpcConnection();
$connection->setRequest(new Common\Connection\Rpc\RpcRequest());
$connection->setHeader('method', 'user');
$connection->setHeader('host', '127.0.0.1');
$connection->setHeader('port', '9999');
$connection->setData(['class'=>'test', 'method'=>'t', 'params'=>['a','b']]);
$client->setConnection($connection);
$client->access();
$conn = $client->getConnection();
var_dump($conn->getData());
