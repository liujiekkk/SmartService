<?php
/**
 * Server 配置文件
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:12:05
 */
namespace Config;

class Main {
    
    /**
     * 参数用来指定监听的ip地址，如127.0.0.1，或者外网地址，或者0.0.0.0监听全部地址
     * @var string
     */
    const HOST = '127.0.0.1';
    
    /**
     * 监听的端口，如 9999
     * 监听小于1024端口需要root权限
     * @var int
     */
    const PORT = 9999;
    
    /**
     * worker 数量
     * @var int
     */
    const WORKER_NUM = 1;
    
    /**
     * reactor 线程数量
     * @var int
     */
    const REACTOR_NUM = 4;
    
    /**
     * task worker 数量
     * @var int
     */
    const TASK_WORKER_NUM = 0;
    
    /**
     * listen backlog
     * @var int
     */
    const BACKLOG = 128;
    
    /**
     * 最大并发请求数量
     * @var int
     */
    const MAX_REQUEST = 50;
    
    /**
     * 分发模式
     * @var int
     */
    const DISPACH_MODE = 1;
    
    /**
     * 是否以守护进程方式运行
     * @var int
     */
    const DAEMONIZE = 0;
    
    /**
     * 数据签名秘钥
     * @var string
     */
    const PROTOCOL_SIGN_SECRET = 'ABCDEFG';
    
    /**
     * 协议数据解析器
     * @var string
     */
    const PROTOCOL_DATA_PARSER = 'Server\Parser\JsonRpc';
    
}