<?php
/**
 * 数据库类
 * @author liujie <king.2oo8@163.com>
 * @date 2018年5月11日
 * @time 下午2:40:57
 */
namespace Db;

use PDO,PDOException,PDOStatement;

class Mysql
{
    /**
     * 单例模式，保存对象实例
     * @var Mysql
     */
    protected static $instance;
    
    /**
     * 私有化构造函数
     */
    protected function __construct() {}
    
    /**
     * 私有化对象复制函数
     */
    protected function __clone() {}
    
    /**
     * 初始化数据库对象
     * @param string $dsn mysql 链接地址
     * @return Mysql
     */
    public static function instance(array $config=[]): Mysql
    {
        $className = get_called_class();
        if (!isset(self::$instance)) {
            self::$instance = new $className();
        }
        if ( $config ) {
            self::$instance->dsn = $config['dsn'];
            self::$instance->user = $config['user'];
            self::$instance->pass = $config['pass'];
            self::$instance->persistent = $config['persistent'];
        }
        self::$instance->connect();
        return self::$instance;
    }
    
    /**
     * mysql 链接地址
     * @var string
     */
    private $dsn;
    
    /**
     * 用户名
     * @var string
     */
    private $user;
    
    /**
     * 密码
     * @var string
     */
    private $pass;
    
    /**
     * 是否持久化链接
     * @var bool
     */
    private $persistent;
    
    /**
     * pdo 连接
     * @var PDO
     */
    protected $connection ;

    protected $stmt = array();
    
    /**
     * 最后执行的sql
     * @var string
     */
    public $lastSql;
    
    //定位字段
    protected $position;
    
    /**
     * 连接检测超时时间
     * @var int
     */
    protected $pingTime = 100;

    const INSERT_ON_DUPLICATE_UPDATE = 'ondup_update';
    const INSERT_ON_DUPLICATE_UPDATE_BUT_SKIP = 'ondup_exclude';
    const INSERT_ON_DUPLICATE_IGNORE = 'ondup_ignore';
    
    /**
     * 检查链接
     * @return boolean
     */
    public function pdoPing(){
        // 检查是否需要ping数据库.
        if($this->pingTime < time()){
            return false;
        }
        return true;
    }

    /**
     * 创建数据库链接
     * @throws \Exception
     */
    protected function connect(){
        if ( !extension_loaded( 'pdo' ) ) {
            throw new \Exception("mysql PDO模块未加载" , __FILE__ . ':' . __LINE__ . "行");
        }
        // 检测连接是否存在，且未超时
        if ( isset($this->connection) && $this->pdoPing() ) {
            return;
        }
        $options = [PDO::ATTR_PERSISTENT => false];
        if ( $this->persistent) {
            $options = [PDO::ATTR_PERSISTENT => true];
        } 
        $this->connection = new PDO(
            $this->dsn,
            $this->user,
            $this->pass,
            $options
        );
        //自己写代码捕获Exception
        $this->connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        //回复列的默认显示格式
        $this->connection->setAttribute( PDO::ATTR_CASE , PDO::CASE_NATURAL );
        // 连接成功后添加ping时间。
        $this->pingTime = time() + 1800;
    }

    /**
     * 执行sql
     * @param null $params
     * @param int  $position
     * @return $this|bool
     */
    public function query($params = null, $position = 1)
    {
        if (!empty($this->stmt[$position]) && $this->stmt[$position] instanceof PDOStatement) {
            if (!is_null($params) && is_array($params)) {
                $this->bind($params, $position);
            }
            $this->stmt[$position]->execute();
            list($errorCode, $driverErrorCode, $errorMessage) = $this->stmt[$position]->errorInfo();
            if ($errorCode && $errorCode != '00000') {
                throw new PDOException($errorMessage);
            }
        }
        return $this;
    }

    /**
     * 获取IN绑定时的绑定列表和绑定数据.
     * @param string $var     绑定变量名.
     * @param array  $srcData 绑定的具体数据数组.
     * @param array  &$return 返回数组.
     * @return 绑定名称
     */
    public static function getBindKey($var, array $srcData, &$return)
    {
        $bindKey = '';
        foreach ($srcData as $key => $val) {
            $name = ':'. $var. '_';
            $bindKey .= ','. $name. $key;
            $return[$name. $key] = $val;
        }
        $bindKey = trim($bindKey, ',');

        return $bindKey;
    }

    /**
     * 创建预处理sql
     * @param     $sql
     * @param int $position
     * @return $this
     */
    public function createSql($sql, $position = 1)
    {
        $this->stmt[$position] = $this->connection->prepare($sql);

        return $this;
    }

    /**
     *  从结果集中的下一行返回单独的一列
     * @Author : whoSafe
     * @Example:$model->fetchColumn()
     * @param int $column
     * @param int $position
     *
     * @return mixed
     */
    public function fetchColumn($column = 0, $position = 1)
    {
        if (!empty($this->stmt[$position]) && $this->stmt[$position] instanceof PDOStatement) {
            return $this->stmt[$position]->fetchColumn($column);
        }
        return array();
    }

    /**
     * 游标方式获取数据
     * @param string $fetchAction
     * @param int    $position
     * @return mixed
     */
    public function fetch($fetchAction = "assoc", $position = 1)
    {
        if (!empty($this->stmt[$position]) && $this->stmt[$position] instanceof PDOStatement) {
            $this->fetchAction($fetchAction, $position);
            return $this->stmt[$position]->fetch()?:array();
        }

        return array();
    }

    /**
     * 获取全部信息
     * @param string $fetchAction
     * @param int    $position
     * @return mixed
     */
    public function fetchAll($fetchAction = "assoc", $position = 1)
    {
        if (!empty($this->stmt[$position]) && $this->stmt[$position] instanceof PDOStatement) {
            $this->fetchAction($fetchAction, $position);

            return $this->stmt[$position]->fetchAll();
        }

        return array();
    }

    /**
     * 返回影响行数，update,delete,insert    对select获取到的结果不能保证准确
     * @param int $position
     * @return int
     */
    public function rowCount($position = 1)
    {
        if (!empty($this->stmt[$position]) && $this->stmt[$position] instanceof PDOStatement) {
            return $this->stmt[$position]->rowCount();
        }

        return 0;
    }

    /**
     * 获取最后插入的id
     * @param string $name
     * @return bool|string
     */
    public function lastInsertId($name = "")
    {
        if (!empty($this->connection) && $this->connection instanceof PDO) {
            return $this->connection->lastInsertId($name);
        }

        return 0;
    }

    /**
     * 设置获取数据的方式
     * @param     $fetchAction
     * @param int $position
     */
    private function fetchAction($fetchAction, $position = 1)
    {

        switch ($fetchAction) {
            case "assoc":
                $get_fetch_action = PDO::FETCH_ASSOC; //asso array
                break;
            case "num":
                $get_fetch_action = PDO::FETCH_NUM; //num array
                break;
            case "object":
                $get_fetch_action = PDO::FETCH_OBJ; //object array
                break;
            case "both":
                $get_fetch_action = PDO::FETCH_BOTH; //assoc array and num array
                break;
            default:
                $get_fetch_action = PDO::FETCH_ASSOC;
                break;
        }
        $this->stmt[$position]->setFetchMode($get_fetch_action);
    }

    /**
     * 数据绑定
     * @param array $params
     * @param int   $position
     */
    private function bind(array $params, $position = 1)
    {
        $this->lastSql = $this->stmt[$position]->queryString;

        foreach ($params as $key => $val) {
            if (strstr($key, ":") === false) {
                continue;
            }
            switch (gettype($val)) {
                case "integer":
                    $type = PDO::PARAM_INT;
                    $this->lastSql = str_replace($key, $val, $this->lastSql);
                    break;
                case "boolean":
                    $type = PDO::PARAM_BOOL;
                    $this->lastSql = str_replace($key, $val, $this->lastSql);
                    break;
                case "NULL":
                    $type = PDO::PARAM_NULL;
                    $this->lastSql = str_replace($key, $val, $this->lastSql);
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    $this->lastSql = str_replace($key, "'" . $val . "'", $this->lastSql);
                    break;
            }

            $this->stmt[$position]->bindParam($key, $params[$key], $type);
        }
    }

    /**
     * 插入数据.
     * @Author : whoSafe
     * @param string $table 表名.
     * @param array $params 参数.
     * @param null $onDup
     * @return bool|string
     */
    public function insert($table, $params, $onDup = null)
    {
        $fields = array_keys($params);
        $bindParams = array();

        foreach ($params as $column => $value) {
            $bindParams[':' . $column . '_0'] = $value;
        }

        $columns = join(',', $fields);
        $values = join(',', array_keys($bindParams));

        $sql_part_ignore = '';
        $sql_part_on_dup = '';

        switch ($onDup) {
            case self::INSERT_ON_DUPLICATE_IGNORE:
                $sql_part_ignore = 'IGNORE';
                break;
            case self::INSERT_ON_DUPLICATE_UPDATE:
                $update_params = (func_num_args() >= 4) ? func_get_arg(3) : $params;
                if ($update_params) {
                    $updates = array_keys($update_params);
                    foreach ($update_params as $column => $value) {
                        $updates[] = $this->quoteObj($column) . "=:" . $this->quoteObj($column) . '_1';
                        $bindParams[':' . $this->quoteObj($column) . '_1'] = $value;
                    }
                    $sql_part_on_dup = 'ON DUPLICATE KEY UPDATE ' . join(",", $updates);
                }
                break;
            case self::INSERT_ON_DUPLICATE_UPDATE_BUT_SKIP:
                $noUpdateColumnNames = func_get_arg(3);
                if (!is_array($noUpdateColumnNames)) {
                    throw new \Exception('invalid INSERT_ON_DUPLICATE_UPDATE_BUT_SKIP argument');
                }
                $updates = array();
                foreach ($params as $column => $value) {
                    if (!in_array($column, $noUpdateColumnNames)) {
                        $updates[] = $this->quoteObj($column) . "=:" . $this->quoteObj($column) . '_2';
                        $bindParams[':' . $this->quoteObj($column) . '_2'] = $value;
                    }
                }
                $sql_part_on_dup = 'ON DUPLICATE KEY UPDATE ' . join(",", $updates);
                break;
            default:
        }

        $table = $this->quoteObj($table);
        $sql = "INSERT $sql_part_ignore INTO $table ($columns) VALUES ($values) $sql_part_on_dup";
        try {
            $this->createSql($sql)->query($bindParams);
            $id = $this->lastInsertId();
            if ($id) {
                return $id;
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * 数据重组.
     * @Author : whoSafe
     * @param $objName
     * @return array|mixed|string
     */
    protected function quoteObj($objName) {
        if (is_array ( $objName ))
        {
            $return = array ();
            foreach ( $objName as $k => $v )
            {
                $return[] = $this->quoteObj($v);
            }
            return $return;
        }
        else
        {
            $v = trim($objName);
            $v = str_replace('`', '', $v);
            $v = preg_replace('# +AS +| +#i', ' ', $v);
            $v = explode(' ', $v);
            foreach($v as $k_1=>$v_1)
            {
                $v_1 = trim($v_1);
                if($v_1 == '')
                {
                    unset($v[$k_1]);
                    continue;
                }
                if(strpos($v_1, '.'))
                {
                    $v_1 = explode('.', $v_1);
                    foreach($v_1 as $k_2=>$v_2)
                    {
                        $v_1[$k_2] = '`'.trim($v_2).'`';
                    }
                    $v[$k_1] = implode('.', $v_1);
                }
                else
                {
                    $v[$k_1] = '`'.$v_1.'`';
                }
            }
            $v = implode(' AS ', $v);
            return $v;
        }
    }

    /**
     * 开始事务
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * 事务提交
     */
    public function commit() {
        $this->connection->commit();
    }

    /**
     * 事务回滚
     */
    public function rollBack() {
        $this->connection->rollback();
    }
}