<?php
namespace Common\Log;

use Library\Shell;

class Log implements ILog
{
    const LOG_TYPE_INFO = 'info';
    const LOG_TYPE_WARNING = 'warning';
    const LOG_TYPE_ERROR = 'error';
    const LOG_TYPE_DEBUG = 'debug';
    
    /**
     * 是否为debug 模式
     * @var bool
     */
    private $debug;
    
    /**
     * 日志文件路径
     * @var string
     */
    protected $filePath;
    
    /**
     * shell 显示控制类
     * @var Shell
     */
    protected $shell;
    
    protected $template = '{time} {type} {info}';
    
    /**
     * 初始化日志模板
     * @param string $filePath
     * @param bool $debug
     */
    public function __construct(string $filePath='', bool $debug=true) {
        $this->debug = $debug;
        $this->filePath = empty($filePath) ? '/tmp/smartservice.log' : $filePath;
        $this->shell = new Shell();
    }

    public function debug(string $info)
    {
        if ( $this->debug ) {
            $colorStr = $this->shell->colorFont(self::LOG_TYPE_DEBUG, Shell::COLOR_PINK);
            $str = $this->getFormatLineData($info, $colorStr);
            $this->shell->println($str);
        } else {
            $this->writeToLogFile($this->filePath, $info);
        }
    }

    public function warning(string $info)
    {
        if ( $this->debug ) {
            $colorStr = $this->shell->colorFont(self::LOG_TYPE_WARNING, Shell::COLOR_YELLOW);
            $str = $this->getFormatLineData($info, $colorStr);
            $this->shell->println($str);
        } else {
            $this->writeToLogFile($this->filePath, $info);
        }
    }

    public function error(string $info)
    {
        if ( $this->debug ) {
            $colorStr = $this->shell->colorFont(self::LOG_TYPE_ERROR, Shell::COLOR_RED);
            $str = $this->getFormatLineData($info, $colorStr);
            $this->shell->println($str);
        } else {
            $this->writeToLogFile($this->filePath, $info);
        }
    }

    public function info(string $info)
    {
        $str = $this->getFormatLineData($info, self::LOG_TYPE_INFO);
        if ( $this->debug ) {
            $this->shell->println($str, self::LOG_TYPE_INFO, Shell::COLOR_GREEN);
        } else {
            $this->writeToLogFile($this->filePath, $info);
        }
    }
    
    protected function getFormatLineData(string $info, string $logType): string 
    {
        $timeStr = date('Y-m-d H:i:s', time());
        return str_replace(['{time}', '{type}', '{info}'], [$timeStr, $logType, $info], $this->template);
    }
    
    /**
     * 写入日志文件
     * @param string $filePath
     */
    protected function writeToLogFile(string $filePath, string $str) 
    {
        file_put_contents($filePath, $str. PHP_EOL, FILE_APPEND);
    }
    
    /**
     * 设置debug 模式
     * @param bool $isDebug
     */
    public function setDebug(bool $isDebug) {
        $this->debug = $isDebug;
    }
    
    public function setLogPath(string $filePath) {
        $this->filePath = $filePath;
    }
}

