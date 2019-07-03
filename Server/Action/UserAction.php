<?php
/**
 * 
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月18日
 * @time 下午4:47:58
 */
namespace Server\Action;

use Server\Server;

class UserAction extends Action
{

    public function execute(Server $server, array $params): array
    {
        // 业务类位置
        $className = '\\'.ucfirst($params['class']);
        $reflectionClass = new \ReflectionClass($className);
        $instance = $reflectionClass->newInstanceWithoutConstructor();
        $reflectionMethod = $reflectionClass->getMethod($params['method']);
        $data = $reflectionMethod->invokeArgs($instance, $params['params']);
        return $data;
    }
}

