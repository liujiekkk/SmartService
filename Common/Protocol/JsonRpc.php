<?php
/**
 * JsonRpc 数据解析器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月6日
 * @time 下午2:13:15
 */
namespace Common\Protocol;
use Common\Action\RpcCall;
use Common\Action\Action;

class JsonRpc extends Protocol
{
    
    /**
     * {@inheritDoc}
     * @see \Common\Protocol\Protocol::encode()
     */
    public function encode(): string
    {
        $method = $this->action->getService().'_'.$this->action->getClass().'_'.$this->action->getMethod();
        $data = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $this->action->encode(),
            'id' => $method
        ];
        return SerilizeUtil::serilize($data);
    }
    
    /**
     * 反解析数据到协议对象
     * @param string $str
     * @throws \Exception
     * @return Protocol
     */
    public function decode(string $str)
    {
        $data = SerilizeUtil::unserilize($str);
        if ( json_last_error() ) {
            throw new \Exception('JsonRpc Parser error.', 100000000);
        }
        if ( !isset($data['jsonrpc']) ) {
            throw new \Exception('JsonRpc no jsonrpc.', 100000000);
        }
        if ( !isset($data['method']) ) {
            throw new \Exception('JsonRpc no method.', 100000000);
        }
        if ( !isset($data['params']) ) {
            throw new \Exception('JsonRpc no params.', 100000000);
        }
        if ( !isset($data['id']) ) {
            throw new \Exception('JsonRpc no id.', 100000000);
        }
        $rpcCall = RpcCall::instance();
        $rpcCall->decode($data['params']);
        $this->action = $rpcCall;
    }
}