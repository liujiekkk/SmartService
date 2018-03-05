<?php
/**
 * 客户端模拟请求
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午5:16:21
 */
namespace Client;
class Client 
{
    protected $client;
    
    /**
     * 连接到远程服务器
     * @param string $host 远程服务器ip地址
     * @param int $port 远程服务器端口号
     * @param float $timeout 是网络IO的超时,包括connect/send/recv，单位是s，支持浮点数。默认为0.5s，即500ms
     * @param int $flag 参数在TCP类型,$flag=1表示设置为非阻塞socket，connect会立即返回。
     * 如果将$flag设置为1，那么在send/recv前必须使用swoole_client_select来检测是否完成了连接
     * @return bool
     */
    public function connect(string $host, int $port, float $timeout = 0.5, int $flag = 0) :Client 
    {
        $this->client->connect($host, $port, $timeout, $flag);
        return $this;
    }
    
    /**
     * 注册异步事件回调函数，调用on方法会使当前的socket变成非阻塞的
     * @param string $event 事件类型，(connect/error/receive/close)
     * @param callable $callback 回调函数(函数名字符串、匿名函数、类静态方法、对象方法)
     */
    public function on(string $event, callable $callback) :Client 
    {
        $this->client->on($event, $callback);
        return $this;
    }
    
    /**
     * 返回swoole_client的连接状态
     * @return bool
     */
    public function isConnected() :bool 
    {
        return $this->client->isConnected();
    }
    
    /**
     * 调用此方法可以得到底层的socket句柄，返回的对象为sockets资源句柄。
     * @return string
     */
    public function getSocket() :string 
    {
        return $this->client->getSocket();
    }
    
    /**
     * 用于获取客户端socket的本地host:port，必须在连接之后才可以使用
     * @return array
     */
    public function getSockName() :array 
    {
        return $this->client->getsockname();
    }
    
    /**
     * 获取对端（服务端）socket的IP地址和端口
     * @return array
     */
    public function getPeerName() :array 
    {
        return $this->client->getPeerName();
    }
    
    /**
     * 发送数据到远程服务器，必须在建立连接后，才可向Server发送数据
     * @param string $data
     * @return int
     */
    public function send(string $data) :int 
    {
        return $this->client->send($data);
    }
    
    /**
     * 向任意IP:PORT的主机发送UDP数据包，仅支持SWOOLE_SOCK_UDP/SWOOLE_SOCK_UDP6 类型的swoole_client对象
     * @param string $ip 目标主机的IP地址，支持IPv4/IPv6
     * @param int $port 目标主机端口
     * @param string $data 要发送的数据内容，不得超过64K
     * @return array
     */
    public function sendto(string $ip, int $port, string $data) :array 
    {
        return $this->client->sendto($ip, $port, $data);
    }
    
    /**
     * 发送文件到服务器，本函数是基于sendfile操作系统调用实现，在1.7.5以上版本可用
     * @param string $filename
     * @param int $offset
     * @param int $length
     */
    public function sendfile(string $filename, int $offset = 0, int $length = 0) 
    {
        return $this->client->sendfile($filename, $offset, $length);
    }
    
    /**
     * recv方法用于从服务器端接收数据
     * @param int $size 接收数据的缓存区最大长度，此参数不要设置过大，否则会占用较大内存 
     * @param int $flags 特殊的SOCKET接收设置 swoole_client::MSG_PEEK | swoole_client::MSG_WAITALL
     */
    public function recv(int $size = 65535, int $flags = 0) 
    {
        return $this->client->recv($size, $flags);
    }
    
    /**
     * 关闭链接
     * @param bool $force
     */
    public function close(bool $force = false) 
    {
        return $this->client->close();
    }
}