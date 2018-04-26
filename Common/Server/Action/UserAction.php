<?php
/**
 * 
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月18日
 * @time 下午4:47:58
 */
namespace Common\Server\Action;

use Common\Server\Server;

class UserAction extends Action
{

    public function execute(Server $server, array $params): array
    {
        try {
            // 业务类位置
            $className = '\\'.ucfirst($params['class']);
            $classObj = new $className();
            $reflectionClass = new \ReflectionClass($className);
            $reflectionMethod = $reflectionClass->getMethod($params['method']);
            $data = $reflectionMethod->invokeArgs($classObj, $params['params']);
            return [$data];
        } catch (\Throwable $t) {
            throw new \Exception(__METHOD__.' '. $t->getMessage());
        }
    }
}

