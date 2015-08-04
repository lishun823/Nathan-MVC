<?php

class UserController extends BaseController {
    //add to the parent constructor
    public function __construct($action) {
        parent::__construct($action);
    }
    //default method
    protected function index() {
       echo "account_user_index";
    }

}
