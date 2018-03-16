<?php
/**
 * 抽象数据缓冲对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月9日
 * @time 上午10:49:48
 */
namespace Common\IO;

abstract class AbstractBuffer
{
    protected $data;
    
    abstract public function writeTo(string $str);
    
    abstract public function read(): string;
}

