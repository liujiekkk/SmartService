<?php
/**
 * 链接接口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月21日
 * @time 下午3:04:55
 */
namespace Common\Connection;
use Common\IO\AbstractBuffer;

abstract class Connection implements Base
{
    protected $request;
    
    protected $response;
    
    protected $timeout = 0;
    
    abstract protected function encode(): string;
    
    abstract protected function decode(string $str);
    
    public function writeBuffer(AbstractBuffer $buffer) 
    {
        $buffer->writeTo($this->encode());
    }
    
    public function readBuffer(AbstractBuffer $buffer) 
    {
        $str = $buffer->read();
        $this->decode($str);
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::setTimeout​($millis)
     */
    public function setTimeout(int $millis)
    {
        $this->timeout = $millis;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::getTimeout​()
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
    
    /**
     * 获取请求对象
     */
    public function getRequest(): Request 
    {
        return $this->request;
    }
    
    /**
     * 获取响应对象
     */
    public function getResponse(): Response 
    {
        return $this->response;    
    }
    
    /**
     * 设置请求对象
     * @param AbstractRequest $request
     */
    public function setRequest(Request $request) 
    {
        $this->request = $request;
        $this->response = null;
    }
    
    /**
     * 设置响应对象
     * @param AbstractResponse $response
     */
    public function setResponse(Response $response) 
    {
        $this->response = $response;
        $this->request = null;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::getHeaders()
     */
    public function getHeaders(): array
    {
        if ( $this->request ) {
            return $this->request->getHeaders();
        } else {
            return $this->response->getHeaders();
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::setHeader($name, $value)
     */
    public function setHeader(string $name, string $value)
    {
        if ( $this->request ) {
            return $this->request->setHeader($name, $value);
        } else {
            return $this->response->setHeader($name, $value);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::setHeaders($headers)
     */
    public function setHeaders(array $headers)
    {
        if ( $this->request ) {
            return $this->request->setHeaders($headers);
        } else {
            return $this->response->setHeaders($headers);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::getHeader($name)
     */
    public function getHeader(string $name): string
    {
        if ( $this->request ) {
            return $this->request->getHeader($name);
        } else {
            return $this->response->getHeader($name);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::setData($data)
     */
    public function setData(array $data)
    {
        if ( $this->request ) {
            return $this->request->setData($data);
        } else {
            return $this->response->setData($data);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\Connection\Base::getData()
     */
    public function getData(): array
    {
        if ( $this->request ) {
            return $this->request->getData();
        } else {
            return $this->response->getData();
        }
    }
}

