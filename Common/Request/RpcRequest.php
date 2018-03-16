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

class RpcRequest extends AbstractRequest
{
    public function writeBuffer(AbstractBuffer $buffer) 
    {
        $buffer->writeTo($this->toString());
    }
    
    public static function readBuffer(AbstractBuffer $buffer): AbstractRequest
    {
        $str = $buffer->read();
        $data = json_decode($str, true);
        if ( json_last_error() ) {
            throw new \Exception('RpcRequest read error.', 100000001);
        }
        // 解析请求头
        $requestHeaders = $data['headers'];
        // 解析协议数据
        $jsonRpc = JsonRpc::decode($data['protocol']);
        return new self($data['headers'], $jsonRpc);
    }
    
    protected function toString(): string
    {
        $data = [
            'headers' => $this->headers,
            'protocol' => $this->protocol->encode()
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}