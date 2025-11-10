<?php

class InicioController{

    private $model;
    private $renderer;
    private $rankingModel;

    public function __construct($model, $rankingModel, $renderer)
    {
        $this->model = $model;
        $this->rankingModel = $rankingModel;
        $this->renderer = $renderer;
    }

    public function base()
    {
        $this->index();
    }
    public function index()
    {
        $usuario = $_SESSION["usuario"]["nombre_usuario"];
        $idUsuario = $_SESSION["usuario"]["id_usuario"];
        $tipoUsuario = $_SESSION["usuario"]["tipo_usuario"];
        if (!isset($usuario)) {
            header("Location: /user/");
            exit();
        }

        $data = $this->model->getUserInfo($usuario);
        // Asegurar que tenemos un array asociativo y no un array de filas
        $userData = is_array($data) && count($data) > 0 ? $data[0] : [];

        if (strtolower($tipoUsuario) === 'editor') {
            $userData["esEditor"] = true;
        } else if (strtolower($tipoUsuario) === 'admin') {
            $userData["esAdmin"] = true;
        }

        $posicion = $this->rankingModel->getPosicionUsuario($idUsuario);
        $userData["isInicio"] = true;
        $userData["posicion"] = $posicion;
        $userData["showNavbar"] = true;
        $this->renderer->render("inicio", $userData);
    }
}