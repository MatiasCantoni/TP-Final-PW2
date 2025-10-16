<?php

class LoginModel
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

    public function registerUser($user, $password)
    {
        $sql = "INSERT INTO usuarios (nombre_usuario, contrasena) VALUES ('$user', '$password')";
        $this->conexion->query($sql);
    }
}