<?php
/**
 * 一个函数调用对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月6日
 * @time 下午2:22:05
 */
namespace Common\Action;

/**
 * 
 * @author liujie
 * @method setService
 */
class RpcCall extends Action
{
    protected $service;
    
    protected $class;
    
    protected $method;
    
    protected $params;
    
    /**
     * 返回结果
     * @var mixed
     */
    protected $return;
    
    /**
     * 获取服务名称
     * @return string
     */
    public function getService() :string
    {
        return $this->service;
    }

    /**
     * 获取调用类名称
     * @return string
     */
    public function getClass() :string
    {
        return $this->class;
    }

    /**
     * 获取调用方法名称
     * @return string
     */
    public function getMethod() :string
    {
        return $this->method;
    }

    /**
     * 获取调用参数数组
     * @return array
     */
    public function getParams() :array
    {
        return $this->params;
    }
    
    /**
     * 设置服务名称
     * @param string $service
     */
    public function setService(string $service)
    {
        $this->service = $service;
    }
    
    /**
     * 设置调用类名称
     * @param string $class
     */
    public function setClass(string $class)
    {
        $this->class = $class;
    }

    /**
     * 设置调用方法名称
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * 设置调用参数数组
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }
    
    /**
     * 将 call 转换成字符串
     * @return string
     */
    public function encode() :string 
    {
        return json_encode(
            [
                'service' => $this->service, 
                'class' => $this->class, 
                'method' => $this->method, 
                'params' => $this->params
            ], 
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
    
    /**
     * 字符串转换成对象
     * @param string $str 字符串
     * @return Action
     */
    public function decode(string $str) 
    {
        $data = json_decode($str, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ( json_last_error() ) {
            throw new \Exception('RpcCall Action decode error:'.$str);
        }
        $this->service = $data['service'];
        $this->class = $data['class'];
        $this->method = $data['method'];
        $this->params = $data['params'];
    } 
    
    /**
     * {@inheritDoc}
     * @see \Common\Action\Action::execute()
     */
    public function execute()
    {
        try {
            $obj = new $this->class();
            $this->return = call_user_func_array([$obj, $this->method], $this->params);
            return true;
        } catch (\Throwable $t) {
            echo $t->getMessage()."\n";
            return false;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Common\Action\Action::getReturn()
     */
    public function getReturn()
    {
        return $this->return;
    }
}

