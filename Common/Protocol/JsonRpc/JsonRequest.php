<?php
namespace Common\Protocol\JsonRpc;

use Common\Protocol\Buffer;

class JsonRequest
{
    protected $id;
    
    protected $class;
    
    protected $method;
    
    protected $params;
    
    /**
     * user/system 用来区分系统命令和外部逻辑
     * @var string
     */
    protected $action = 'user';
    
    /**
     * @return the $id
     */
    public function getId(): string
    {
        return $this->id;
    }
    
    /**
     * @return the $class
     */
    public function getClass(): string
    {
        return $this->class;
    }
    
    /**
     * @return the $method
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    /**
     * @return the $params
     */
    public function getParams(): array
    {
        return $this->params;
    }
    
    public function getAction(): string 
    {
        return $this->action;
    }
    
    /**
     * @param field_type $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }
    
    /**
     * @param string $class
     */
    public function setClass(string $class)
    {
        $this->class = $class;
    }
    
    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }
    
    /**
     * @param field_type $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }
    
    public function setAction(string $action) 
    {
        $this->action = $action;
    }
    
    public function __construct(string $class, string $method, array $params)
    {
        $this->id = uuid_create();
        $this->class = $class;
        $this->method = $method;
        $this->params = $params;
    }
    
    /**
     * 将数据转换成buffer 类型
     * @param Buffer $buffer
     */
    public function encode(): string
    {
        $data = [
            'id' => $this->id,
            'class' => $this->class,
            'method' => $this->method,
            'params' => $this->params,
            'action' => $this->action
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    public static function decode(string $msg) 
    {
        $data = json_decode($msg, true);
        if ( json_last_error() ) {
            throw new \Exception('JsonRpc Parser error.', 100000000);
        }
        $obj = new self($data['class'], $data['method'], $data['params']);
        $obj->setAction($data['action']);
        return $obj;
    }
}

