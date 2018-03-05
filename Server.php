<?php
use Server\ServerTcp;
use Server\ServerHttp;

/**
 * 服务启动入口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:05:32
 */
include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();

// 实例化服务，并且运行
ServerTcp::instance(Config\Main::HOST, Config\Main::PORT)->run();
// ServerHttp::instance(Config\Main::HOST, Config\Main::PORT)->run();