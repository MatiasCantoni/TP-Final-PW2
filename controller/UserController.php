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
        $this->renderer->render("register");
    }

    public function registerValidation(){
        $this->model->registerUser($_POST["n-completo"], $_POST["anio"],  $_POST["sexo"], "Argentina", "Buenos Aires",  $_POST["correo"], $_POST["contrasena"], $_POST["usuario"],  $_POST["foto"]);
        
        
        // $this->renderer->render("login", ["success" => "Usuario registrado con éxito. Por favor, inicie sesión."]);
    }

    public function redirectToIndex()
    {
        header("Location: /TP-Final-PW2/");
        exit;
    }

}

