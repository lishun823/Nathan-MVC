<?php

class HomeController extends BaseController {

    public $disable_logging=array("test", "foo", "bar");

    public function __construct($action) {
        parent::__construct($action);
    }

    protected function index() {
        $this->model->getMsg();
    }

    public function test(){
        echo "home_test";
    }
}
