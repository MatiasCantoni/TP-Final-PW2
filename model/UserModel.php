<?php

class UserModel
{

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function getUserByUsername($user)
    {
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$user'";
        $result = $this->conexion->query($sql);
        return $result ?? [];
    }

    public function registerUser($nombre_completo, $anio_nacimiento, $sexo, $pais, $ciudad, $email, $contrasena, $nombre_usuario, $foto_perfil)
    {
        $contraseniaHash = password_hash($contrasena, PASSWORD_DEFAULT);

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
        // Si se pasó un nombre de archivo, guardamos la ruta relativa dentro de assets
        if ($foto_perfil) {
            $foto_perfil_db = 'assets/img/uploads/' . $foto_perfil;
        } else {
            $foto_perfil_db = '';
        }

        $sql = "INSERT INTO usuarios (nombre_completo, anio_nacimiento, sexo, pais, ciudad, email, contrasena, nombre_usuario, foto_perfil, token_validacion) VALUES (
            '$nombre_completo',
            '$anio_nacimiento',
            '$sexo',
            '$pais',
            '$ciudad',
            '$email',
            '$contraseniaHash',
            '$nombre_usuario',
            '$foto_perfil_db',
            '$token'
        )";
        $this->conexion->query($sql);
    }
}