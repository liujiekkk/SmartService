<?php
namespace Common\Protocol;
abstract class AbstractFrame
{
    /**
     * 数据帧类型 (request/response)
     * @var uint8
     */
    protected $type;
    
    /**
     * 数据包体长度
     * uint32
     * @var int
     */
    protected $length;
    
    /**
     * string 包体
     * @var string
     */
    protected $body;
    
    /**
     * frame end
     * @var string
     */
    protected $frameEnd;
    
    public function __construct($length, $body, $frameEnd) 
    {
        $this->length = $length;
        $this->body = $body;
        $this->frameEnd = $frameEnd;
    }
    
    public function getType() 
    {
        return $this->type;
    }
    
    public function getLength() 
    {
        return $this->length;
    }
    
    public function getBody() 
    {
        return $this->body;
    }
    
    public function getFrameEnd()
    {
        return $this->frameEnd;
    }
}
