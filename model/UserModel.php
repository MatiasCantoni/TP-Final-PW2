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
        // Si la consulta devuelve un array de filas, devolver la primera fila asociativa
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return [];
    }

    public function getUserByEMail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = '$email'";
        $resultado = $this->conexion->query($sql);
        if (is_array($resultado) && count($resultado) > 0) {
            return $resultado[0];
        }
        return [];
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
        if ($anio_nacimiento < 1900 || $anio_nacimiento > intval(date("Y") - 5)) {
            $resultado = "El año de nacimiento no es válido.";
            return $resultado;
        }
        $token = bin2hex(random_bytes(16)); // genera un código seguro

        $foto_perfil_db = $foto_perfil ? 'assets/img/uploads/' . $foto_perfil : '';

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

    public function validarToken($token){
        $sql = "SELECT * FROM usuarios WHERE token_validacion = '$token'";
        $resultado = $this->conexion->query($sql);

        if ($resultado == null) {
            return false;
        } else {
            $sqlUpdate = "UPDATE usuarios SET token_validacion = NULL, cuenta_activa = 1
                          WHERE token_validacion = '$token'";
            $this->conexion->query($sqlUpdate);
            return true;
        }

    }

    public function validarLogin($usuario, $contrasena){
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario'";
        $resultado = $this->conexion->query($sql);
        if (is_array($resultado) && count($resultado) > 0) {
            $user = $resultado[0];
            if ($user['tipo_usuario'] == 'admin' || $user['tipo_usuario'] == 'editor') {
                return $user;
            }
            if (password_verify($contrasena, $user['contrasena'])) {
                return $user;
            }
        }
        return null;
    }
    public function cambiarContrasena($correo, $usuario, $contraseniaNueva){
        $sql = "SELECT * FROM usuarios WHERE email = '$correo' AND nombre_usuario = '$usuario'";
        $resultado = $this->conexion->query($sql);
        if ($resultado == null) {
            return false;
        } else {
            $contraseniaHash = password_hash($contraseniaNueva, PASSWORD_DEFAULT);
            $sqlUpdate = "UPDATE usuarios SET contrasena = '$contraseniaHash'
                          WHERE email = '$correo' AND nombre_usuario = '$usuario'";
            $this->conexion->query($sqlUpdate);
            return true;
        }
    }
}