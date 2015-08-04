<?php
/*
 * Project: Nathan MVC
 * File: /classes/loader.php
 * Purpose: class which maps URL requests to controller object creation
 * Author: Nathan Davison
 */

class Loader {

    private $controllerName;
    private $controllerClass;
    private $action;
    private $module;

    //store the URL request values on object creation
    public function __construct() {
        $this->module = MODULE;
        $this->controllerName = CONTROLLER;
        $this->controllerClass = ucfirst(CONTROLLER) . "Controller";
        $this->action = ACTION;

    }

    //factory method which establishes the requested controller as an object
    public function createController() {
        //check our requested controller's class file exists and require it if so
        $controllerFile ="controllers/" .$this->module . "/" . $this->controllerName . ".php";
        if (file_exists($controllerFile)) {
            require($controllerFile);
        } else {
            require("controllers/home/error.php");
            return new ErrorController("badurl");
        }

        //does the class exist?
        if (class_exists($this->controllerClass)) {
            $parents = class_parents($this->controllerClass);

            //does the class inherit from the BaseController class?
            if (in_array("BaseController",$parents)) {
                //does the requested class contain the requested action as a method?
                if (method_exists($this->controllerClass,$this->action))
                {
                    return new $this->controllerClass($this->action);
                } else {
                    //bad action/method error
                    require("controllers/home/error.php");
                    return new ErrorController("badurl");
                }
            } else {
                //bad controller error
                require("controllers/home/error.php");
                return new ErrorController("badurl");
            }
        } else {
            //bad controller error
            require("controllers/home/error.php");
            return new ErrorController("badurl");
        }
    }
}

