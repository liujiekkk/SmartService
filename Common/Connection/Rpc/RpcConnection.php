<?php
namespace Common\Connection\Rpc;

use Common\Connection\Connection;
use Common\Protocol\JsonRpc;
use Common\Connection\Request;
use Common\Connection\Response;

class RpcConnection extends Connection
{
    protected function encode(): string
    {
        $jsonRpc = new JsonRpc();
        $jsonRpc->setId('1');
        $jsonRpc->setJsonrpc('2.0');
        if ( $this->request ) {
            // request 相关
            $jsonRpc->setMethod($this->request->getHeader('type'));
            $jsonRpc->setParams($this->request->getRequestBody());
            // 默认值
            $jsonRpc->setCode('');
            $jsonRpc->setMessage('');
            $jsonRpc->setError('');
            $jsonRpc->setResult('');
            $jsonRpc->setData('');
        } else {
            // response 相关
            $jsonRpc->setCode($this->response->getHeader('code'));
            $jsonRpc->setMessage($this->response->getHeader('message'));
            $jsonRpc->setError($this->response->getHeader('error'));
            $jsonRpc->setResult('');
            $jsonRpc->setData($this->response->getResponseBody());
            // 默认值
            $jsonRpc->setMethod('');
            $jsonRpc->setParams([]);
        }
        return $jsonRpc->encode();
    }
    
    protected function decode(string $str)
    {
        $jsonRpc = new JsonRpc();
        $jsonRpc->decode($str);
        if ( $this->request ) {
            // 解析请数据
            $this->request->setHeader('type', $jsonRpc->getMethod());
            $this->request->setRequestBody($jsonRpc->getParams());
        } else {
            // 解析响应数据
            $jsonRpc->decode($str);
            $this->response->setHeader('code', $jsonRpc->getCode());
            $this->response->setHeader('message', $jsonRpc->getMessage());
            $this->response->setHeader('error', $jsonRpc->getError());
            $this->response->setResponseBody($jsonRpc->getData());
        }
    }
}

