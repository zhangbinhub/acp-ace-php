<?php
namespace service\tools\connection;

use config\DataBaseConfig;
use service\tools\logger\LoggerClass;

class ConnectionFactoryClass
{

    private $dbno;

    private $charset = "";

    private $connection = null;

    private $dbType = "";

    private $sql_array = array();

    /**
     * 构造函数
     *
     * @param int $resourceIndex
     * @param bool $isPersistent 是否为长连接
     */
    function __construct($resourceIndex = 0, $isPersistent = false)
    {
        $config = DataBaseConfig::getInstance();
        if (isset($config[$resourceIndex])) {
            $resource = $config[$resourceIndex];
            try {
                $options = array();
                $options[\PDO::ATTR_PERSISTENT] = $isPersistent;
                $options[\PDO::ATTR_CASE] = \PDO::CASE_LOWER;
                if (isset($resource['ATTR_ERRMODE']) && $resource['ATTR_ERRMODE'] != "") {
                    switch ($resource['ATTR_ERRMODE']) {
                        case "ERRMODE_SILENT":
                            $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_SILENT;
                            break;
                        case "ERRMODE_WARNING":
                            $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_WARNING;
                            break;
                        case "ERRMODE_EXCEPTION":
                            $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
                            break;
                    }
                }
                if (isset($resource['ATTR_ORACLE_NULLS']) && $resource['ATTR_ORACLE_NULLS'] != "") {
                    switch ($resource['ATTR_ORACLE_NULLS']) {
                        case "NULL_NATURAL":
                            $options[\PDO::ATTR_ORACLE_NULLS] = \PDO::NULL_NATURAL;
                            break;
                        case "NULL_EMPTY_STRING":
                            $options[\PDO::ATTR_ORACLE_NULLS] = \PDO::NULL_EMPTY_STRING;
                            break;
                        case "NULL_TO_STRING":
                            $options[\PDO::ATTR_ORACLE_NULLS] = \PDO::NULL_TO_STRING;
                            break;
                    }
                }
                $this->connection = new \PDO($resource['url'], $resource['username'], $resource['password'], $options);
                if (isset($resource['charset']) && $resource['charset'] != "") {
                    if (stripos($resource['url'], 'mysql') !== false) {
                        $this->connection->query("set names " . $resource['charset'] . ";");
                    }
                    $this->charset = $resource['charset'];
                }
                if (isset($resource['dbtype'])) {
                    $this->dbType = strtolower($resource['dbtype']);
                }
                $this->dbno = $resourceIndex;
            } catch (\PDOException $e) {
                unset($this->connection);
                LoggerClass::error("Error!: " . $e->getMessage());
                die();
            }
        }
    }

    /**
     * 返回数据源编号
     * @return int
     */
    public function getDbno()
    {
        return (int)$this->dbno;
    }

    /**
     * 获取连接字符集
     *
     * @return string
     */
    public function getCharset()
    {
        return (string)$this->charset;
    }

    /**
     * 数据库类型
     *
     * @return string
     */
    public function getDbType()
    {
        return (string)$this->dbType;
    }

    /**
     * 当前连接是否处于自动提交状态
     *
     * @return boolean
     */
    public function isAutoCommit()
    {
        return !$this->connection->inTransaction();
    }

    /**
     * 数据库是否连接成功
     *
     * @return boolean
     */
    public function isConnSucc()
    {
        if ($this->connection != null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 通过SQL语句进行查询
     * @param $query_string
     * @return null|\PDOStatement
     */
    public function doQuery($query_string)
    {
        return $this->doQueryByPre($query_string);
    }

    /**
     * 通过预处理执行带参数SQL语句
     * @param $query_string
     * @param null $param
     * @return null|\PDOStatement
     */
    public function doQueryByPre($query_string, $param = null)
    {
        try {
            $stmt = $this->connection->prepare($query_string);
            $stmt->execute($param);
            return $stmt;
        } catch (\PDOException $e) {
            LoggerClass::error("sql failed: " . $e->getMessage() . "\nsql:" . $query_string);
            return null;
        }
    }

    /**
     * 通过SQL语句查询LOB数据
     * @param $tablename
     * @param $pkey
     * @param $pkeyvalue
     * @param $lobcolname
     * @return null|string
     */
    public function doQueryLOB($tablename, $pkey, $pkeyvalue, $lobcolname)
    {
        $sqlstring = 'select ' . $lobcolname . ' from ' . $tablename . ' where ' . $pkey . '=?';
        try {
            $lobparam = null;
            $stmt = $this->connection->prepare($sqlstring);
            $stmt->bindParam(1, $pkeyvalue);
            $stmt->execute();
            $stmt->bindColumn($lobcolname, $lobparam, \PDO::PARAM_LOB);
            $stmt->fetch(\PDO::FETCH_BOUND);
            $result = null;
            switch ($this->dbType) {
                case 'mysql':
                    $result = $lobparam;
                    break;
                case 'oracle':
                    $result = stream_get_contents($lobparam);
                    break;
                case 'mssql':
                    $result = $lobparam;
                    break;
                default:
                    $result = $lobparam;
                    break;
            }
            return $result;
        } catch (\PDOException $e) {
            LoggerClass::error("sql failed: " . $e->getMessage() . "\nsql:" . $sqlstring);
            return null;
        }
    }

    /**
     * 执行SQL语句
     *
     * @param string $sql_string
     * @return boolean
     */
    public function doExcute($sql_string)
    {
        return $this->doExcuteByPre($sql_string);
    }

    /**
     * 通过预处理执行带参数SQL语句
     *
     * @param string $query_string
     * @param array $param
     * @param bool $isbatch
     * @return bool
     */
    public function doExcuteByPre($query_string, $param = null, $isbatch = false)
    {
        try {
            if (!$isbatch) {
                $this->clearBatch();
            }
            $stmt = $this->connection->prepare($query_string);
            return $stmt->execute($param);
        } catch (\PDOException $e) {
            LoggerClass::error("sql failed: " . $e->getMessage() . "\nsql:" . $query_string);
            return false;
        }
    }

    /**
     * 插入带LOB数据的记录
     * @param $tablename
     * @param $pkey
     * @param $pkeyvalue
     * @param $lobcolname
     * @param $param
     * @return bool
     */
    public function doInsertLOB($tablename, $pkey, $pkeyvalue, $lobcolname, $param)
    {
        $sqlstring = '';
        try {
            $this->clearBatch();
            $commitable = false;
            if (!$this->connection->inTransaction()) {
                $this->connection->beginTransaction();
                $commitable = true;
            }
            switch ($this->dbType) {
                case 'mysql':
                    $sqlstring = 'insert into ' . $tablename . '(' . $pkey . ',' . $lobcolname . ') values(:key,:lob)';
                    break;
                case 'oracle':
                    $sqlstring = 'insert into ' . $tablename . '(' . $pkey . ',' . $lobcolname . ') values(:key,EMPTY_BLOB()) RETURNING ' . $lobcolname . ' INTO :lob';
                    break;
                case 'mssql':
                    $sqlstring = 'insert into ' . $tablename . '(' . $pkey . ',' . $lobcolname . ') values(:key,:lob)';
                    break;
                default:
                    $sqlstring = 'insert into ' . $tablename . '(' . $pkey . ',' . $lobcolname . ') values(:key,:lob)';
                    break;
            }
            if ($sqlstring !== '') {
                $stmt = $this->connection->prepare($sqlstring);
                $stmt->bindParam(':key', $pkeyvalue);
                $stmt->bindParam(':lob', $param, \PDO::PARAM_LOB);
                $result = $stmt->execute();
            } else {
                $result = false;
            }
            if ($result) {
                if ($commitable) {
                    $this->connection->commit();
                }
            } else {
                if ($commitable) {
                    $this->connection->rollBack();
                }
            }
            return $result;
        } catch (\PDOException $e) {
            LoggerClass::error("sql failed: " . $e->getMessage() . "\nsql:" . $sqlstring);
            return false;
        }
    }

    /**
     * 更新带LOB数据的记录
     * @param $tablename
     * @param $pkey
     * @param $pkeyvalue
     * @param $lobcolname
     * @param $param
     * @return bool
     */
    public function doUpdateLOB($tablename, $pkey, $pkeyvalue, $lobcolname, $param)
    {
        $sqlstring = '';
        try {
            $this->clearBatch();
            $commitable = false;
            if (!$this->connection->inTransaction()) {
                $this->connection->beginTransaction();
                $commitable = true;
            }
            switch ($this->dbType) {
                case 'mysql':
                    $sqlstring = 'update ' . $tablename . ' set ' . $lobcolname . '=:lob where ' . $pkey . '=:key';
                    break;
                case 'oracle':
                    $sqlstring = 'update ' . $tablename . ' set ' . $lobcolname . '=EMPTY_BLOB() where ' . $pkey . '=:key RETURNING ' . $lobcolname . ' INTO :lob';
                    break;
                case 'mssql':
                    $sqlstring = 'update ' . $tablename . ' ' . $lobcolname . '=:lob where ' . $pkey . '=:key';
                    break;
                default:
                    $sqlstring = 'update ' . $tablename . ' set ' . $lobcolname . '=:lob where ' . $pkey . '=:key';
                    break;
            }
            if ($sqlstring !== '') {
                $stmt = $this->connection->prepare($sqlstring);
                $stmt->bindParam(':key', $pkeyvalue);
                $stmt->bindParam(':lob', $param, \PDO::PARAM_LOB);
                $result = $stmt->execute();
            } else {
                $result = false;
            }
            if ($result) {
                if ($commitable) {
                    $this->connection->commit();
                }
            } else {
                if ($commitable) {
                    $this->connection->rollBack();
                }
            }
            return $result;
        } catch (\PDOException $e) {
            LoggerClass::error("sql failed: " . $e->getMessage() . "\nsql:" . $sqlstring);
            return false;
        }
    }

    /**
     * 开始事务
     *
     * @return boolean
     */
    public function beginTransaction()
    {
        try {
            if (!$this->connection->inTransaction()) {
                return $this->connection->beginTransaction();
            } else {
                return true;
            }
        } catch (\PDOException $e) {
            LoggerClass::error("beginTransaction failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 回滚事务
     *
     * @return boolean
     */
    public function rollBack()
    {
        try {
            if ($this->connection->inTransaction()) {
                $this->clearBatch();
                return $this->connection->rollBack();
            } else {
                return true;
            }
        } catch (\PDOException $e) {
            LoggerClass::error("rollBack failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 提交事务
     *
     * @return boolean
     */
    public function commit()
    {
        try {
            if ($this->connection->inTransaction()) {
                $this->clearBatch();
                return $this->connection->commit();
            } else {
                return true;
            }
        } catch (\PDOException $e) {
            LoggerClass::error("commit failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 将SQL语句添加进批量执行队列中
     *
     * @param string $sql_string
     */
    public function addBatch($sql_string)
    {
        try {
            array_push($this->sql_array, $sql_string);
        } catch (\PDOException $e) {
            LoggerClass::error("addBatch failed: " . $e->getMessage());
        }
    }

    /**
     * 清空批量执行队列
     */
    public function clearBatch()
    {
        try {
            unset($this->sql_array);
            $this->sql_array = array();
        } catch (\PDOException $e) {
            LoggerClass::error("clearBatch failed: " . $e->getMessage());
        }
    }

    /**
     * 批量执行SQL语句
     *
     * @return boolean
     */
    public function doExecBatch()
    {
        $autoCommit = false;
        if (!$this->connection->inTransaction()) {
            $autoCommit = true;
        }
        try {
            if ($autoCommit) {
                $this->beginTransaction();
            }
            foreach ($this->sql_array as $sql_string) {
                if (!$this->doExcuteByPre($sql_string, null, true)) {
                    if ($autoCommit) {
                        $this->rollBack();
                    }
                    return false;
                }
            }
            if ($autoCommit) {
                $this->commit();
            }
            $this->clearBatch();
            return true;
        } catch (\PDOException $e) {
            if ($autoCommit) {
                $this->rollBack();
            }
            LoggerClass::error("commit failed: " . $e->getMessage());
            $this->clearBatch();
            return false;
        }
    }
}

?>