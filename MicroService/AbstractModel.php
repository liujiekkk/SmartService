<?php
/**
 * 抽象 Model 层基类
 * @author liujie <king.2oo8@163.com>
 * @date 2019年7月3日
 * @time 下午4:38:45
 */
namespace MicroService;

use Library\Singleton;
use Db\Mysql;

abstract class AbstractModel
{
    use Singleton;
    
    public function getDataBase(): Mysql
    {
        return Mysql::instance();
    }
}

