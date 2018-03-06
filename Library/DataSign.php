<?php
/**
 * 数据签名类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月6日
 * @time 下午6:51:23
 */
namespace Library;

class DataSign 
{
    /**
     * 生成数据签名
     * @param array $data 数据数组
     * @param string $secret 秘钥
     * @return string
     */
    public static function generate(array $data, $secret='') :string 
    {
        sort($data);
        $str = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return md5($str.$secret);
    }
}