<?php
/**
 * Swoole Server
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午4:11:42
 */
namespace Common\Server;

use Common\Server\Event\EventVector;
use Common\Server\Event\Event;
use Common\Db\Mysql;
use Common\Protocol\FrameReader;
use Common\Protocol\JsonRpc\JsonResponse;
use Common\Protocol\JsonRpc\JsonRequest;
use Common\Protocol\DataFrame;

class ServerRpc extends Server {
    
    /**
     * 初始化各种数据库单例
     * {@inheritDoc}
     * @see \Common\Server\Server::initDb()
     */
    protected function initDb() 
    {
        if ( $this->config->mysql ) {
            // 初始化 mysql
            $db = Mysql::instance($this->config->mysql);
        }
    }
    
    /**
     * 初始化默认监听事件
     * {@inheritDoc}
     * @see \Server\Server::initEvent()
     */
    protected function initEvent() :EventVector
    {
        $events = new EventVector();
        $events->addEvent(new Event('Start', [$this, 'onStart']));
        $events->addEvent(new Event('ManagerStart', [$this, 'onManagerStart']));
        $events->addEvent(new Event('WorkerStart', [$this, 'onWorkerStart']));
        $events->addEvent(new Event('Connect', [$this, 'onConnect']));
        $events->addEvent(new Event('Receive', [$this, 'onReceive']));
        $events->addEvent(new Event('Close', [$this, 'onClose']));
        return $events;
    }
    
    /**
     * 主进程启动调用
     */
    public function onStart() 
    {
        $serverName = $this->config->getName();
        // 设置 master 进程名称
        if ( !cli_set_process_title("[php] Smart-{$serverName} master") ) {
            $this->log->warning('Can not set process title');
            return;
        }
    }
    
    /**
     * manager 进程启动调用
     */
    public function onManagerStart()
    {
        $serverName = $this->config->getName();
        // 设置 manager 进程名称
        if ( !cli_set_process_title("[php] Smart-{$serverName} manager") ) {
            $this->log->warning('Can not set process title');
            return;
        }
    }
    
    /**
     * worker 启动调用
     */
    public function onWorkerStart($serv, $workerId)
    {
        $serverName = $this->config->getName();
        // 设置 worker 进程名称
        if ( !cli_set_process_title("[php] Smart-{$serverName} worker") ) {
            $this->log->warning('Can not set process title');
            return;
        }
    }
    
    /**
     * 客户端连接相应函数
     */
    public function onConnect($serv, $fd)
    {
        $this->log->info("Hello Client {$fd}");
    }
    
    /**
     * 收到客户端数据响应函数
     */
    public function onReceive($serv, $fd, $fromId, $data) 
    {
        // 解析协议包
        $this->bufferReader->append($data);
        $frame = $this->frameReader->consumeFrame($this->bufferReader);
        
        $request = JsonRequest::decode($frame->getBody());
        
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
            $class = ('\\Common\\Server\\Action\\'.ucfirst($action).'Action')::instance();
            $result = $class->execute($this, $params);
        } catch (\Throwable $t) {
            // 写入错误日志
            $errMsg = $this->log->format($t);
            $this->log->error($errMsg);
            // 响应客户端
            $response = new JsonResponse($request->getId(), 100000000, '服务器异常', []);
            $str = $response->encode();
            $frame = new DataFrame(strlen($str), $str, "\n");
            $this->frameWriter->appendFrame($frame, $this->bufferWriter);
            $s = $this->bufferWriter->consume($this->bufferWriter->getLength());
            $serv->send($fd, $s);
            // 关闭客户端链接
            $this->close($fd);
            return;
        }
        // 将处理结果返回给客户端
        $response = new JsonResponse($request->getId(), 0, '成功', $result);
        $str = $response->encode();
        $frame = new DataFrame(strlen($str), $str, "\n");
        $this->frameWriter->appendFrame($frame, $this->bufferWriter);
        $s = $this->bufferWriter->consume($this->bufferWriter->getLength());
        $serv->send($fd, $s);
    }
    
    /**
     * 关闭客户端连接响应函数
     */
    public function onClose($serv, $fd) 
    {
        $this->log->info("Client: Close.");
    }
}