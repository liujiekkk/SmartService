<?php
use Server\Parser\Call;

/**
 * 客户端启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:51:55
 */

include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();

$call = new Call('service', 'Test', 't', ['b'=>2]);
// 实例化服务，并且运行
$client = \Client\ClientTcp::instance(Config\Main::HOST, Config\Main::PORT);
$request = Request\Request::instance();
$request->setData($call, 'client1');
$client->setRequest($request);
$client->connect();
