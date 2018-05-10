<?php

/**
 * 系统调用处理器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月18日
 * @time 下午2:00:34
 */
namespace Common\Server\Action;

use Common\Server\Server;
class SystemAction extends Action
{
    
    public function execute(Server $server, array $params): array
    {
        // 系统类
        $className = get_class($server);
        $reflectionClass = new \ReflectionClass($className);
        $reflectionMethod = $reflectionClass->getMethod($params['method']);
        $data = $reflectionMethod->invokeArgs($server, $params['params']);
        return [$data];
    }
}

