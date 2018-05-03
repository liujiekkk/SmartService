<?php
/**
 * 服务启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:05:32
 */
include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();

// 获取服务名称
$params = getopt('n:');
if ( !isset($params['n']) ) {
    echo 'No server name input!'.PHP_EOL;
    exit;
}
try {
    $configClass = '\\Conf\\Server\\Serv\\'.ucfirst($params['n']);
    // 实例化配置对象
    $config = new $configClass();
    // 实例化服务，并且运行
    \Common\Server\ServerTcp::instance($config)->run();
} catch (Throwable $t) {
    echo $t->getMessage().PHP_EOL;
}