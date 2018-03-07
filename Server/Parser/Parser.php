<?php
/**
 * 数据解析器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月6日
 * @time 下午2:16:24
 */
namespace Server\Parser;
interface Parser
{
    /**
     * 将原始数据转成协议数据
     * @param array $data
     * @return string
     */
    public static function encode(array $data) :string;
    
    /**
     * 将协议数据转换回原始数据
     * @param string $str
     * @return array
     */
    public static function decode(string $str) :array;
}