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
    public static function encode(array $data): string
    {
        $tmp = [
            'jsonrpc' => '2.0',
            'method' => $data['s'].'_'.$data['c'].'_'.$data['m'],
            'params' => $data,
            'id' => $data['u'],
        ];
        return json_encode($tmp, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * {@inheritDoc}
     * @see \Server\Parser\Parser::decode($str)
     */
    public static function decode(string $str): array
    {
        $data = json_decode($str, true);
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
        return $data['params'];
    }
}