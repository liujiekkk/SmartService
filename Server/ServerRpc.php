<?php
/**
 * Swoole Server
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月13日
 * @time 下午4:11:42
 */
namespace Server;

use Server\Event\EventVector;
use Server\Event\Event;
use Common\Protocol\FrameReader;
use Common\Protocol\JsonRpc\JsonRequest;
use Common\Protocol\DataFrame;

class ServerRpc extends Server {
    
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
        // 重新实例化请求对象
        $request = JsonRequest::decode($frame->getBody());
        // 请求分发
        $response = Dispatcher::instance()
            ->setServer($this)
            ->dispatch($request);
        
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