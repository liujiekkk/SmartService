<?php
namespace Common\Protocol\JsonRpc;

use Common\Protocol\AbstractResponse;

class JsonResponse extends AbstractResponse
{
    
    /**
     * 将数据转换成buffer 类型
     * @param Buffer $buffer
     */
    public function encode(): string
    {
        $data = [
            'id' => $this->id,
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    public static function decode(string $msg): AbstractResponse
    {
        $data = json_decode($msg, true);
        if ( json_last_error() ) {
            throw new \Exception('JsonRpc Parser error.', 100000000);
        }
        self::instance()
            ->setId($data['id'])
            ->setCode($data['code'])
            ->setMessage($data['message'])
            ->setData($data['data']);
        return self::instance();
    }
}

