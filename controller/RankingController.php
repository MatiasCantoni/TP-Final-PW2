<?php

class RankingController {
    private $model;
    private $renderer;

    public function __construct($model, $renderer) {
        $this->model = $model;
        $this->renderer = $renderer;
    }

    public function base() {
        $this->index();
    }

    public function index() {
        if (!isset($_SESSION["usuario"])) {
            header("Location: /user/loginForm");
            exit();
        }

        $topJugadores = $this->model->getTopJugadores(10);
        
        $idUsuarioActual = $_SESSION["usuario"]["id_usuario"];
        $posicionUsuario = $this->model->getPosicionUsuario($idUsuarioActual);
        
        $data = [
            "isRanking" => true,
            "jugadores" => $topJugadores,
            "posicionUsuario" => $posicionUsuario,
            "idUsuarioActual" => $idUsuarioActual
        ];

        $this->renderer->render("ranking", $data);
    }
}