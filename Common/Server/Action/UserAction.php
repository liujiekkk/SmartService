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
        // 业务类位置
        $className = '\\'.$params['class'];
        $classObj = new $className();
        $data = call_user_func_array([$classObj, $params['method']], $params['params']);
        return [$data];
    }
}

