<?php
use Common\Server\ServerTcp;
use Config\Main;

/**
 * 服务启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:05:32
 */
include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();

// 额外配置
$settings = [
    'worker_num' => Main::WORKER_NUM,
    'reactor_num' => Main::REACTOR_NUM, //reactor thread num
    'backlog' => Main::BACKLOG,   //listen backlog
    'max_request' => Main::MAX_REQUEST,
    'dispatch_mode' => Main::DISPACH_MODE,
    'daemonize' => Main::DAEMONIZE
];
// 实例化服务，并且运行
ServerTcp::instance(Config\Main::HOST, Config\Main::PORT, $settings)->run();
// ServerHttp::instance(Config\Main::HOST, Config\Main::PORT, $settings)->run();