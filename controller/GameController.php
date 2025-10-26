<?php

class GameController{
    private $gameModel;
    private $renderer;

    public function __construct($gameModel, $renderer)
    {
        $this->gameModel = $gameModel;
        $this->renderer = $renderer;
    }

    public function singleplayer(){
        
    }
}