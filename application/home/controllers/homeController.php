<?php

class HomeController extends BaseController {

    public $disable_logging=array("test", "foo", "bar");

    public function __construct($action) {
        parent::__construct($action);
    }

    protected function index() {
        $info = array("name"=>"mvc", "info"=>array(11,12,13));
        $this->display($info);

    }

    public function test(){
        $info = array("name"=>"mvc", "info"=>array(11,12,13));
        $this->output($info);
    }
}
