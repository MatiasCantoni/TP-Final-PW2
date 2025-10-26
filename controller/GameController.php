<?php

class GameController{
    private $model;
    private $renderer;

    public function __construct($model, $renderer)
    {
        $this->model = $model;
        $this->renderer = $renderer;
    }

    // Muestra la ruleta
    public function singleplayer(){
        if (!isset($_SESSION["usuario"])) {
            header("Location: index.php?controller=User&method=loginForm");
            exit();
        }

        $categorias = $this->model->getCategorias();
        $this->renderer->render("singleplayer", ["categorias" => $categorias]);
        
    }

    public function pregunta(){
        $categorias = $_GET["categorias"];
        $pregunta = $this->model->getPregunta($categorias);
        $this->renderer->render("pregunta", ["pregunta" => $pregunta]);
    }

    public function responder(){

    }
}