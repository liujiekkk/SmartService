<?php
/**
 * 常用工具类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月7日
 * @time 下午7:13:50
 */
namespace Library;
class Tool 
{
    /**
     * 输出带颜色字
     * @param string $text 字体
     * @param string $status 状态（green, red, yellow, blue）
     */
    public static function colorFont($text, $status) {
        $out = "";
        switch($status) {
            case "green":
                $out = "[32m"; //Green
                break;
            case "red":
                $out = "[31m"; //Red
                break;
            case "yellow":
                $out = "[33m"; //Yellow
                break;
            case "blue":
                $out = "[34m"; //Blue
                break;
            default:
                $out = "[32m"; //Green
        }
        return chr(27) . "$out" . "$text" . chr(27) . "[0m";
    }
}