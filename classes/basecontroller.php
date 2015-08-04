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
    protected $view;
    public function __construct($action) {
        $this->action = $action;
        echo $modelFile = "models/".MODULE."/".CONTROLLER."Model.php";
        if (file_exists($modelFile)){
            echo $modelName = ucwords(MODULE)."Model";
            //$this->model = new HomeModel();
        }
    }

    //executes the requested method
    public function executeAction() {
        return $this->{$this->action}();
    }
}
