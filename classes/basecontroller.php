<?php
/*
 * Project: Nathan MVC
 * File: /classes/basecontroller.php
 * Purpose: abstract class from which controllers extend
 * Author: Nathan Davison
*/
abstract class BaseController {
    protected $action;
    protected $model;
    public function __construct($action) {
        $this->action = $action;
        $modelFile = M."/models/".C."Model.php";
        if (file_exists($modelFile)){
            include_once($modelFile);
            $modelName = ucfirst(M)."Model";
            $this->model = new $modelName();
        }
    }

    //executes the requested method
    public function executeAction() {
        return $this->{$this->action}();
    }
}
