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

final class Dispatcher
{
    use Singleton;
    
    protected $request;
    
    /**
     * Server 实例
     * @var Server
     */
    protected $server;
    
    public function setServer(Server $server): self 
    {
        $this->server = $server;
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
        } catch (\Throwable $t) {
            // 格式化日志输出
            $msg = $this->server->log->format($t);
            // 记录日志
            $this->server->log->error($msg);
            // 响应客户端
            return JsonResponse::instance()
                ->getId($request->getId())
                ->setCode(100000000)
                ->setMessage('服务器异常')
                ->setData([]);
        }
        return JsonResponse::instance()
            ->setId($request->getId())
            ->setCode(0)
            ->setMessage('成功')
            ->setData($result);
    }
}

