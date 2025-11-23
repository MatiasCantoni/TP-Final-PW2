<?php

class GameModel{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }
    public function esUsuarioAptoParaJugar($idUsuario){
        $sql = "SELECT tipo_usuario FROM usuarios WHERE id_usuario = '$idUsuario'";
        $result = $this->conexion->query($sql);
        $resultado = false;
        if($result[0]['tipo_usuario'] === 'jugador'){
            $resultado = true;
        }
        return $resultado;
    }
    public function getCategorias(){
        return ['Historia', 'Ciencia', 'Deportes', 'Arte', 'Geografia', 'Entretenimiento'];
    }

    public function getPreguntaRandom($categoria, $usuario, $nivelUsuario){
        $nivel = match ($nivelUsuario) {
            'bajo' => 'facil',
            'medio' => 'media',
            'alto' => 'dificil',
            default => 'media',
        };

        // primero busco preguntas del nivel exacto del usuario
        $sql = "SELECT * FROM preguntas WHERE categoria = '$categoria' AND estado = 'aprobada' AND dificultad = '$nivel'
            AND id_pregunta NOT IN (SELECT id_pregunta FROM preguntas_respondidas WHERE id_usuario = $usuario) ORDER BY RAND() LIMIT 1";
        $result = $this->conexion->query($sql);

        // si no hay preguntas del nivel exacto, busco en dificultades cercanas
        if (empty($result) || !is_array($result) || count($result) == 0) {
            $dificultades_cercanas = match ($nivel) {
                'facil' => ['media', 'dificil'],
                'media' => ['facil', 'dificil'],
                'dificil' => ['media', 'facil'],
                default => ['media', 'facil', 'dificil']
            };


            foreach ($dificultades_cercanas as $dif_cercana) {
                $sql = "SELECT * FROM preguntas WHERE categoria = '$categoria' AND estado = 'aprobada' AND dificultad = '$dif_cercana'
                    AND id_pregunta NOT IN (SELECT id_pregunta FROM preguntas_respondidas WHERE id_usuario = $usuario) ORDER BY RAND() LIMIT 1";
                $result = $this->conexion->query($sql);
                
                if (is_array($result) && count($result) > 0) {
                    break; 
                }
            }
        }

        if (is_array($result) && count($result) > 0) {
            $this->agregarPreguntaRespondida($result[0]["id_pregunta"], $usuario);
                        
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
        $validacionHora = $this->validarTiempoDeRespuesta($idPregunta, $idUsuario);
        $correcta = $this->getRespuestaCorrecta($idPregunta);
        

        if ($correcta !== null) {
            // Si el tiempo se acabó, la respuesta es incorrecta automáticamente
            if ($tiempo_terminado === '1' || $validacionHora === false) {
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

                // Añadir respuestas correctas al usuario
                $sql = "UPDATE usuarios SET respuestas_correctas = respuestas_correctas + 1 WHERE id_usuario = $idUsuario";
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
                
                // Limpiar preguntas respondidas para reiniciar el ciclo
                $sql = "DELETE FROM preguntas_respondidas WHERE id_usuario = $idUsuario";
                $this->conexion->query($sql);

                // Añadir respuestas incorrectas al usuario
                $sql = "UPDATE usuarios SET respuestas_incorrectas = respuestas_incorrectas + 1 WHERE id_usuario = $idUsuario";
                $this->conexion->query($sql);
            }
            // preguntar al profe si se puede dejar esto aca
            $this->setDificultadPregunta($idPregunta);
            $this->setNivelUsuario($idUsuario);
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

    public function setDificultadPregunta($idPregunta){
        $sql = "SELECT correcta_count, incorrecta_count FROM preguntas WHERE id_pregunta = $idPregunta";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            $correcta_count = (int)$result[0]['correcta_count'];
            $incorrecta_count = (int)$result[0]['incorrecta_count'];
            $total = $correcta_count + $incorrecta_count;

            if ($total >= 10) { // Solo ajustar dificultad si hay al menos 10 respuestas
                $ratio = $correcta_count / $total;
                $nueva_dificultad = 'media';

                if ($ratio >= 0.8) {
                    $nueva_dificultad = 'facil';
                } elseif ($ratio < 0.5) {
                    $nueva_dificultad = 'dificil';
                }

                $sql_update = "UPDATE preguntas SET dificultad = '$nueva_dificultad' WHERE id_pregunta = $idPregunta";
                $this->conexion->query($sql_update);
            }
        }
    }

    public function setNivelUsuario($idUsuario){
        $sql = "SELECT respuestas_correctas, respuestas_incorrectas FROM usuarios WHERE id_usuario = $idUsuario";
        $result = $this->conexion->query($sql);
        if (is_array($result) && count($result) > 0) {
            $correctas = (int)$result[0]['respuestas_correctas'];
            $incorrectas = (int)$result[0]['respuestas_incorrectas'];
            $total = $correctas + $incorrectas;

            if ($total >= 0) {
                $ratio = $correctas / $total;
                $nivel = 'medio';

                if ($ratio >= 0.7) {
                    $nivel = 'alto';
                } elseif ($ratio < 0.4) {
                    $nivel = 'bajo';
                }

                $sql_update = "UPDATE usuarios SET nivel = '$nivel' WHERE id_usuario = $idUsuario";
                $this->conexion->query($sql_update);
            }
        }
    }

    public function validarTiempoDeRespuesta($idPregunta, $idUsuario){
        // Usar NOW() de MySQL para la hora actual, evitando problemas de zona horaria en PHP
        // TIMESTAMPDIFF calcula la diferencia en segundos usando el reloj del servidor MySQL
        
        $sql = "SELECT TIMESTAMPDIFF(SECOND, hora, NOW()) as diferencia_segundos
                FROM preguntas_respondidas
                WHERE id_pregunta = $idPregunta AND id_usuario = $idUsuario
                ORDER BY hora DESC LIMIT 1";

        $resultado = $this->conexion->query($sql);

        if (!is_array($resultado) || count($resultado) == 0) {
            error_log("Error: No se encontró pregunta_respondida para validar tiempo");
            return true;
        }

        $diferenciaSegundos = (int)$resultado[0]['diferencia_segundos'];
        
        $tiempoMaximoPermitido = 10;
        
        error_log("Validación de tiempo: Diferencia=$diferenciaSegundos segundos, máximo permitido=$tiempoMaximoPermitido");
        
        if ($diferenciaSegundos < 0 || $diferenciaSegundos > $tiempoMaximoPermitido) {
            error_log("Respuesta rechazada: Usuario tardó $diferenciaSegundos segundos (máximo: $tiempoMaximoPermitido)");
            return false;
        }
        
        return true;
    }
}