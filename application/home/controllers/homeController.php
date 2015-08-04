<?php

class HomeController extends BaseController {
    //add to the parent constructor
    public function __construct($action) {
        parent::__construct($action);
    }
    //default method
    protected function index() {
        echo "<pre>";
        $all_settings = config();
        print_r($all_settings);

        echo "<hr>";
        $db_settings = config("database");
        print_r($db_settings);

        echo "<hr>";
        $db_settings = config("database.opentime");
        print_r($db_settings);


    }

    public function test(){
        print_r(I("aaaaaa","ssbb","md5"));

    }
}
