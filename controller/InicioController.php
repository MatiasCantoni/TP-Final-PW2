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
        } else if (strtolower($tipoUsuario) === "jugador") {
            $userData["esJugador"] = true;
        }

        $posicion = $this->model->getPosicionUsuario($idUsuario);
        $userData["isInicio"] = true;
        $userData["posicion"] = $posicion;
        $userData["showNavbar"] = true;
        $this->renderer->render("inicio", $userData);
    }

    public function sugerirPregunta(){
        $idUsuario = $_SESSION["usuario"]["id_usuario"];

        $datos["idUsuario"] = $idUsuario;
        $datos["showNavbar"] = true;

        $this->renderer->render("sugerirPregunta", $datos);
    }
    public function enviarPreguntaSugerida(){
        $idUsuario = $_POST["id_usuario"];
        $texto = $_POST["texto"];
        $opcion_a = $_POST["opcion_a"];
        $opcion_b = $_POST["opcion_b"];
        $opcion_c = $_POST["opcion_c"];
        $opcion_d = $_POST["opcion_d"];
        $correcta = $_POST["correcta"];
        $categoria = $_POST["categoria"];

        $this->model->validacionPreguntaSugerida(
            $idUsuario, $texto, $opcion_a, $opcion_b, $opcion_c, $opcion_d, $correcta, $categoria);

        $this->renderer->render("sugerirPregunta", ["exito" => "Pregunta enviada con exito!"]);
    }
}