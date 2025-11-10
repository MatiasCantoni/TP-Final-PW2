<?php

class InicioModel{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function getUserInfo($usuario) {
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario'";
        $resultado = $this->conexion->query($sql);
        return $resultado;
    }
    public function getPosicionUsuario($idUsuario) {
        $sql = "SELECT COUNT(*) + 1 as posicion
                FROM usuarios
                WHERE puntaje_total > (
                    SELECT puntaje_total 
                    FROM usuarios 
                    WHERE id_usuario = $idUsuario
                )
                AND cuenta_activa = 1";
        
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            return (int)$result[0]['posicion'];
        }
        return null;
    }
}