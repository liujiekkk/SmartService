<?php
/**
 * 日志接口
 * @author liujie <king.2oo8@163.com>
 * @date 2018年4月19日
 * @time 上午10:45:49
 */
namespace Common\Log;

interface ILog
{
    /**
     * 记录信息数据
     * @param string $info
     */
    public function info(string $info);
    
    /**
     * 记录debug 数据
     * @param string $info
     */
    public function debug(string $info);
    
    /**
     * 记录警告数据
     * @param string $info
     */
    public function warning(string $info);
    
    /**
     * 记录错误数据
     * @param string $info
     */
    public function error(string $info);
    
    /**
     * 格式化输出信息
     * @param \Throwable $t
     * @return string
     */
    public function format(\Throwable $t): string;
    
}

