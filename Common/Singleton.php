<?php
/**
 * 单例方法集
 * @author liujie <king.2oo8@163.com>
 * @date 2017年8月9日
 * @time 下午2:12:41
 */
namespace Common;

trait Singleton {
    
    /**
     * 单例模式，保存对象实例
     * @var SingletonSite
     */
    protected static $classVector;
    
    /**
     * 获取对象实例
     */
    public static function instance() {
        $className = get_called_class();
        if (!isset(self::$classVector[$className])) {
            self::$classVector[$className] = new $className();
        }
        return self::$classVector[$className];
    }
    
    /**
     * 私有化构造函数
     */
    protected function __construct() {}
    
    /**
     * 私有化对象复制函数
     */
    protected function __clone() {}
}