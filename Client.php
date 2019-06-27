<?php
use Common\Client\Client;
use Common\Client\ClientRpc;

/**
 * 客户端启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:51:55
 */

include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();

// 测试同步请求
$config = new \Conf\Client\Example();
$client = new \Common\Client\ClientRpc($config);
$data = $client->request('test\Test', 'say', ['hello']);
var_dump($data);

// $data = [];
// // 测试并行请求
// $config = new \Conf\Client\Example();
// for($i = 0; $i < 5; $i++ ) {
//     $client = new \Common\Client\ClientRpc($config);
//     $data[$i] = $client->requestAsync('Test\Test', 'say', ['hello']);
// }
// var_dump($data);
// // 发送异步请求
// ClientRpc::executeAsync();
// var_dump($data);


