<?php
/**
 * 一个函数调用对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月6日
 * @time 下午2:22:05
 */
namespace Server\Parser;

use Library\DataSign;

class Call
{
    protected $service;
    
    protected $class;
    
    protected $method;
    
    protected $params;
    
    protected $clientId;
    
    protected $sn;
    
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
     * 获取客户端的认证ID
     * @return string
     */
    public function getClientId() :string 
    {
        return $this->clientId;
    }
    
    /**
     * 获取数据签名
     * @return string
     */
    public function getSn() :string 
    {
        return $this->sn;
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
     * 设置客户端 ID（此 ID 主要作为签名认证）
     * @param string $clientId
     */
    public function setClientId(string $clientId) 
    {
        $this->clientId = $clientId;
    }
    
    /**
     * 设置签名值
     * @param string $sn
     */
    public function setSn(string $sn) 
    {
        $this->sn = $sn;
    }
    
    /**
     * 校验数据签名
     * @param string $secret 签名秘钥
     * @return bool
     */
    public function checkSn(string $secret) :bool 
    {
        $tmp = [
            's' => $this->service,
            'c' => $this->class,
            'm' => $this->method,
            'p' => $this->params,
            'cid' => $this->clientId,
        ];
        $sn = DataSign::generate($tmp, $secret);
        return $sn == $this->sn ? true : false;
    }

    /**
     * 调用结构构造函数
     * @param string $service 服务名称
     * @param string $class 类名称
     * @param string $method 方法名称
     * @param array $params 参数数组
     */
    public function __construct(string $service, string $class, string $method, array $params, string $clientId,string $sn) 
    {
        $this->service = $service;
        $this->class = $class;
        $this->method = $method;
        $this->params = $params;
        $this->clientId = $clientId;
        $this->sn = $sn;
    }
}

