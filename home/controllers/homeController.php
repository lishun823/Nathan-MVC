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
        echo "<pre>";
        $all_settings = config("");
        print_r($all_settings);

        echo "<hr>";
        $user_settings = config("user");
        print_r($user_settings);

        echo "<hr>";
        $username = config("user.name");
        print_r($username);

        echo "<hr>";
        $init = config("user.init_setting");
        print_r($init);

        echo "<hr>";
        $init = config("user.init_setting.gold");
        print_r($init);

    }

    public function test(){
        $msg = $this->model->getMsg();
        echo  $msg . ":::home_index:::";

        $game = new GameModel();
        echo $game->gameList();
    }
}
