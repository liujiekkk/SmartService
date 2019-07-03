<?php
/**
 * 抽象请求类
 * @author liujie <king.2oo8@163.com>
 * @date 2019年7月2日
 * @time 下午5:18:37
 */
namespace Common\Protocol;

use Library\Singleton;

abstract class AbstractRequest
{
    use Singleton;
    
    protected $id;
    
    protected $class;
    
    protected $method;
    
    protected $params;
    
    protected $action;
    
    public function getId(): string 
    {
        return $this->id;
    }
    
    public function getClass(): string 
    {
        return $this->class;
    }
    
    public function getMethod(): string 
    {
        return $this->method;
    }
    
    public function getParams(): array 
    {
        return $this->params;
    }
    
    public function getAction(): string 
    {
        return $this->action;
    }
    
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    public function setClass(string $class): self 
    {
        $this->class = $class;
        return $this;
    }
    
    public function setMethod(string $method): self 
    {
        $this->method = $method;
        return $this;
    }
    
    public function setParams(array $params): self 
    {
        $this->params = $params;
        return $this;
    }
    
    public function setAction(string $action): self 
    {
        $this->action = $action;
        return $this;
    }
    
    abstract public function encode(): string;
    
    abstract public static function decode(string $msg): self;
}

