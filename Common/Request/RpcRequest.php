<?php
/**
 * 请求对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午6:53:45
 */
namespace Common\Request;
use Common\IO\AbstractBuffer;
use Common\Protocol\JsonRpc;
use Common\Client\Client;

class RpcRequest extends AbstractRequest
{
    public function writeBuffer(AbstractBuffer $buffer) 
    {
        $buffer->writeTo($this->toString());
    }
    
    public function readBuffer(AbstractBuffer $buffer)
    {
        $str = $buffer->read();
        $data = json_decode($str, true);
        if ( json_last_error() ) {
            throw new \Exception('RpcRequest read error.', 100000001);
        }
        // 解析请求头
        $this->headers = $data['headers'];
        // 解析协议数据
        $jsonRpc = JsonRpc::instance();
        $jsonRpc->decode($data['protocol']);
        $this->protocol =$jsonRpc;
    }
    
    protected function toString(): string
    {
        $data = [
            'headers' => $this->headers,
            'protocol' => $this->protocol->encode()
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    public function send(Client $client): bool 
    {
        $client->setRequest($this);
        return $client->connect();
    }
}