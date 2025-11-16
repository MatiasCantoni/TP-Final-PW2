<?php

class AdminModel{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function getUsuariosTotales(){
        $sql = "SELECT * FROM usuarios WHERE tipo_usuario = 'jugador'";
        $resultado = $this->conexion->query($sql);
        if (is_array($resultado) && count($resultado) > 0) {
            return count($resultado);
        }
        return null;
    }

    public function getPartidasTotales(){
        $sql = "SELECT * FROM partidas";
        $resultado = $this->conexion->query($sql);
        if (is_array($resultado) && count($resultado) > 0) {
            return count($resultado);
        }
        return null;
    }
    public function getPreguntasTotales(){
        $sql = "SELECT * FROM preguntas where estado = 'aprobada'";
        $resultado = $this->conexion->query($sql);
        if (is_array($resultado) && count($resultado) > 0) {
            return count($resultado);
        }
        return null;
    }

    public function getPreguntasTotalesCreadas(){
        $sql = "SELECT * FROM preguntas";
        $resultado = $this->conexion->query($sql);
        if (is_array($resultado) && count($resultado) > 0) {
            return count($resultado);
        }
        return null;
    }

    public function getEstadisticas($filtro, $tiempo) {

        switch ($filtro) {
            case "sexo": $campo = "sexo"; break;
            case "edad": $campo = "edad"; break;
            case "pais":
            default: $campo = "pais"; break;
        }

        switch ($tiempo) {
            case "dia":
                $condicionTiempo = "fecha_registro >= CURDATE()";
                break;

            case "semana":
                $condicionTiempo = "fecha_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;

            case "anio":
                $condicionTiempo = "fecha_registro >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                break;

            case "mes":
            default:
                $condicionTiempo = "fecha_registro >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
        }

        $sql = "
            SELECT $campo AS etiqueta, COUNT(*) AS cantidad
            FROM usuarios
            WHERE $condicionTiempo
            GROUP BY $campo
            ORDER BY cantidad DESC
        ";

        $resultado = $this->conexion->query($sql);
        return $resultado;
    }
}