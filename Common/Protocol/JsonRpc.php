<?php
/**
 * JsonRpc 数据解析器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月6日
 * @time 下午2:13:15
 */
namespace Common\Protocol;
use Library\SerilizeUtil;

class JsonRpc extends Protocol
{
    /**
     * 协议版本号
     * @var string
     */
    protected $jsonrpc;
    
    /**
     * 请求方法
     * @var string
     */
    protected $method;
    
    /**
     * 请求参数
     * @var array
     */
    protected $params;
    
    /**
     * 请求、响应ID
     * @var string
     */
    protected $id;
    
    /**
     * 响应结果
     * @var mixed
     */
    protected $result;
    
    /**
     * 错误对象
     * @var array
     */
    protected $error;
    
    /**
     * 错误码
     * @var string
     */
    protected $code;
    
    /**
     * 错误信息
     * @var string
     */
    protected $message;
    
    /**
     * 错误附加说明
     * @var string
     */
    protected $data;
    
    /**
     * @return the $jsonrpc
     */
    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    /**
     * @return the $method
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    public function getParams(): array 
    {
        return $this->params;
    }
    
    /**
     * @return the $id
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $jsonrpc
     */
    public function setJsonrpc(string $jsonrpc)
    {
        $this->jsonrpc = $jsonrpc;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    /**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
   
    public function setParams(array $data) 
    {
        $this->params = $data;
    }

    /**
     * {@inheritDoc}
     * @see \Common\Protocol\Protocol::encode()
     */
    public function encode(): string
    {
        $data = [
            'jsonrpc' => $this->jsonrpc,
            'method' => $this->method,
            'params' => $this->params,
            'id' => $this->id
        ];
        return SerilizeUtil::serilize($data);
    }
    
    /**
     * 反解析数据到协议对象
     * @param string $str
     * @throws \Exception
     * @return Protocol
     */
    public function decode(string $str)
    {
        $data = SerilizeUtil::unserilize($str);
        if ( json_last_error() ) {
            throw new \Exception('JsonRpc Parser error.', 100000000);
        }
        if ( !isset($data['jsonrpc']) ) {
            throw new \Exception('JsonRpc no jsonrpc.', 100000000);
        }
        if ( !isset($data['method']) ) {
            throw new \Exception('JsonRpc no method.', 100000000);
        }
        if ( !isset($data['params']) ) {
            throw new \Exception('JsonRpc no params.', 100000000);
        }
        if ( !isset($data['id']) ) {
            throw new \Exception('JsonRpc no id.', 100000000);
        }
        $this->jsonrpc = $data['jsonrpc'];
        $this->method = $data['method'];
        $this->params = $data['params'];
        $this->id = $data['id'];
    }
}