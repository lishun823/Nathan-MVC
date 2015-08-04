<?php

abstract class BaseController {
    protected $action;
    protected $model;
    public function __construct($action) {
        $this->action = $action;
        $this->model = newClass(M.".".C);
    }

    //executes the requested method
    public function executeAction() {
        return $this->{$this->action}();
    }
}
