<?php

class GameController extends BaseController {
    //add to the parent constructor
    public function __construct($action) {
        parent::__construct($action);
    }
    //default method
    protected function index() {
        $user = newClass("account.user");
        echo $user->getName();
        echo  ":::";
    }

}

