<?php
/*
 * Project: Nathan MVC
 * File: /controllers/home.php
 * Purpose: controller for the home of the app.
 * Author: Nathan Davison
*/
class HomeController extends BaseController {
    //add to the parent constructor
    public function __construct($action) {
        parent::__construct($action);
    }
    //default method
    protected function index() {
        echo  "home_index";
    }

    public function test(){
        echo 'test';
    }
}
