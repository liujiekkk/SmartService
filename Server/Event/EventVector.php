<?php
/**
 * 事件容器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月5日
 * @time 下午6:58:16
 */
namespace Server\Event;

class EventVector implements \IteratorAggregate
{
    /**
     * 事件容器数组
     * @var array
     */
    protected $eventVector;
    
    /**
     * 添加事件
     * @param Event $event
     */
    public function addEvent(Event $event) 
    {
        $eventId = $event->getEventName();
        $this->eventVector[$eventId] = $event;
    }
    
    /**
     * 删除事件
     * @param Event $event
     */
    public function removeEvent(Event $event) 
    {
        $eventId = $event->getEventName();
        unset($this->eventVector[$eventId]);
    }
    
    /**
     * 根据事件名称获取事件
     * @param string $eventName
     * @return Event
     */
    public function getEventByEventName(string $eventName) :Event
    {
        return $this->eventVector[$eventName];
    }
    
    /**
     * {@inheritDoc}
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->eventVector);
    }
}