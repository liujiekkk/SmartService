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

$call = new Call('service', 'class_', 'method_', ['a'=>1, 'b'=>2], 'client1', '148eb2b953fcfb048791637d9b61852d');
// 实例化服务，并且运行
$client = (new \Client\ClientTcp());
$request = Request\Request::instance();
$request->setData($call);
$client->setRequest($request);
$client->connect(Config\Main::HOST, Config\Main::PORT);
