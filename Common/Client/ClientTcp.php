<?php
/**
 * TCP 客户端
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:18:44
 */
namespace Common\Client;
use Common\Server\Event\EventVector;
use Common\Server\Event\Event;
use Common\Request\Request;
use Library\Shell;
use Common\IO\StringBuffer;
use Common\Connection\Rpc\RpcConnection;

class ClientTcp extends Client 
{
    /**
     * 命令行对象
     * @var Shell
     */
    protected $shell;
    
    /**
     * 初始化 Client
     * @param string $host 指定监听的IP地址
     * @param int $port 监听端口号
     * @param $is_sync 是否是同步客户端
     * @param $key 客户端唯一标识
     */
    public static function instance(int $is_sync=SWOOLE_SOCK_ASYNC, string $key='') :Client
    {
        if ( !self::$instance ) {
            self::$instance = new static();
            self::$instance->shell = new Shell();
            self::$instance->client = new \swoole_client(SWOOLE_TCP, $is_sync, $key);
        }
        return self::$instance;
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
        $this->log('Connect success.');
        // 链接成功以后发送请求数据
        $buffer = new StringBuffer();
        // 写入buffer
        $this->connection->writeBuffer($buffer);
        $client->send($buffer->read());
    }
    
    public function onReceive($client, string $data='') 
    {
        if (!empty($data)) {
            $text = $this->shell->colorFont('received:'. $data, Shell::COLOR_GREEN);
            $this->log($text);
            $client->close();
            return;
        }
        $this->log('no response.');
    }
    
    public function onClose($client) 
    {
        $this->log('client close');
    }
    
    public function onError($client) 
    {
        exit("error\n");
    }
    
    /**
     * 日志记录函数，debug 模式下为输出
     * @param string $text 记录内容
     */
    protected function log(string $text) 
    {
        $this->shell->println($text);
    }
}