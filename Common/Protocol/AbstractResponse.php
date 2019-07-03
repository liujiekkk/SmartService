<?php
/**
 * 抽象响应对象
 * @author liujie <king.2oo8@163.com>
 * @date 2019年7月2日
 * @time 下午5:34:19
 */
namespace Common\Protocol;

use Library\Singleton;

abstract class AbstractResponse
{
    use Singleton;
    
    protected $id;
    
    protected $code;
    
    protected $message;
    
    protected $data;
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getCode(): int 
    {
        return $this->code;
    }
    
    public function getMessage(): string 
    {
        return $this->message;
    }
    
    public function getData(): array 
    {
        return $this->data;
    }
    
    public function setId(string $id): self 
    {
        $this->id = $id;
        return $this;
    }
    
    public function setCode(int $code): self 
    {
        $this->code = $code;
        return $this;
    }
    
    public function setMessage(string $message): self 
    {
        $this->message = $message;
        return $this;
    }
    
    public function setData(array $data): self 
    {
        $this->data = $data;
        return $this;
    }
    
    abstract public function encode(): string;
    
    abstract public static function decode(string $msg): self;
}

