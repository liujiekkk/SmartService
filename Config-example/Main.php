<?php
/**
 * Server 配置文件
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午3:12:05
 */
namespace Config;

abstract class Main {
    
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
    
}