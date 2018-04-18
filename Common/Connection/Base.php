<?php
/**
 * 抽象请求相应对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月9日
 * @time 上午9:43:00
 */
namespace Common\Connection;

interface Base 
{
    public function getHeaders(): array;
    
    public function getHeader(string $name): string;
    
    /**
     * 设置链接数据 key=>value 关联数组
     * @param array $data
     */
    public function setData(array $data);
    
    /**
     * 获取链接数据
     * @return array
     */
    public function getData(): array; 
    
    /**
     * 设置请求头
     * @param string $name 请求头参数名
     * @param string $value 请求头参数值
     */
    public function setHeader(string $name, string $value);
    
    /**
     * 批量设置请求头信息
     * @param array $headers 请求头数组
     */
    public function setHeaders(array $headers);
    
    /**
     * 设置请求超时时间
     * @param int $millis
     */
    public function setTimeout(int $millis);
    
    /**
     * 获取请求超时时间
     * @return int
     */
    public function getTimeout(): int;
    
}