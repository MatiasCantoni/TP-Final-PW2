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

    public function getPreguntaRandom($categoria){
        $sql = "SELECT * FROM preguntas WHERE categoria = '$categoria' ORDER BY RAND() LIMIT 1";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return [];
    }
    public function getRespuestaCorrecta($idPregunta){
        $sql = "SELECT respuesta_correcta FROM preguntas WHERE id_pregunta = $idPregunta";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            return $result[0]['respuesta_correcta'];
        }
        return null;
    }

    public function verificarRespuesta($idPregunta, $idUsuario, $opcionSeleccionada){
        $datos = [];
        $correcta = $this->getRespuestaCorrecta($idPregunta);
        if ($correcta !== null) {
            $esCorrecta = $correcta === $opcionSeleccionada;
            $datos['correcta'] = $esCorrecta;
            if($esCorrecta){
                $sql = "UPDATE usuarios SET puntaje_partida = puntaje_partida + 10 WHERE id_usuario = $idUsuario";
                $this->conexion->query($sql);
                $sql = "SELECT puntaje_partida FROM usuarios WHERE id_usuario = $idUsuario";
                $result_partida = $this->conexion->query($sql);
                $puntaje_partida = 0;
                if (is_array($result_partida) && count($result_partida) > 0) {
                    $puntaje_partida = (int) $result_partida[0]['puntaje_partida'];
                    $datos['puntajePartida'] = $puntaje_partida;
                }
                
                $sql = "SELECT puntaje_total FROM usuarios WHERE id_usuario = $idUsuario";
                $result_total = $this->conexion->query($sql);
                $puntaje_total = 0;
                if (is_array($result_total) && count($result_total) > 0) {
                    $puntaje_total = (int) $result_total[0]['puntaje_total'];
                }
                
                if ($puntaje_partida > $puntaje_total) {
                    $sql = "UPDATE usuarios SET puntaje_total = $puntaje_partida WHERE id_usuario = $idUsuario";
                    $this->conexion->query($sql);
                }
            } else {
                // VAMOS A TENER QUE REFACTORIZAR ESTO POR REPETIDO
                $sql = "SELECT puntaje_partida FROM usuarios WHERE id_usuario = $idUsuario";
                $result_partida = $this->conexion->query($sql);
                $puntaje_partida = 0;
                if (is_array($result_partida) && count($result_partida) > 0) {
                    $puntaje_partida = (int) $result_partida[0]['puntaje_partida'];
                    $datos['puntajePartida'] = $puntaje_partida;
                }
                $sql = "UPDATE usuarios SET puntaje_partida = 0 WHERE id_usuario = $idUsuario";
                $this->conexion->query($sql);
            }
            return $datos;
        }
        return null;
    }
    public function getPuntajePartida($id) {
        $sql = "SELECT puntaje_partida FROM usuarios WHERE id_usuario = $id";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            return (int) $result[0]['puntaje_partida'];
        }
        return 0;
    }

    public function getPuntajeTotal($id) {
        $sql = "SELECT puntaje_total FROM usuarios WHERE id_usuario = $id";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            return (int) $result[0]['puntaje_total'];
        }
        return 0;
    }

    public function getPreguntaById($idPregunta){
        $sql = "SELECT * FROM preguntas WHERE id_pregunta = $idPregunta";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return null;
    }
}