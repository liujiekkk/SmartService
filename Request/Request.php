<?php
/**
 * 请求对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午6:53:45
 */
namespace Request;

use Common\Singleton;

class Request {
    
    use Singleton;
    
    /**
     * 设置返回数据
     * @param array $data
     * @return Request
     */
    public function setData(array $data) :Request 
    {
        $this->data = $data;
        return $this;
    }
}