<?php
use Common\Action\RpcCall;
use Common\Protocol\JsonRpc;

/**
 * 客户端启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:51:55
 */

include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();

$call = new RpcCall('service', 'Test', 't', ['b'=>2]);
$jsonRpc = new JsonRpc($call);
$request = new \Common\Request\RpcRequest(['head'], $jsonRpc);
// 实例化服务，并且运行
$client = \Common\Client\ClientTcp::instance(Config\Main::HOST, Config\Main::PORT);
$client->setRequest($request);
$client->connect();
