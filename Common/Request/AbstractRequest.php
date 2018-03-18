<?php
/**
 * 抽象请求类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月9日
 * @time 上午9:44:52
 */
namespace Common\Request;

use Common\Protocol\Protocol;
use Common\IO\AbstractBuffer;
use Library\Singleton;
use Common\Client\Client;

abstract class AbstractRequest extends AbstractRequestResponse
{
    use Singleton;
    
    /**
     * 请求体 
     * @var Protocol
     */
    protected $protocol;
    
    /**
     * 请求头
     * @var string
     */
    protected $headers;
    
    public function setHeaders(array $headers) 
    {
        $this->headers = $headers;
    }
    
    public function setProtocol(Protocol $protocol) 
    {
        $this->protocol = $protocol;
    }
    
    public function getHeaders() :array 
    {
        return $this->headers;
    }
    
    public function getProtocol() :Protocol 
    {
        return $this->protocol;
    }
    
    abstract public function writeBuffer(AbstractBuffer $buffer); 
    
    abstract public function readBuffer(AbstractBuffer $buffer);
    
    abstract protected function toString(): string;
    
    abstract public function send(Client $client): bool; 
    
}

