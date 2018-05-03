<?php
/**
 * 服务启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:05:32
 */
include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();

// 额外配置
$config = new \Conf\Server\Config();
// 实例化服务，并且运行
\Common\Server\ServerTcp::instance($config)->run();
// ServerHttp::instance(Config\Main::HOST, Config\Main::PORT, $settings)->run();