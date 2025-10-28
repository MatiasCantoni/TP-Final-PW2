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
            header("Location: /TP-Final-PW2/user/loginForm");
            exit();
        }

        // Obtener top 50 jugadores
        $topJugadores = $this->model->getTopJugadores(50);
        
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

    public function perfil() {
        if (!isset($_SESSION["usuario"])) {
            header("Location: /TP-Final-PW2/user/loginForm");
            exit();
        }

        $idUsuario = isset($_GET["id"]) ? (int)$_GET["id"] : $_SESSION["usuario"]["id_usuario"];
        
        $usuario = $this->model->getUsuarioById($idUsuario);
        
        if (!$usuario) {
            header("Location: /TP-Final-PW2/ranking/index");
            exit();
        }

        $partidas = $this->model->getPartidasUsuario($idUsuario);
        
        $estadisticas = $this->model->getEstadisticasUsuario($idUsuario);
        
        $posicion = $this->model->getPosicionUsuario($idUsuario);

        $urlPerfil = "http://" . $_SERVER['HTTP_HOST'] . "/TP-Final-PW2/ranking/perfil?id=" . $idUsuario;

        $data = [
            "isPerfil" => true,
            "usuario" => $usuario,
            "partidas" => $partidas,
            "estadisticas" => $estadisticas,
            "posicion" => $posicion,
            "urlPerfil" => $urlPerfil,
            "esPerfilPropio" => $idUsuario == $_SESSION["usuario"]["id_usuario"]
        ];

        $this->renderer->render("perfil", $data);
    }
}