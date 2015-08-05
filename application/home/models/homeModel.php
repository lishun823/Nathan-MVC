<?php
class HomeModel extends BaseModel {
    public function __construct() {
    	parent::__construct();
    }

    public function getMsg() {
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";
    	echo $ret=$this->db->query("select name from tbl_test limit 1","", true); echo "\r\n";

    	$name = uniqid();
    	$money = rand(2000,9999);
    	$sql = "insert into tbl_test values(null, '$name', $money, now())\r\n";
    	echo '@@'.$this->db->exec($sql)."\r\n";
    	echo '##'.$this->db->getInfo("lastSql");
    	echo '::'.$this->db->getinfo("lastInsID");

    }
}
