<?php

abstract class BaseController {
    protected $action;
    protected $model;
    protected $viewfile;
    public $disable_logging=array();

    public function __construct($action) {
        $this->action = $action;
        $this->model = newClass(M.".".C);
    }

    //executes the requested method
    public function executeAction() {
        return $this->{$this->action}();
    }

    public function display($data=array(), $viewfile="", $template="default"){
        $templateFile  = APP_PATH. "/application/". M . "/views/{$template}.php";
        if ($viewfile=="") $viewfile = APP_PATH. "/application/".M . "/views/". C."_".A.".php";
        $this->viewfile = $viewfile;
        if (file_exists($viewfile)) {
            extract($data);
            unset($data);
            if (file_exists($templateFile)){
                require($templateFile);
            }else{
                require($viewfile);
            }
        }else{
            E("cannot found viewfile: $viewfile");
        }
    }

    public function output($data=array(), $viewfile=""){
        $this->display($data, $viewfile, "..");
    }

}


