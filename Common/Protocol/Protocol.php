<?php
/**
 * 抽象协议类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月9日
 * @time 上午10:41:43
 */
namespace Common\Protocol;

use Library\Singleton;

abstract class Protocol
{
    use Singleton;
    
    abstract public function encode(): string; 
    
    abstract public function decode(string $str);
}

