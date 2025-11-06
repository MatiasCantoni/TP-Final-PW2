<?php

class GameModel{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function getCategorias(){
        return ['Historia', 'Ciencia', 'Deportes', 'Arte', 'Geografia', 'Entretenimiento'];
    }

    public function getPreguntaRandom($categoria, $usuario){

        $sql = "SELECT * FROM preguntas WHERE categoria = '$categoria' AND estado = 'aprobada' AND 
                id_pregunta NOT IN (SELECT id_pregunta FROM preguntas_respondidas WHERE id_usuario = $usuario) ORDER BY RAND() LIMIT 1";
        $result = $this->conexion->query($sql);

        if (is_array($result) && count($result) > 0) {
            $this->agregarPreguntaRespondida($result[0]["id_pregunta"], $usuario);
            
            // Sanitizar los campos de texto para asegurar codificación correcta
            // $result[0]['texto'] = htmlspecialchars($result[0]['texto'], ENT_QUOTES, 'UTF-8');
            // $result[0]['opcion_a'] = htmlspecialchars($result[0]['opcion_a'], ENT_QUOTES, 'UTF-8');
            // $result[0]['opcion_b'] = htmlspecialchars($result[0]['opcion_b'], ENT_QUOTES, 'UTF-8');
            // $result[0]['opcion_c'] = htmlspecialchars($result[0]['opcion_c'], ENT_QUOTES, 'UTF-8');
            // $result[0]['opcion_d'] = htmlspecialchars($result[0]['opcion_d'], ENT_QUOTES, 'UTF-8');
            
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

    public function verificarRespuesta($idPregunta, $idUsuario, $opcionSeleccionada, $tiempo_terminado = ''){
        $datos = [];
        
        // Validar que tengamos un id_pregunta válido
        if (empty($idPregunta) || !is_numeric($idPregunta)) {
            error_log("Error: id_pregunta inválido o vacío: " . var_export($idPregunta, true));
            return null;
        }
        
        $correcta = $this->getRespuestaCorrecta($idPregunta);
        
        if ($correcta !== null) {
            // Si el tiempo se acabó, la respuesta es incorrecta automáticamente
            if ($tiempo_terminado === '1') {
                $esCorrecta = false;
            } else {
                $esCorrecta = $correcta === $opcionSeleccionada;
            }
            $datos['correcta'] = $esCorrecta;
            
            // Obtener o crear partida activa
            $idPartida = $this->getOrCreatePartidaActiva($idUsuario);
            
            // Registrar respuesta en la partida
            $this->registrarRespuestaPartida($idPartida, $idPregunta, $idUsuario, $opcionSeleccionada, $esCorrecta);
            
            if($esCorrecta){
                // Sumar puntos a la partida actual
                $sql = "UPDATE usuarios SET puntaje_partida = puntaje_partida + 10 WHERE id_usuario = $idUsuario";
                $this->conexion->query($sql);
                
                $sql = "SELECT puntaje_partida FROM usuarios WHERE id_usuario = $idUsuario";
                $result_partida = $this->conexion->query($sql);
                $puntaje_partida = 0;
                if (is_array($result_partida) && count($result_partida) > 0) {
                    $puntaje_partida = (int) $result_partida[0]['puntaje_partida'];
                    $datos['puntajePartida'] = $puntaje_partida;
                }
                
                // Actualizar puntaje total si supera el récord
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
                
                // Actualizar estadísticas de la pregunta
                $sql = "UPDATE preguntas SET correcta_count = correcta_count + 1 WHERE id_pregunta = $idPregunta";
                $this->conexion->query($sql);
                
                // Actualizar puntaje de la partida
                $sql = "UPDATE partidas SET puntaje_obtenido = $puntaje_partida WHERE id_partida = $idPartida";
                $this->conexion->query($sql);
            } else {
                // Respuesta incorrecta - finalizar partida
                $sql = "SELECT puntaje_partida FROM usuarios WHERE id_usuario = $idUsuario";
                $result_partida = $this->conexion->query($sql);
                $puntaje_partida = 0;
                if (is_array($result_partida) && count($result_partida) > 0) {
                    $puntaje_partida = (int) $result_partida[0]['puntaje_partida'];
                    $datos['puntajePartida'] = $puntaje_partida;
                }
                
                // Finalizar partida
                $sql = "UPDATE partidas 
                        SET fecha_fin = NOW(), 
                            puntaje_obtenido = $puntaje_partida,
                            ganador = $idUsuario
                        WHERE id_partida = $idPartida";
                $this->conexion->query($sql);
                
                // Resetear puntaje de partida del usuario
                $sql = "UPDATE usuarios SET puntaje_partida = 0 WHERE id_usuario = $idUsuario";
                $this->conexion->query($sql);
                
                // Actualizar estadísticas de la pregunta
                $sql = "UPDATE preguntas SET incorrecta_count = incorrecta_count + 1 WHERE id_pregunta = $idPregunta";
                $this->conexion->query($sql);

                $sql = "DELETE FROM preguntas_respondidas WHERE id_usuario = $idUsuario";
                $this->conexion->query($sql);
            }
            return $datos;
        }
        return null;
    }

    // Obtener o crear una partida activa para el usuario
    private function getOrCreatePartidaActiva($idUsuario) {
        // Buscar partida activa (sin fecha_fin)
        $sql = "SELECT id_partida FROM partidas 
                WHERE id_jugador1 = $idUsuario 
                AND fecha_fin IS NULL 
                ORDER BY fecha_inicio DESC 
                LIMIT 1";
        $result = $this->conexion->query($sql);
        
        if (is_array($result) && count($result) > 0) {
            return (int)$result[0]['id_partida'];
        }
        
        // Crear nueva partida
        $sql = "INSERT INTO partidas (modo, id_jugador1, fecha_inicio) 
                VALUES ('solitario', $idUsuario, NOW())";
        $this->conexion->query($sql);
        
        // Obtener el ID de la partida recién creada
        $sql = "SELECT LAST_INSERT_ID() as id";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            return (int)$result[0]['id'];
        }
        
        return null;
    }

    // Registrar una respuesta en una partida
    private function registrarRespuestaPartida($idPartida, $idPregunta, $idUsuario, $respuesta, $correcta) {
        $correctaInt = $correcta ? 1 : 0;
        $sql = "INSERT INTO respuestas_partida (id_partida, id_pregunta, id_usuario, respuesta, correcta) 
                VALUES ($idPartida, $idPregunta, $idUsuario, '$respuesta', $correctaInt)";
        $this->conexion->query($sql);
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

    public function reportarPregunta($idPregunta, $idUsuario, $motivo, $comentario){
        $sql = "INSERT INTO reportes_pregunta (id_pregunta, id_usuario, motivo, comentario) VALUES ($idPregunta, $idUsuario, '$motivo', '$comentario')";
        $this->conexion->query($sql);

        $sql = "UPDATE preguntas SET estado = 'pendiente' WHERE id_pregunta = $idPregunta";
        $this->conexion->query($sql);
    }

    public function agregarPreguntaRespondida($idPregunta, $idUsuario){
        $sql = "INSERT INTO preguntas_respondidas (id_pregunta, id_usuario) VALUES ($idPregunta, $idUsuario)";
        $this->conexion->query($sql);
    }
}