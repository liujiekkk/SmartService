<?php
/**
 * 用户操作抽象类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月9日
 * @time 上午11:04:51
 */
namespace Common\Action;

use Library\Singleton;

abstract class Action
{
    use Singleton;
    
    /**
     * 操作接口
     */
    abstract public function execute();
    
    /**
     * 获取操作结果
     */
    abstract public function getReturn();
    
    /**
     * 将当前对象转换成字符串
     * @return string
     */
    abstract public function encode(): string;
    
    abstract public function decode(string $str);
}

