<?php

class GameModel{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function getCategorias(){
        return ['Historia', 'Ciencia', 'Deportes', 'Arte', 'Geografía', 'Entretenimiento'];
    }

    public function getPregunta($categoria){
        $sql = "SELECT * FROM preguntas WHERE categoria = '$categoria' ORDER BY RAND() LIMIT 1";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return [];
    }
}