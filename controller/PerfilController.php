<?php

class PerfilController {
    private $perfilModel;
    private $rankingModel;
    private $renderer;

    public function __construct($perfilModel, $rankingModel, $renderer) {
        $this->perfilModel = $perfilModel;
        $this->rankingModel = $rankingModel;
        $this->renderer = $renderer;
    }

    public function base() {
        $this->show();
    }
    
    public function show() {
        if (!isset($_SESSION["usuario"])) {
            header("Location: /user/loginForm");
            exit();
        }

        $idUsuario = isset($_GET["id"]) ? (int)$_GET["id"] : $_SESSION["usuario"]["id_usuario"];
        
        $usuario = $this->perfilModel->getUsuarioById($idUsuario);
        
        if (!$usuario) {
            header("Location: /ranking/");
            exit();
        }

        $partidas = $this->perfilModel->getPartidasUsuario($idUsuario);
        $estadisticas = $this->perfilModel->getEstadisticasUsuario($idUsuario);
        
        $posicion = $this->rankingModel->getPosicionUsuario($idUsuario);

        $urlPerfil = "http://" . $_SERVER['HTTP_HOST'] . "/perfil/show?id=" . $idUsuario;

        $esPerfilPropio = $idUsuario == $_SESSION["usuario"]["id_usuario"];
        
        if ($esPerfilPropio) {
            $usuario['contrasena_oculta'] = str_repeat('*', 10);
        }

        $data = [
            "isPerfil" => true,
            "usuario" => $usuario,
            "partidas" => $partidas,
            "estadisticas" => $estadisticas,
            "posicion" => $posicion,
            "urlPerfil" => $urlPerfil,
            "esPerfilPropio" => $esPerfilPropio,
            "showNavbar" => true
        ];

        $this->renderer->render("perfil", $data);
    }
}