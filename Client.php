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

// 协议
$protocol = \Common\Protocol\JsonRpc::instance();
$protocol->setJsonrpc('2.0');
$protocol->setMethod('User');
$protocol->setId('id');
$protocol->setParams(['class'=>'Test', 'method'=>'t', 'params'=>['b'=>'cc']]);

// 请求
$request = \Common\Request\RpcRequest::instance();
$request->setHeaders(['server'=>'rpcTest','host'=>'127.0.0.1','port'=>'9999']);
$request->setProtocol($protocol);
$request->send($client);
