<?php
/**
 * 字符串类型缓冲对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月9日
 * @time 上午10:51:21
 */
namespace Common\IO;

class StringBuffer extends AbstractBuffer
{
    /**
     * {@inheritDoc}
     * @see \Common\IO\AbstractBuffer::writeTo()
     */
    public function writeTo(string $str)
    {
        $this->data = $str;
    }
    
    /**
     * {@inheritDoc}
     * @see \Common\IO\AbstractBuffer::read()
     */
    public function read(): string
    {
        return $this->data;
    }
}

