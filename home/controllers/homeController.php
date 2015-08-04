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
    }

    public function test(){
        $msg = $this->model->getMsg();
        echo  $msg . ":::home_index:::";

        $game = new GameModel();
        echo $game->gameList();
    }
}
