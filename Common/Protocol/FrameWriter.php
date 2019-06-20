<?php
namespace Common\Protocol;

class FrameWriter
{
    // 将帧内容写入到 buffer 缓存
    public function appendFrame(AbstractFrame $frame, Buffer $buffer) 
    {
        $buffer->appendUint8($frame->getType());
        $buffer->appendUint32($frame->getLength());
        $buffer->append($frame->getBody());
        $buffer->appendUint8("\n");
    }
}

