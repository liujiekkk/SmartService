<?php
/**
 * SwooleProcess 封装类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月3日
 * @time 上午11:20:38
 */
namespace Common\Process;

class SwooleProcess
{
    protected $process;
    
    public function __construct(callable $func, bool $redirect_stdin_stdout=false, bool $create_pipe=true) 
    {
        $this->process = new \swoole_process($func, $redirect_stdin_stdout, $create_pipe);
    }
    
    public function start(): int 
    {
        return $this->process->start();
    }
    
    public function name(string $name) 
    {
        $this->process->name($name);
    }
    
    public function exec(string $execfile, array $args): bool 
    {
        return $this->process->exec($execfile, $args);
    }
    
    public function write(string $data): int 
    {
        return $this->process->write($data);
    }
    
    public function read(int $bufferSize=8192) 
    {
        return $this->process->read($bufferSize);
    }
    
    public function setTimeout(float $timeout) 
    {
        $this->process->setTimeout($timeout);
    }
    
    public function close(int $which = 0): bool 
    {
        return $this->process->close($which);
    }
    
    public function exit(int $status = 0): int 
    {
        return $this->process->exit($status);
    }
    
    public static function kill($pid, $signo = SIGTERM): bool 
    {
        return \swoole_process::kill($pid, $signo);
    }
    
    public static function wait(bool $blocking = true): array 
    {
        return \swoole_process::wait($blocking);
    }
    
    public static function daemon(bool $nochdir = true, bool $noclose = true): bool
    {
        return \swoole_process::daemon($nochdir, $noclose);
    }
    
    public static function signal(int $signo, callable $callback): bool
    {
        return \swoole_process::signal($signo, $callback);
    }
    
    public static function alarm(int $interval_usec, int $type = ITIMER_REAL) : bool
    {
        return \swoole_process::alarm($interval_usec, $type);
    }
    
    public static function setAffinity(array $cpu_set) 
    {
        return \swoole_process::setAffinity($cpu_set);
    }
}

