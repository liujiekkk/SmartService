<?php
/**
 * 抽象请求接口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月9日
 * @time 上午9:44:52
 */
namespace Common\Connection;

abstract class Request implements Base
{
    protected $headers = [];
    
    protected $body = [];
    
    protected $timeout = 0;
    
    /**
     * 设置请求体
     * @todo 暂未启用
     * @param String $body
     */
    abstract public function setRequestBody(array $body);
    
    /**
     * 获取请求体
     * @return array
     */
    abstract public function getRequestBody(): array;
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::setData()
     */
    public function setData(array $data)
    {
        $this->body = $data;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::setHeader()
     */
    public function setHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::setHeaders()
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::getHeaders()
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    public function getHeader(string $name): string
    {
        if ( !isset($this->headers[$name]) ) {
            throw new \Exception('Undefined header name:'.$name);
        }
        return $this->headers[$name];
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::getData()
     */
    public function getData(): array
    {
        return $this->body;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::setTimeout($millis)
     */
    public function setTimeout(int $millis)
    {
        $this->timeout = $millis;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::getTimeout()
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
}

