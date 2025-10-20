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
        $this->renderer->render("login", ["isLogin" => true]);
    }

    public function login()
    {
        
        $resultado = $this->model->getUserByUsername($_POST["usuario"]);

        if (sizeof($resultado) > 0 && password_verify($_POST["contrasena"], $resultado["contrasena"])) {
            $_SESSION["usuario"] = $_POST["usuario"];
            $this->renderer->render("inicio");
        } else {
            $this->renderer->render("login", ["isLogin" => true,"error" => "Usuario o clave incorrecta"]);
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
            $this->validacionImagen()
        );

        // Si la función devolvió un string es un mensaje de error (usuario/email ya existe)
        if (is_string($result) && !empty($result)) {
            $this->renderer->render("register", ["isRegister" => true, "error" => $result]);
            return;
        }
        $this->renderer->render("validacionUno", ["isLogin" => true,"success" => "Usuario registrado con éxito. Por favor, inicie sesión."]);
    }

    public function validacionUno(){
        $this->renderer->render("validacionUno", ["isLogin" => true]);
    }

    public function primeraValidacionToken(){
        if (!isset($_POST["email"]) || empty(trim($_POST["email"]))) {
            $this->renderer->render("validacionUno", ["error" => "Por favor ingrese un correo."]);
            return;
        }

        $email = trim($_POST["email"]);
        $resultado = $this->model->getUserByEMail($email);
        if (empty($resultado)) {
            $this->renderer->render("validacionUno", ["error" => "Correo no registrado."]);
            return;
        }

        $to = $resultado["email"] ?? null;
        $nombre = $resultado["nombre_completo"] ?? null;
        $token = $resultado["token_validacion"] ?? null;

        if (empty($to) || empty($token)) {
            $this->renderer->render("validacionUno", ["error" => "No se pudo generar el token."]);
            return;
        }

        EmailHelper::enviarToken($to, $nombre, $token);
        $this->renderer->render("validacionDos");
        
    }
    public function segundaValidacionToken(){
        $resultado = $this->model->validarToken($_POST["token"]);
        if ($resultado == false) {
            $this->renderer->render("validacionDos", ["error" => "Token inválido."]);
            return;
        }
        $this->renderer->render("login", ["isLogin" => true,"success" => "Cuenta validada con éxito. Por favor, inicie sesión."]);

    }

    public function olvidarContraseña(){
        $this->renderer->render("olvidarContraseña", ["isLogin" => true]);
    }

    public function recuperarContrasena(){
        $resultado = $this->model->cambiarContrasena(
            $_POST["email"],
            $_POST["usuario"],
            $_POST["nueva-contrasena"]
        );

        if ($resultado == false) {
            $this->renderer->render("olvidarContraseña", ["isLogin" => true,"error" => "Correo o usuario incorrecto."]);
            return;
        }
        $this->renderer->render("login", ["isLogin" => true,"success" => "Contraseña actualizada con éxito. Por favor, inicie sesión."]);
    }
    public function redirectToIndex()
    {
        header("Location: /TP-Final-PW2/");
        exit;
    }

    public function validacionImagen(){
        $fotoNombre = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $dirSubida = __DIR__ . '/../assets/img/uploads';
            if (!is_dir($dirSubida)) {
                mkdir($dirSubida, 0755, true);
            }
            $nombreTemporal = $_FILES['foto']['tmp_name'];
            $nombreOrig = basename($_FILES['foto']['name']);
            $ext = pathinfo($nombreOrig, PATHINFO_EXTENSION);
            $fotoNombre = uniqid('user_') . '.' . $ext;
            $dest = $dirSubida . '/' . $fotoNombre;
            if (!move_uploaded_file($nombreTemporal, $dest)) {
                $fotoNombre = null;
            }
        }
        return $fotoNombre;
    }

}

