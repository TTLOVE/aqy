<?php

namespace Service;
use Illuminate\Database\Capsule\Manager as Capsule;
use PDO as PDO;

/**
 * 设置连接数据库
 */
class DATABASE 
{

    private $Host;
    private $DBName;
    private $DBUser;
    private $DBPassword;
    private $pdo;
    private $sQuery;
    private $bConnected = false;
    private $log;
    private $parameters;
    public $rowCount   = 0;
    public $columnCount   = 0;
    public $querycount = 0;

    /**
     * 设置要连接的数据库
     *
     * @param $dbName 数据库名称
     *
     * @return mix
     */
    public function setDataBase($dbName='')
    {
        $dbConfig = require BASE_PATH.'/Config/database.php';
        $dbName = empty($dbName) ? 'finance' : $dbName;

        if ( isset($dbConfig[ $dbName ]) ) {
            // Eloquent ORM
            $capsule = new Capsule;
            $capsule->addConnection($dbConfig[ $dbName ]);
            $capsule->bootEloquent();

            // local pdo
            $this->dbConfig($dbConfig[ $dbName ]);
            return true;
        } else {
            throw new InvalidArgumentException("没有对应数据库配置信息!" . $dbName);
        }
    }

    public function dbConfig($dbConfig)
    {
        $this->Host       = $dbConfig['host'];
        $this->DBName     = $dbConfig['database'];
        $this->DBUser     = $dbConfig['username'];
        $this->DBPassword = $dbConfig['password'];
        $this->Connect();
        $this->parameters = array();
    }


    private function Connect()
    {
        try {
            $this->pdo = new PDO('mysql:dbname=' . $this->DBName . ';host=' . $this->Host . ';charset=utf8', 
                $this->DBUser, 
                $this->DBPassword,
                array(
                    //For PHP 5.3.6 or lower
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                    PDO::ATTR_EMULATE_PREPARES => false,
                    //长连接
                    //PDO::ATTR_PERSISTENT => true,

                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                )
            );
            $this->bConnected = true;

        }
        catch (PDOException $e) {
            echo $this->ExceptionLog($e->getMessage());
            die();
        }
    }


    public function CloseConnection()
    {
        $this->pdo = null;
    }


    private function Init($query, $parameters = "")
    {
        if (!$this->bConnected) {
            $this->Connect();
        }
        try {
            $this->parameters = $parameters;
            $this->sQuery     = $this->pdo->prepare($this->BuildParams($query, $this->parameters));

            if (!empty($this->parameters)) {
                if (array_key_exists(0, $parameters)) {
                    $parametersType = true;
                    array_unshift($this->parameters, "");
                    unset($this->parameters[0]);
                } else {
                    $parametersType = false;
                }
                foreach ($this->parameters as $column => $value) {
                    $this->sQuery->bindParam($parametersType ? intval($column) : ":" . $column, $this->parameters[$column]); //It would be query after loop end(before 'sQuery->execute()').It is wrong to use $value.
                }
            }

            $this->succes = $this->sQuery->execute();
            $this->querycount++;
        }
        catch (PDOException $e) {
            echo $this->ExceptionLog($e->getMessage(), $this->BuildParams($query));
            die();
        }

        $this->parameters = array();
    }

    private function BuildParams($query, $params = null)
    {
        if (!empty($params)) {
            $rawStatement = explode(" ", $query);
            foreach ($rawStatement as $value) {
                if (strtolower($value) == 'in') {
                    return str_replace("(?)", "(" . implode(",", array_fill(0, count($params), "?")) . ")", $query);
                }
            }
        }
        return $query;
    }


    public function query($query, $params = null, $index ='' , $fetchmode = PDO::FETCH_ASSOC)
    {
        $query        = trim($query);
        $rawStatement = explode(" ", $query);
        $this->Init($query, $params);
        $statement = strtolower($rawStatement[0]);
        if ($statement === 'select' || $statement === 'show') {
            $tmpData =  $this->sQuery->fetchAll($fetchmode);
            if (!empty($index)){
                foreach ($tmpData as $k => $v){
                    unset($tmpData[$k]);
                    $tmpData[$v[$index]] = $v;
                }
            }
            return $tmpData;
        } elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
            return $this->sQuery->rowCount();
        } else {
            return NULL;
        }
    }

    /**
        * 批量插入
        *
        * @param $insertTable 插入的表名
        * @param $insertParam 插入的表的键值
        * @param $insertData 　插入的数据(二维数组)
        *
        * @return int(插入的数量)
     */
    public function batchInsert($insertTable='', $insertParam=[], $insertData=[])
    {
        if ( empty($insertTable) || empty($insertParam) || empty($insertData) ) {
            return false; 
        }

        $querySql = "insert into " . $insertTable;
        // 处理插入的字段名称
        $querySql .= " (" . implode(',', $insertParam) . ")";

        // 处理插入的数据的预处理
        $paramCount = count($insertParam);
        $buildPara = substr(str_repeat("?,", $paramCount), 0, -1);
        $buildParaStr = "(" . $buildPara . "),";

        // 获取插入的数量
        $insertCount = count($insertData);
        $willBuild = substr(str_repeat($buildParaStr, $insertCount), 0, -1);
        $querySql .= " values " . $willBuild;

        // 处理插入数据
        $data = [];
        foreach ($insertData as $value) {
            $data = array_merge($data, $value);
        }

        return $this->query($querySql, $data);
    }


    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }


    public function column($query, $params = null)
    {
        $this->Init($query, $params);
        $resultColumn = $this->sQuery->fetchAll(PDO::FETCH_COLUMN);
        $this->rowCount = $this->sQuery->rowCount();
        $this->columnCount = $this->sQuery->columnCount();
        $this->sQuery->closeCursor();
        return $resultColumn;
    }
    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $this->Init($query, $params);
        $resultRow = $this->sQuery->fetch($fetchmode);
        $this->rowCount = $this->sQuery->rowCount();
        $this->columnCount = $this->sQuery->columnCount();
        $this->sQuery->closeCursor();
        return $resultRow;
    }


    public function single($query, $params = null)
    {
        $this->Init($query, $params);
        return $this->sQuery->fetchColumn();
    }


    /**
     * 创建像这样的查询: "IN('a','b')";
     *
     * @access   public
     * @param    mixed      $item_list      列表数组或字符串,如果为字符串时,字符串只接受数字串
     * @param    string   $field_name     字段名称
     * @author   wj
     *
     * @return   string
     */
    public function dbCreateIn($item_list, $field_name = '')
    {
        if (empty($item_list)) {
            return $field_name . " IN ('') ";
        } else {
            if (! is_array($item_list)) {
                $item_list = explode(',', $item_list);
                foreach ($item_list as $k => $v) {
                    $item_list[$k] = intval($v);
                }
            }

            $item_list = array_unique($item_list);
            $item_list_tmp = '';
            foreach ($item_list as $item) {
                if ($item !== '') {
                    $item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
                }
            }
            if (empty($item_list_tmp)) {
                return $field_name . " IN ('') ";
            } else {
                return $field_name . ' IN (' . $item_list_tmp . ') ';
            }
        }
    }



    private function ExceptionLog($message, $sql = "")
    {
        $exception = 'Unhandled Exception. <br />';
        $exception .= $message;
        $exception .= "<br /> You can find the error back in the log.";

        if (!empty($sql)) {
            $message .= "\r\nRaw SQL : " . $sql;
        }
        $this->log->write($message, $this->DBName . md5($this->DBPassword));
        //Prevent search engines to crawl
        header("HTTP/1.1 500 Internal Server Error");
        header("Status: 500 Internal Server Error");
        return $exception;
    }
    
}
