<?php
/**
 * SmartService 服务管理
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月3日
 * @time 上午10:50:57
 */
use Common\Process\SwooleProcess;
use Library\Shell;
class SmartService
{
    protected $shell;
    
    public function __construct() 
    {
        $this->shell = new Shell();
    }
    
    /**
     * 获取服务端列表
     * @return array
     */
    protected function getServerList(): array 
    {
        $path = __DIR__.'/Conf/Server/';
        $fileList = Shell::getFileList($path);
        $data = [];
        foreach ($fileList as $fileName) {
            $pos = strrpos($fileName, '.');
            if ( $pos ) {
                $data[] = substr($fileName, 0, $pos);
            }
        }
        return $data;
    }
    
    /**
     * 创建单个服务端进程
     * @param string $signal 操作信号
     * @param string $serverName 服务名称
     */
    protected function fork(string $signal, string $serverName) {
        $process = new SwooleProcess(function (\swoole_process $worker) use ($signal, $serverName){
            // 启动单个 Server 进程
            $phpBin = '/usr/local/php7/bin/php';
            $params = [__DIR__.'/Server.php', '-s', $signal, '-n', $serverName];
            $worker->exec($phpBin, $params);
        }, true, true);
        $pid = $process->start();
        usleep(100);
        // 回收结束运行的子进程
        $rs = SwooleProcess::wait();
        if ( $rs['code'] ) {
            $str = $this->shell->colorFont("Server {$serverName} {$signal} failed.", Shell::COLOR_RED);
            $this->shell->println($str);
        } else {
            $str = $this->shell->colorFont("Server {$serverName} {$signal} success.", Shell::COLOR_GREEN);
            $this->shell->println($str);
        }
    }
    
    /**
     * 批量创建多个服务端进程
     * @param string $signal 操作信号
     */
    protected function forks(string $signal) 
    {
        $servers = $this->getServerList();
        foreach ($servers as $serverName) {
            $this->fork($signal, $serverName);
        }
    }
    
    /**
     * 启动所有服务
     */
    public function start(string $serverName='')
    {
        if ($serverName) {
            $this->fork('start', $serverName);
        } else {
            $this->forks('start');
        }
    }
    
    /**
     * 停止所有服务
     */
    public function stop(string $serverName='')
    {
        if ($serverName) {
            $this->fork('stop', $serverName);
        } else {
            $this->forks('stop');
        }
    }
    
    /**
     * 平滑重启所有服务（不停机）
     */
    public function reload(string $serverName='')
    {
        if ($serverName) {
            $this->fork('reload', $serverName);
        } else {
            $this->forks('reload');
        }
    }
    
    /**
     * 重启所有服务
     * @param string $serverName 服务名称
     */
    public function restart(string $serverName='')
    {
        if ($serverName) {
            $this->fork('restart', $serverName);
        } else {
            $this->forks('restart');
        }
    }
}
include_once 'Autoload.php';
Autoload::instance()->setIncludePath(__DIR__)->init();
$signal = '';
// 进程指定信号
if ( isset($argv[1]) ) {
    $signal = $argv[1];
}
$serverName = '';
if ( isset($argv[2]) ) {
    $serverName = $argv[2];
}
$service = new SmartService();
if ( !method_exists($service, $signal) ) {
    echo 'Usage: php SmartService.php [start|stop|reload|restart] [<server_name>]'.PHP_EOL;
    exit;
}
$service->{$signal}($serverName);
