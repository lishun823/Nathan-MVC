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
        require ("models/home.php");
        $this->model = new HomeModel();
    }
    //default method
    protected function index() {
        $this->view->output($this->model->index());
    }

    public function test(){
        echo 'test';
    }
}
