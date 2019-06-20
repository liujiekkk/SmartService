<?php
/**
 * swoole 传输的基本数据帧
 */
namespace Common\Protocol;

class DataFrame extends AbstractFrame
{
    protected $type = 1;
}

