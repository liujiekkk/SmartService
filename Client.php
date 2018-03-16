<?php

/**
 * 客户端启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:51:55
 */

include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();
// 调用
$call = \Common\Action\RpcCall::instance();
$call->setService('service');
$call->setClass('Test');
$call->setMethod('t');
$call->setParams(['b'=>'这是我的执行结果']);
// 协议
$protocol = \Common\Protocol\JsonRpc::instance();
$protocol->setAction($call);
// 请求
$request = \Common\Request\RpcRequest::instance();
$request->setHeaders(['head']);
$request->setProtocol($protocol);
// 实例化服务，并且运行
$client = \Common\Client\ClientTcp::instance(Config\Main::HOST, Config\Main::PORT);
$client->setRequest($request);
$client->connect();
