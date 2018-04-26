<?php
/**
 * 常用工具类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月7日
 * @time 下午7:13:50
 */
namespace Library;
class Shell 
{
    /**
     * 红色字体
     * @var string
     */
    const COLOR_RED = 'red';
    
    /**
     * 绿色字体
     * @var string
     */
    const COLOR_GREEN = 'green';
    
    /**
     * 黄色字体
     * @var string
     */
    const COLOR_YELLOW = 'yellow';
    
    /**
     * 蓝色字体
     * @var string
     */
    const COLOR_BLUE = 'blue';
    
    /**
     * 淡蓝字体
     * @var string
     */
    const COLOR_SKY_BLUE = 'sky_blue';
    
    /**
     * 紫色字体
     * @var string
     */
    const COLOR_PURPLE = 'purple';
    
    /**
     * 普通字体
     * @var string
     */
    const COLOR_NORMAL = 'normal';
    
    /**
     * 输出带颜色字
     * @param string $text 字体
     * @param string $status 状态（green, red, yellow, blue）
     */
    public function colorFont(string $text, string $status=self::COLOR_BLUE) {
        $out = "";
        switch($status) {
            case self::COLOR_RED :
                $out = "[31m"; //Red
                break;
            case self::COLOR_GREEN :
                $out = "[32m"; //Green
                break;
            case self::COLOR_YELLOW :
                $out = "[33m"; //Yellow
                break;
            case self::COLOR_BLUE :
                $out = "[34m"; //Blue
                break;
            case self::COLOR_PURPLE :
                $out = "[35m";
                break;
            case self::COLOR_SKY_BLUE:
                $out = "[36m";
                break;
            case self::COLOR_NORMAL :
                $out = "[39m";
                break;
            default:
                $out = "[39m"; //normal
        }
        return chr(27) . "$out" . "$text" . chr(27) . "[0m";
    }
    
    /**
     * 打印一行数据
     * @param string $str 欲输出的字符串
     */
    public function println(string $str, string $text='', $color=self::COLOR_NORMAL) 
    {
        if ( $text ) {
            $colorText = $this->colorFont($text, $color);
            echo str_replace($text, $colorText, $str). PHP_EOL;
        } else {
            echo $str. PHP_EOL;
        }
    }
}