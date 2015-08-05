<?php

class BaseModel {
    protected $db;
    public function __construct() {
    	$this->db = newClass("classes/mysql");
    }

}
