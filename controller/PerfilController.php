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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
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
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $urlPerfil = "http://" . $host . "/perfil/show?id=" . $idUsuario;

        $esPerfilPropio = $idUsuario == $_SESSION["usuario"]["id_usuario"];
        
        if ($esPerfilPropio) {
            $usuario['contrasena_oculta'] = str_repeat('*', 10);
        }

        $sexoMasculino = $usuario['sexo'] == 'Masculino';
        $sexoFemenino = $usuario['sexo'] == 'Femenino';
        $sexoOtro = $usuario['sexo'] == 'Prefiero no cargarlo';

        $data = [
            "isPerfil" => true,
            "usuario" => $usuario,
            "partidas" => $partidas,
            "estadisticas" => $estadisticas,
            "posicion" => $posicion,
            "urlPerfil" => $urlPerfil,
            "esPerfilPropio" => $esPerfilPropio,
            "showNavbar" => true,
            "sexoMasculino" => $sexoMasculino,
            "sexoFemenino" => $sexoFemenino,
            "sexoOtro" => $sexoOtro
        ];

        $this->renderer->render("perfil", $data);
    }

    public function actualizar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario'])) {
            $id = $_POST['id_usuario'];
            if ($id == $_SESSION['usuario']['id_usuario']) {
                $this->perfilModel->actualizarUsuario(
                    $id,
                    $_POST['nombre_completo'],
                    $_POST['anio_nacimiento'],
                    $_POST['sexo'],
                    $_POST['pais'],
                    $_POST['ciudad']
                );
                $_SESSION['usuario']['nombre_completo'] = $_POST['nombre_completo'];
                $_SESSION['usuario']['pais'] = $_POST['pais'];
                $_SESSION['usuario']['ciudad'] = $_POST['ciudad'];
            }
        }
        header("Location: /perfil/show");
        exit();
    }
}