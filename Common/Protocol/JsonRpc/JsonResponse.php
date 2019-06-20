<?php
namespace Common\Protocol\JsonRpc;

class JsonResponse 
{
    
    protected $id;
    
    protected $code;
    
    protected $message;
    
    protected $data;
    
    

    /**
     * @return the $id
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return the $code
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return the $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return the $data
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param field_type $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param field_type $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @param field_type $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param field_type $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function __construct(string $id, string $code, string $msg, array $data) 
    {
        $this->id = $id;
        $this->code = $code;
        $this->message = $msg;
        $this->data = $data;
    }
    
    /**
     * 将数据转换成buffer 类型
     * @param Buffer $buffer
     */
    public function encode(): string
    {
        $data = [
            'id' => $this->id,
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    public static function decode(string $msg)
    {
        $data = json_decode($msg, true);
        if ( json_last_error() ) {
            throw new \Exception('JsonRpc Parser error.', 100000000);
        }
        return new self($data['id'], $data['code'], $data['message'], $data['data']);
    }
}

