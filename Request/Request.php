<?php
/**
 * 请求对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午6:53:45
 */
namespace Request;

use Common\Singleton;
use Server\Parser\Call;
use Server\Parser\JsonRpc;

class Request {
    
    use Singleton;
    
    protected $call;
    
    public function getData() :string 
    {
        return JsonRpc::encode($this->call);
    }
    
    public function setData(Call $call) 
    {
        $this->call = $call;
    }
}