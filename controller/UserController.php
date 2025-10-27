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
        $usuario = $_POST["usuario"];
        $contrasena = $_POST["contrasena"];
        $resultado = $this->model->validarLogin($usuario, $contrasena);
        // $resultado = $this->model->getUserByUsername($usuario);

        if ($resultado && sizeof($resultado) > 0) {
            if ($resultado["cuenta_activa"] == 1) {
                $_SESSION["usuario"] = $resultado;
                header("Location: /TP-Final-PW2/inicio/index");
                exit();    
            }
            $this->renderer->render("login", ["isLogin" => true,"error" => "Cuenta no validada. Por favor, revise su correo."]);
            return;
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
        $nombreCompleto = $_POST["n-completo"];
        $anio = $_POST["anio"];
        $sexo = $_POST["sexo"];
        $pais = $_POST["pais"];
        $ciudad = $_POST["ciudad"];
        $correo = $_POST["correo"];
        $contrasena = $_POST["contrasena"];
        $usuario = $_POST["usuario"];

        $result = $this->model->registerUser(
            $nombreCompleto,
            $anio,
            $sexo,
            $pais,
            $ciudad,
            $correo,
            $contrasena,
            $usuario,
            $this->validacionImagen()
        );

        // Si la función devolvió un string es un mensaje de error (usuario/email ya existe)
        if (is_string($result) && !empty($result)) {
            $this->renderer->render("register", ["isRegister" => true, "error" => $result]);
            return;
        }
        $this->renderer->render("validacionUno", ["isValidation" => true,"success" => "Usuario registrado con éxito. Por favor, inicie sesión."]);
    }

    public function validacionUno(){
        $this->renderer->render("validacionUno", ["isValidation" => true]);
    }

    public function primeraValidacionToken(){
        if (!isset($_POST["email"]) || empty(trim($_POST["email"]))) {
            $this->renderer->render("validacionUno", ["isValidation" => true, "error"  => "Por favor ingrese un correo."]);
            return;
        }

        $email = trim($_POST["email"]);
        $resultado = $this->model->getUserByEMail($email);
        if (empty($resultado)) {
            $this->renderer->render("validacionUno", ["isValidation" => true, "error"=> "Correo no registrado."]);
            return;
        }

        $to = $resultado["email"] ?? null;
        $nombre = $resultado["nombre_completo"] ?? null;
        $token = $resultado["token_validacion"] ?? null;

        if (empty($to) || empty($token)) {
            $this->renderer->render("validacionUno", ["isValidation" => true, "error" => "No se pudo generar el token."]);
            return;
        }

        EmailHelper::enviarToken($to, $nombre, $token);
        $this->renderer->render("validacionDos", ["isValidation" => true]);
        
    }
    public function segundaValidacionToken(){
        $resultado = $this->model->validarToken($_POST["token"]);
        if ($resultado == false) {
            $this->renderer->render("validacionDos", ["isValidation" => true, "error" => "Token inválido."]);
            return;
        }
        $this->renderer->render("login", ["isLogin" => true,"success" => "Cuenta validada con éxito. Por favor, inicie sesión."]);

    }

    public function olvidarContraseña(){
        $this->renderer->render("olvidarContraseña", ["isRecupero" => true]);
    }

    public function recuperarContrasena(){
        $email = $_POST["email"];
        $usuario = $_POST["usuario"];
        $nuevaContrasena = $_POST["nueva-contrasena"];
        $resultado = $this->model->cambiarContrasena(
            $email,
            $usuario,
            $nuevaContrasena
        );

        if ($resultado == false) {
            $this->renderer->render("olvidarContraseña", ["isRecupero" => true,"error" => "Correo o usuario incorrecto."]);
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

