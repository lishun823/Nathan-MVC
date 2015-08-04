<?php

abstract class BaseController {
    protected $action;
    protected $model;
    public function __construct($action) {
        $this->action = $action;
        $modelFile = M."/models/".C."Model.php";
        if (file_exists($modelFile)){
            $modelName = ucfirst(C)."Model";
            $this->model = new $modelName();
        }
    }

    //executes the requested method
    public function executeAction() {
        return $this->{$this->action}();
    }
}
