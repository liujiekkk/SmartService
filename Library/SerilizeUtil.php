<?php
/**
 * 数据解析器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月6日
 * @time 下午2:16:24
 */
namespace Library;

class SerilizeUtil
{
    /**
     * 将原始数据转成协议数据
     * @param array $data
     * @return string
     */
    public static function serilize(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 将协议数据转换回原始数据
     * @param string $str
     * @return array
     */
    public static function unserilize(string $str): array
    {
        $data = json_decode($str, true);
        if ( json_last_error() ) {
            throw new \Exception('SerilizeUtil unserilize data exception:'. $str );
        }
        return $data;
    }
}