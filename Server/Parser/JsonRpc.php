<?php
/**
 * JsonRpc 数据解析器
 * @author liujie <king.2oo8@163.com>
 * @date 2018年3月6日
 * @time 下午2:13:15
 */
namespace Server\Parser;
use Server\Parser\Call;

class JsonRpc implements Parser
{
    
    /**
     * {@inheritDoc}
     * @see \Server\Parser\Parser::encode($call)
     */
    public static function encode(Call $call): string
    {
        $tmp = [
            's' => $call->getService(),
            'c' => $call->getClass(),
            'm' => $call->getMethod(),
            'p' => $call->getParams(),
            'cid' => $call->getClientId(),
            'sn' => $call->getSn()
        ];
        return json_encode($tmp, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * {@inheritDoc}
     * @see \Server\Parser\Parser::decode($str)
     */
    public static function decode(string $str): Call
    {
        $data = json_decode($str, true);
        if ( json_last_error() ) {
            throw new \Exception('JsonRpc Parser error.', 100000000);
        }
        if ( !isset($data['s']) ) {
            throw new \Exception('JsonRpc no service name.', 100000000);
        }
        if ( !isset($data['c']) ) {
            throw new \Exception('JsonRpc no class name.', 100000000);
        }
        if ( !isset($data['m']) ) {
            throw new \Exception('JsonRpc no method name.', 100000000);
        }
        if ( !isset($data['p']) ) {
            throw new \Exception('JsonRpc no params data.', 100000000);
        }
        if ( !isset($data['cid']) ) {
            throw new \Exception('JsonRpc no cid data.', 100000000);
        }
        if ( !isset($data['sn']) ) {
            throw new \Exception('JsonRpc no sn data.', 100000000);
        }
        return new Call($data['s'], $data['c'], $data['m'], $data['p'], $data['cid'], $data['sn']);
    }

}