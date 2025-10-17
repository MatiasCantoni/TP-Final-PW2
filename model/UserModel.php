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
    public function registerUser( array $data)
    {
        $sql = "INSERT INTO usuarios (nombre_completo, contrasena, nombre_usuario, anio_nacimiento, sexo, correo_electronico, foto_perfil) VALUES ('{$data['usuario']}', '{$data['contrasena']}', '{$data['n-completo']}', '{$data['anio']}', '{$data['sexo']}', '{$data['correo']}', '{$data['foto']}')";
        $this->conexion->query($sql);
    }
}