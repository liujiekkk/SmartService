<?php
/**
 * 用户操作抽象类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月9日
 * @time 上午11:04:51
 */
namespace Common\Server\Action;

use Library\Singleton;
use Common\Server\Server;

abstract class Action
{
    use Singleton;
    
    /**
     * 操作接口
     */
    abstract public function execute(Server $server, array $params): array;
    
}

