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
}