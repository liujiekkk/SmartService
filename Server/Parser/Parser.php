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
    
    public static function encode(Call $call) :string;
    
    public static function decode(string $str) :Call;
}