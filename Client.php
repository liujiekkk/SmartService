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
$config = new \Conf\Client\Example();
$client = new \Common\Client\ClientRpc($config);
$data = $client->request('test\Test', 'say', ['hello']);
var_dump($data);
