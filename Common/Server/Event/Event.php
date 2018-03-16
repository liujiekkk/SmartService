<?php
/**
 * 服务端响应事件
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月5日
 * @time 下午6:54:04
 */
namespace Common\Server\Event;

class Event
{
    /**
     * 事件名称
     * @var string
     */
    protected $eventName;
    
    /**
     * 回调函数
     * @var callback
     */
    protected $callback;
    
    /**
     * 服务端事件构造函数
     * @param string $eventName 事件名称
     * @param callable $callback 回调函数
     */
    public function __construct(string $eventName, callable $callback) {
        $this->eventName = $eventName;
        $this->callback = $callback;
    }
    
    /**
     * 获取事件名称
     * @return string
     */
    public function getEventName() :string 
    {
        return $this->eventName;
    }
    
    /**
     * 获取回调函数
     * @return callable
     */
    public function getCallback() :callable 
    {
        return $this->callback;
    }
}

