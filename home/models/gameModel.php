<?php

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

