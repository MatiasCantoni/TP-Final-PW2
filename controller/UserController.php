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
        // Manejar subida de foto (mínimo)
        $fotoNombre = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $uploadsDir = __DIR__ . '/../assets/img/uploads';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            $tmpName = $_FILES['foto']['tmp_name'];
            $origName = basename($_FILES['foto']['name']);
            $ext = pathinfo($origName, PATHINFO_EXTENSION);
            $fotoNombre = uniqid('user_') . '.' . $ext;
            $dest = $uploadsDir . '/' . $fotoNombre;
            if (!move_uploaded_file($tmpName, $dest)) {
                $fotoNombre = null;
            }
        }

        $result = $this->model->registerUser(
            $_POST["n-completo"],
            $_POST["anio"],
            $_POST["sexo"],
            $_POST["pais"],
            $_POST["ciudad"],
            $_POST["correo"],
            $_POST["contrasena"],
            $_POST["usuario"],
            $fotoNombre
        );

        // Si la función devolvió un string es un mensaje de error (usuario/email ya existe)
        if (is_string($result) && !empty($result)) {
            $this->renderer->render("register", ["isRegister" => true, "error" => $result]);
            return;
        }
        $this->renderer->render("login", ["isLogin" => true,"success" => "Usuario registrado con éxito. Por favor, inicie sesión."]);
    }

    public function redirectToIndex()
    {
        header("Location: /TP-Final-PW2/");
        exit;
    }

}

