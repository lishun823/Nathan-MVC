<?php
class Mysql {
    // 当前SQL指令
    private $lastSql = '';
    //  insert_id
    private $lastInsID = null;

    // 返回或者影响记录数
    private $numRows = 0;
    // 返回字段数
    private $numCols = 0;

    // 错误信息
    private $lastError = '';

    // 当前连接ID
    private $conn = null;
    // 当前连接ID
    private $cfgid = null;
    // 是否已经连接数据库
    private $connected = false;

    private $version = "";

    private $lastTimeUsed = 0;

    public function getInfo($key){
        return property_exists($this, $key) ? $this->$key : null;
    }

    private function getMasterConnect() {
        return $this->initConnect(0);
    }

    private function getSlaveConnect(){
        $cfg = config("mysql");
        $rnd = count($cfg)>1 ? rand(1, count($cfg)-1) : 0;
        return $this->initConnect($rnd);
    }

    private function initConnect($cfgid) {
        static $conns = array();

        if (!isset($conns[$cfgid]) || !is_resource($conns[$cfgid])){
            $cfg = config("mysql.$cfgid");
            $host = $cfg["host"] . ":" . $cfg["port"];
            if ($cfg["pconnect"]) {
                $conns[$cfgid] = mysql_pconnect($host, $cfg["user"], $cfg["pass"], 131072);
            } else {
                $conns[$cfgid] = mysql_connect($host, $cfg["user"], $cfg["pass"], true, 131072);
                //echo "connnect use config[{$cfgid}]\r\n";
            }
            if (!mysql_select_db($cfg['dbname'], $conns[$cfgid])) return $this->error();

            $dbVersion = mysql_get_server_info($conns[$cfgid]);
            $this->version = $dbVersion;
            mysql_query("SET NAMES '" . $cfg['charset'] . "'", $conns[$cfgid]);
            if ($dbVersion > '5.0.1') mysql_query("SET sql_mode=''", $conns[$cfgid]);
        }

        $this->connected = true;
        $this->conn =$conns[$cfgid];

        $this->cfgid = $cfgid;
        return $conns[$cfgid];
    }

    public function query($sql = "", $keyField ="", $compact_result=false) {
        $link = $this->getSlaveConnect();

        $this->lastSql = $sql;
        $time_start = time();
        $query = mysql_query($sql, $link);
        if (!$query) return $this->error();
        $this->recordSlowQuery($time_start);

        $result = array();
        $result_array = array();
        $result_assoc = array();
        $rowCount = 0;
        $fieldCount =0;
        while($row=mysql_fetch_assoc($query)){
            $rowCount++;
            if (is_string($keyField) && trim($keyField)!==""){
                $result[$row[$keyField]]=$row;
            }else{
                $result[]=$row;
            }
            if ($rowCount==1) $fieldCount = count($row);
            if ($fieldCount==1 && $compact_result) $result_array[]=current($row);
        }
        $this->numRows=$rowCount;
        $this->numCols=$fieldCount;

        if ($rowCount==1 && $fieldCount==1 && $compact_result) return current($result_array);
        if ($rowCount==1 && $compact_result) return $result[0];
        if ($fieldCount==1 && $compact_result) return $result_array;

        return $result;
    }



    public function exec($sql = "", $keyField ="", $compact_result=false) {
        $link = $this->getMasterConnect();

        $this->lastSql = $sql;
        $time_start = time();
        $query = mysql_query($sql, $link);
        if (!$query) return $this->error();
        $this->recordSlowQuery($time_start);

        $this->lastInsID =mysql_insert_id($link);
        $rowCount = mysql_affected_rows($link);
        $this->numRows=$rowCount;
        return $rowCount;
    }

    private function recordSlowQuery($start){
        $time_used = time() - $start;
        $this->lastTimeUsed = $time_used;
        $n = '';
        if ($time_used>20){
            $n="20";
        } elseif ($time_used>10){
            $n="10";
        }elseif ($time_used>5){
            $n='05';
        }
        if ($n!=="") log_message("mysql/slow/{$month}_{$n}s.log", $msg);
    }

    public function error($msg=""){
        if ($msg=="") $msg=mysql_error($this->conn);
        $this->lastError = $msg;
        $month = date("Ym");
        log_message("mysql/error/error_{$month}.log", $msg);
        E($msg);
        return false;
    }

    public function __destruct() {
        //echo "end";
    }
}
