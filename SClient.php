<?php
/**
 * 发送请求
 * @author liujie <king.2oo8@163.com>
 * @date 2019年6月28日
 * @time 下午4:42:41
 */
use Library\Singleton;
use Client\ClientRpc;

class SClient {
    use Singleton;
    
    protected $config;
    
    protected $className;
    
    /**
     * 设置请求的服务
     * @param string $serviceName
     * @return Client
     */
    public function service(string $serviceName, string $className): self 
    {
        $class = "\\Conf\\Client\\".ucfirst($serviceName);
        $this->config = new $class();
        $this->className = ucfirst($serviceName). "\\Api\\". $className;
        return $this;
    }
    
    public function __call(string $method, array $args): array
    {
        $client = new ClientRpc($this->config);
        $jsonRpc = $client->request($this->className, $method, $args, 'user');
        $ret = [
            'code' => $jsonRpc->getCode(),
            'message' => $jsonRpc->getMessage(),
            'data' => $jsonRpc->getData()
        ];
        return $ret;
    }
    
}


