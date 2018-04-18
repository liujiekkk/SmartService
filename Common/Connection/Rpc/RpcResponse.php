<?php
/**
 * Rpc 响应
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月19日
 * @time 下午3:27:46
 */
namespace Common\Connection\Rpc;
use Common\Connection\Response;

class RpcResponse extends Response
{
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Response::setResponseBody($body)
     */
    public function setResponseBody(array $body)
    {
        $this->body = $body;        
    }

    /**
     * {@inheritDoc}
     * @see \Common\Connection\Response::getResponseBody()
     */
    public function getResponseBody(): array
    {
        return $this->body;
    }
}

