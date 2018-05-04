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
$params = getopt('s:n:');
if ( !isset($params['s']) ) {
    echo 'No signal input!'.PHP_EOL;
    exit;
}
$signals = ['start', 'stop', 'reload', 'restart'];
if ( !in_array($params['s'], $signals)) {
    echo 'Error signal.'.PHP_EOL;
    exit;
}
if ( !isset($params['n']) ) {
    echo 'No server name input!'.PHP_EOL;
    exit;
}
$manager = new Common\Server\ServerManager();
$manager->{$params['s']}($params['n']);

