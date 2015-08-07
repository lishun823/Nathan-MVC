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


    public function executeAction() {
        if (method_exists($this, "check") && $this->check() === false) return;
        return $this->{$this->action}();
    }

    public function display($data=array(), $viewfile="", $template="default"){
        $templateFile  = APP_PATH. "/application/". M . "/views/{$template}.php";
        if ($viewfile=="") $viewfile = APP_PATH. "/application/".M . "/views/". C."_".A.".php";
        $this->viewfile = $viewfile;

        $charset = C("app.html_charset", "utf-8");
        header("Content-type: text/html; charset={$charset}");

        if (file_exists($viewfile)) {
            extract($data);
            if ($template!=="" && file_exists($templateFile)){
                require($templateFile);
            }else{
                require($viewfile);
            }
        }else{
            E("cannot found viewfile: $viewfile");
        }
    }

    public function output($data=array(), $viewfile=""){
        $this->display($data, $viewfile, "");
    }

    public function json($data=array()){
        $charset = C("app.html_charset", "utf-8");
        header("Content-type: application/json; charset={$charset}");
        $jsonstr = json_encode($data);

        //测试环境， json输出中文汉字，便于调试。
        if (IS_WIN || I("get.utf8")=="yes") $jsonstr=utf8json($jsonstr);
        echo $jsonstr;
    }

}


