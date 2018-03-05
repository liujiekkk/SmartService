<?php
/**
 * 自动加载类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午5:08:40
 */

class Autoload
{
    
    /**
     * 私有化构造函数，使生成对象途径唯一
     */
    private function __construct() {}
    
    /**
     * 私有化clone 函数，使生成对象途径唯一
     */
    private function __clone() {}
    
    /**
     * 静态变量存储
     * @var Autoload
     */
    private static $instance;
    
    public $path = array();

    /**
     * 单例实例化对象
     * @return Autoload
     */
    public static function instance()
    {
        if ( empty(self::$instance) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 设置载入路径.
     * @param string $path 文件路径.
     * @return Autoload
     */
    public function setIncludePath(string $path) :Autoload
    {
        if (!isset($this->path[$path])) {
            $this->path[$path] = 1;
        }
        return $this;
    }

    /**
     * 自动加载.
     * @param string $className 加载文件类.
     * @return bool
     */
    public function loadByNamespace(string $className) :bool
    {
        $path =  DIRECTORY_SEPARATOR.ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $className), DIRECTORY_SEPARATOR);
        $sysPath = $this->path;
        foreach( $sysPath as $key => $value ){
            if ( is_file($key . $path . '.php') ) {
                $filePath = $key . $path . '.php';
                require_once $filePath;
            } elseif ( is_file($key . '/Library' . $path . '.php') ) {
                $filePath = $key . '/Library' . $path . '.php';
                require_once $filePath;
            }
        }
        return true;
    }

    /**
     * 初始化类加载方法
     * @return Autoload
     */
    public function init() :Autoload
    {
        // 替换系统自动加载类方法
        spl_autoload_register(array($this, 'loadByNamespace'));
        return $this;
    }
}

