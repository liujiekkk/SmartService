<?php
/**
 * 抽象响应对象接口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月9日
 * @time 上午9:45:32
 */
namespace Common\Connection;

abstract class Response implements Base 
{
    protected $headers = [];
    
    protected $body = [];
    
    protected $timeout = 0;
    
    /**
     * 设置响应体
     * @param string $method
     */
    abstract public function setResponseBody(array $body);
    
    /**
     * 获取相应体
     * @return string
     */
    abstract public function getResponseBody(): array;
    
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

