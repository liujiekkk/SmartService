<?php
/**
 * 请求对象
 * @author liujie <king.2oo8@163.com>
 * @date 2018年2月24日
 * @time 下午6:53:45
 */
namespace Request;

use Library\Singleton;
use Server\Parser\Call;
use Library\DataSign;
use Config\Main;

class Request {
    
    use Singleton;
    
    /**
     * 协议数据
     * @var string
     */
    protected $data;
    
    public function toString() :string 
    {
        return $this->data;
    }
    
    /**
     * 设置请求内容
     * @param Call $call 调用对象
     * @param string $user 请求的客户端用户id
     */
    public function setData(Call $call, string $user) 
    {
        $tmp = [
            's' => $call->getService(),
            'c' => $call->getClass(),
            'm' => $call->getMethod(),
            'p' => $call->getParams(),
            'u' => $user,
        ];
        $sn = DataSign::generate($tmp, Main::PROTOCOL_SIGN_SECRET);
        $tmp['sn'] = $sn;
        $this->data = (Main::PROTOCOL_DATA_PARSER)::encode($tmp);
    }
    
    /**
     * 将请求原始数据转换成 call
     * @param string $str 原始数据
     * @return Call
     */
    public static function str2Call(string $str) :Call 
    {
            $arr = (Main::PROTOCOL_DATA_PARSER)::decode($str);
            // 解析数据，判断签名
            $callSn = $arr['sn'];
            unset($arr['sn']);
            $realSn = DataSign::generate($arr, Main::PROTOCOL_SIGN_SECRET);
            if ($callSn != $realSn) {
                throw new \Exception('Data sign error.', 100000002);
            }
            return new Call($arr['s'], $arr['c'], $arr['m'], $arr['p']);
    }
}