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

abstract class AbstractRequest extends AbstractRequestResponse
{
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
    
    public function __construct(array $headers, Protocol $protocol) 
    {
        $this->protocol = $protocol;
        $this->headers = $headers;
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
    
    abstract public static function readBuffer(AbstractBuffer $buffer): AbstractRequest;
    
    abstract protected function toString(): string;
}

