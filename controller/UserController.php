<?php

class UserController
{
    private $model;
    private $renderer;

    public function __construct($model, $renderer)
    {
        $this->model = $model;
        $this->renderer = $renderer;
    }

    public function base()
    {
        $this->loginForm();
    }

    public function loginForm()
    {
        $this->renderer->render("login");
    }

    public function login()
    {
        $resultado = $this->model->getUserWith($_POST["usuario"], $_POST["contrasena"]);

        if (sizeof($resultado) > 0) {
            $_SESSION["usuario"] = $_POST["usuario"];
            include_once("vista/inicioVista.mustache");
        } else {
            $this->renderer->render("login", ["error" => "Usuario o clave incorrecta"]);
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirectToIndex();
    }
    
    public function register()
    {
        $this->renderer->render("register", ["isRegister" => true]);
    }

    public function registerValidation(){
        $result = $this->model->registerUser(
            $_POST["n-completo"],
            $_POST["anio"],
            $_POST["sexo"],
            $_POST["pais"],
            $_POST["ciudad"],
            $_POST["correo"],
            $_POST["contrasena"],
            $_POST["usuario"],
            $_POST["foto"]
        );

        // Si la función devolvió un string es un mensaje de error (usuario/email ya existe)
        if (is_string($result) && !empty($result)) {
            $this->renderer->render("register", ["isRegister" => true, "error" => $result]);
            return;
        }

        $this->renderer->render("login", ["success" => "Usuario registrado con éxito. Por favor, inicie sesión."]);
    }

    public function redirectToIndex()
    {
        header("Location: /TP-Final-PW2/");
        exit;
    }

}

