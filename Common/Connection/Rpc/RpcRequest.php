<?php
/**
 * 请求对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午6:53:45
 */
namespace Common\Connection\Rpc;
use Common\Connection\Request;

class RpcRequest extends Request
{
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Request::setRequestBody()
     */
    public function setRequestBody(array $body)
    {
        $this->body = $body;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Request::getRequestBody()
     */
    public function getRequestBody(): array
    {
        return $this->body;
    }
}