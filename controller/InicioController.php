<?php

class InicioController{

    private $model;
    private $renderer;
    
    public function __construct($model, $renderer)
    {
        $this->model = $model;
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
        if (!isset($usuario)) {
            header("Location: /user/");
            exit();
        }
        $posicion = $this->model->getPosicionUsuario($idUsuario);
        $data = $this->model->getUserInfo($usuario);
        // Asegurar que tenemos un array asociativo y no un array de filas
        $userData = is_array($data) && count($data) > 0 ? $data[0] : [];
        $userData["isInicio"] = true;
        $userData["posicion"] = $posicion;
        $this->renderer->render("inicio", $userData);
    }
}