<?php

class AdminController{

    private $model;
    private $renderer;

    public function __construct($model,$renderer)
    {
        $this->model = $model;
        $this->renderer = $renderer;
    }

    public function base(){
        AuthHelper::checkRole("admin");

        $usuariosTotales = $this->model->getUsuariosTotales();
        $partidasJugadasTotales = $this->model->getPartidasTotales();
        $preguntasTotales = $this->model->getPreguntasTotales(); 
        $preguntasTotalesCreadas = $this->model->getPreguntasTotalesCreadas();

        
        
        $this->renderer->render("admin",
        ["jugadoresTotales" => $usuariosTotales,
        "partidasTotales" => $partidasJugadasTotales,
        "preguntasTotales" => $preguntasTotales,
        "preguntasCreadasTotales" => $preguntasTotalesCreadas,
        "showNavbar" => true,
        "isAdmin" => true]);
    }

    public function getEstadisticas(){
        $filtro = $_GET["filtro"] ?? 'pais';
        $tiempo = $_GET["tiempo"] ?? 'mes';

        header('Content-Type: application/json');

        echo json_encode(
            $this->model->getEstadisticas($filtro, $tiempo));
        exit();
    }

    public function darPermisos(){
        AuthHelper::checkRole("admin");

        $nombreUsuario = $_GET["usuario"];

        header('Content-Type: application/json');

        echo json_encode(
            $this->model->darPermisos($nombreUsuario));
        exit();
    }
    public function quitarPermisos(){
        AuthHelper::checkRole("admin");
        $nombreUsuario = $_GET["usuario"];

        header('Content-Type: application/json');

        echo json_encode(
            $this->model->quitarPermisos($nombreUsuario));
        exit();
    }
}   