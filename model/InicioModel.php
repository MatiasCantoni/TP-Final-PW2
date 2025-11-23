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

    public function validacionPreguntaSugerida($idUsuario, $texto, $a, $b, $c, $d, $respuesta, $categoriaNombre) {
        // Buscar el id_categoria según el nombre
        $sqlCategoria = "SELECT id_categoria FROM categorias WHERE nombre = '$categoriaNombre'";
        $resultadoCategoria = $this->conexion->query($sqlCategoria);

        if (!$resultadoCategoria || count($resultadoCategoria) == 0) {
            echo "Error: la categoría '$categoriaNombre' no existe en la base de datos.";
            return;
        }

        $idCategoria = $resultadoCategoria[0]['id_categoria'];

        // Insertar la pregunta usando id_categoria
        $sql = "INSERT INTO preguntas 
                (texto, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, id_categoria, dificultad, id_creador, estado)
                VALUES 
                ('$texto', '$a', '$b', '$c', '$d', '$respuesta', '$idCategoria', 'facil', '$idUsuario', 'pendiente')";

        $this->conexion->query($sql);
    }

}