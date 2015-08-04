<?php

class Loader {
    public function __construct() {
    }

    //factory method which establishes the requested controller as an object
    public function createController() {
        //check our requested controller's class file exists and require it if so
        $controllerFile = M. "/controllers/". C . "Controller.php";
        if (file_exists($controllerFile)) {
            require ($controllerFile);
        } else {
            return $this->showError();
        }
        //does the class exist?
        //
        $controllerClass = ucfirst(C) . "Controller";

        if (class_exists($controllerClass)) {
            $parents = class_parents($controllerClass);
            //does the class inherit from the BaseController class?
            if (in_array("BaseController", $parents)) {
                //does the requested class contain the requested action as a method?
                if (method_exists($controllerClass, A)) {
                    return new $controllerClass(A);
                } else {
                    return $this->showError();
                }
            } else {
                return $this->showError();
            }
        } else {
            return $this->showError();
        }
    }

    /**
     * controller 或者 method 找不到的时候，显示出错信息
     *
     * @return [type] [description]
     */
    public function showError() {
        return error();
    }
}
