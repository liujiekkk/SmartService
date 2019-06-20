<?php
namespace Common\Protocol;

use Common\Exception\FrameException;

class FrameReader
{
    /**
     * 
     * @param Buffer $buffer
     * @throws ProtocolException
     * @return AbstractFrame|NULL
     */
    public function consumeFrame(Buffer $buffer) : ?AbstractFrame
    {
        // 看是否有足够的header 长度
        if ($buffer->getLength() < 5) {
            return null;
        }
        
        $type = $buffer->readUint8(0);
        $length = $buffer->readUint32(1);
        $bodyOffset = 5; // type:uint8=>1 + length:uint32
        // 没有足够数据
        if ($buffer->getLength() < $bodyOffset + $length + 1) {
            return null;
        }
        
        $buffer->consume(5);
        $body = $buffer->consume($length);
        $frameEnd = $buffer->consumeUint8();
        // 普通类型数据帧
        if ($type == 1 ) {
            return new DataFrame($length, $body, $frameEnd);
        } else {
            throw new FrameException('error frame header type: '. $type);
        }
    }
}

