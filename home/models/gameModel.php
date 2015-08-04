<?php
/*
 * Project: Nathan MVC
 * File: /models/home.php
 * Purpose: model for the home controller.
 * Author: Nathan Davison
 */

class GameModel extends BaseModel
{

    public function gameList()
    {
        return "I love this game!";
    }

    public function getOnlineNum(){
    	return rand(1000,2000);
    }
}

