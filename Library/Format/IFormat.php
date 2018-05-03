<?php
/**
 * 格式化数据接口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月3日
 * @time 下午2:18:51
 */
namespace Library\Format;

interface IFormat
{
    public static function format(string $input): string; 
}

