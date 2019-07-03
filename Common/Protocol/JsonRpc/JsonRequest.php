<?php
namespace Common\Protocol\JsonRpc;

use Common\Protocol\AbstractRequest;

class JsonRequest extends AbstractRequest
{    
    /**
     * 将数据转换成buffer 类型
     * @param Buffer $buffer
     */
    public function encode(): string
    {
        $data = [
            'id' => $this->id,
            'class' => $this->class,
            'method' => $this->method,
            'params' => $this->params,
            'action' => $this->action
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    public static function decode(string $msg): AbstractRequest
    {
        $data = json_decode($msg, true);
        if ( json_last_error() ) {
            throw new \Exception('JsonRpc Parser error.', 100000000);
        }
        $obj = self::instance();
        $obj->setId($data['id'])
            ->setClass($data['class'])
            ->setMethod($data['method'])
            ->setParams($data['params'])
            ->setAction($data['action']);
        return $obj;
    }
}

