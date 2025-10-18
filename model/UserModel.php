<?php

class UserModel
{

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function getUserWith($user, $password)
    {
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$user' AND contrasena = '$password'";
        $result = $this->conexion->query($sql);
        return $result ?? [];
    }
    // usar los datos del controller para guardarlos en las base de datos
    public function registerUser($nombre_completo, $anio_nacimiento, $sexo, $pais, $ciudad, $email, $contrasena, $nombre_usuario, $foto_perfil)
    {
        $sql = "select * from usuarios where nombre_usuario = '$nombre_usuario'";
        $result = $this->conexion->query($sql);
        $sql2 = "select * from usuarios where email = '$email'";
        $result2 = $this->conexion->query($sql2);
        if ($result != null) {
            $resultado = "El nombre de usuario ya existe.";
            return $resultado;
        }
        if ($result2 != null) {
            $resultado = "El correo ya existe.";
            return $resultado;
        }

        $token = bin2hex(random_bytes(16)); // genera un código seguro
        $sql = "INSERT INTO usuarios (nombre_completo, anio_nacimiento, sexo, pais, ciudad, email, contrasena, nombre_usuario, foto_perfil, token_validacion) VALUES (
            '$nombre_completo',
            '$anio_nacimiento',
            '$sexo',
            '$pais',
            '$ciudad',
            '$email',
            '$contrasena',
            '$nombre_usuario',
            '$foto_perfil',
            '$token'
        )";
        $this->conexion->query($sql);
    }
}