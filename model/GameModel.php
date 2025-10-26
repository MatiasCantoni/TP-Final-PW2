<?php

class GameModel{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function getCategorias(){
        $sql = "SELECT * FROM categoria";
        return $this->conexion->query($sql);
    }

    public function getPregunta($categoriaId){
        $sql = "SELECT * FROM preguntas WHERE categoria_id = $categoriaId ORDER BY RAND() LIMIT 1";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return [];
    }
}