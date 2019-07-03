<?php
/**
 * 请求分发器
 * @author liujie <king.2oo8@163.com>
 * @date 2019年7月2日
 * @time 下午5:12:45
 */
namespace Server;

use Common\Protocol\AbstractRequest;
use Common\Protocol\AbstractResponse;
use Library\Singleton;
use Common\Protocol\JsonRpc\JsonResponse;
use Common\Log\Log;

final class Dispatcher
{
    use Singleton;
    
    protected $request;
    
    /**
     * Server 实例
     * @var Server
     */
    protected $server;
    
    /**
     * 日志模块实例
     * @var Log
     */
    protected $logger;
    
    public function setServer(Server $server): self 
    {
        $this->server = $server;
        return $this;
    }
    
    public function setLog(Log $logger): self 
    {
        $this->logger = $logger;
        return $this;
    }
    
    public function dispatch(AbstractRequest $request): AbstractResponse 
    {
        // 调用相应的系统方法还是用户业务逻辑
        $action = $request->getAction();
        $params = [
            'class' => $request->getClass(),
            'method' => $request->getMethod(),
            'params' => $request->getParams()
        ];
        // 处理调用
        $result = [];
        try {
            // 单例模式，每个 worker 进程中只实例化一次
            $class = ('\\Server\\Action\\'.ucfirst($action).'Action')::instance();
            $result = $class->execute($this->server, $params);
            return JsonResponse::instance()
                ->setId($request->getId())
                ->setCode(0)
                ->setMessage('成功')
                ->setData($result);
        } catch (\Throwable $t) {
            $msg = $this->logger->format($t);
            $this->logger->error($msg);
            return JsonResponse::instance()
                ->setId($request->getId())
                ->setCode(100000000)
                ->setMessage($t->getMessage())
                ->setData([]);
        }
    }
}

