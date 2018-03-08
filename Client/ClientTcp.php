<?php
/**
 * TCP 客户端
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:18:44
 */
namespace Client;
use Server\Event\EventVector;
use Server\Event\Event;
use Request\Request;
use Library\Shell;

class ClientTcp extends Client 
{
    protected $shell;
    
    /**
     * 实例化客户端
     * @param int $is_sync SWOOLE_SOCK_ASYNC 或者 SWOOLE_SOCK_SYNC
     * @param string $key 客户端唯一标识
     */
    public function __construct(int $is_sync = SWOOLE_SOCK_ASYNC, string $key='') {
        $this->client = new \swoole_client(SWOOLE_TCP, $is_sync, $key);
        $this->shell = new Shell();
    }
    
    public function initEvent() :EventVector
    {
        $events = new EventVector();
        $events->addEvent(new Event('connect', [$this, 'onConnect']));
        $events->addEvent(new Event('receive', [$this, 'onReceive']));
        $events->addEvent(new Event('close', [$this, 'onClose']));
        $events->addEvent(new Event('error', [$this, 'onError']));
        return $events;
    }
    
    /**
     * 链接建立成功相应函数
     * @param Swoole\Client $client
     * @param string $data
     */
    public function onConnect($client, $data='') 
    {
        echo "Connect success.\n";
        // 链接成功以后发送请求数据
        $client->send($this->request->toString());
    }
    
    public function onReceive($client, string $data='') 
    {
        if (!empty($data)) {
            $text = $this->shell->colorFont('received:'. $data, Shell::COLOR_GREEN);
             $this->shell->println($text);
            $client->close();
            return;
        }
        echo "no response.\n";
    }
    
    public function onClose($client) 
    {
        echo "client close\n";
    }
    
    public function onError($client) 
    {
        exit("error\n");
    }
}