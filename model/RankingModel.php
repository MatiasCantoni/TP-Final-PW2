<?php

class RankingModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Obtener top N jugadores por puntaje total
    public function getTopJugadores($limite = 50) {
        $sql = "SELECT 
                    id_usuario,
                    nombre_usuario,
                    nombre_completo,
                    foto_perfil,
                    puntaje_total,
                    pais,
                    ciudad,
                    (SELECT COUNT(*) FROM partidas WHERE id_jugador1 = usuarios.id_usuario AND fecha_fin IS NOT NULL) as partidas_jugadas
                FROM usuarios 
                WHERE cuenta_activa = 1
                ORDER BY puntaje_total DESC 
                LIMIT $limite";
        
        $result = $this->conexion->query($sql);
        return is_array($result) ? $result : [];
    }

    // Obtener posición del usuario en el ranking
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

    // Obtener datos completos de un usuario
    public function getUsuarioById($idUsuario) {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = $idUsuario AND cuenta_activa = 1";
        $result = $this->conexion->query($sql);
        
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return null;
    }

    // Obtener historial de partidas de un usuario
    public function getPartidasUsuario($idUsuario) {
        $sql = "SELECT 
                    p.id_partida,
                    p.modo,
                    p.fecha_inicio,
                    p.fecha_fin,
                    p.id_jugador1,
                    p.id_jugador2,
                    p.ganador,
                    u2.nombre_usuario as oponente_nombre,
                    (SELECT COUNT(*) FROM respuestas_partida WHERE id_partida = p.id_partida AND id_usuario = $idUsuario AND correcta = 1) as respuestas_correctas,
                    (SELECT COUNT(*) FROM respuestas_partida WHERE id_partida = p.id_partida AND id_usuario = $idUsuario) as total_respuestas
                FROM partidas p
                LEFT JOIN usuarios u2 ON (p.id_jugador2 = u2.id_usuario)
                WHERE (p.id_jugador1 = $idUsuario OR p.id_jugador2 = $idUsuario)
                    AND p.fecha_fin IS NOT NULL
                ORDER BY p.fecha_fin DESC
                LIMIT 20";
        
        $result = $this->conexion->query($sql);
        return is_array($result) ? $result : [];
    }

    // Obtener estadísticas del usuario
    public function getEstadisticasUsuario($idUsuario) {
        $sql = "SELECT 
                    COUNT(DISTINCT p.id_partida) as total_partidas,
                    COUNT(DISTINCT CASE WHEN p.ganador = $idUsuario THEN p.id_partida END) as partidas_ganadas,
                    SUM(CASE WHEN rp.correcta = 1 THEN 1 ELSE 0 END) as preguntas_correctas,
                    COUNT(rp.id_respuesta) as total_preguntas_respondidas,
                    ROUND(
                        (SUM(CASE WHEN rp.correcta = 1 THEN 1 ELSE 0 END) * 100.0 / 
                        NULLIF(COUNT(rp.id_respuesta), 0)), 2
                    ) as porcentaje_acierto
                FROM partidas p
                LEFT JOIN respuestas_partida rp ON p.id_partida = rp.id_partida AND rp.id_usuario = $idUsuario
                WHERE (p.id_jugador1 = $idUsuario OR p.id_jugador2 = $idUsuario)
                    AND p.fecha_fin IS NOT NULL";
        
        $result = $this->conexion->query($sql);
        
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        
        return [
            'total_partidas' => 0,
            'partidas_ganadas' => 0,
            'preguntas_correctas' => 0,
            'total_preguntas_respondidas' => 0,
            'porcentaje_acierto' => 0
        ];
    }
}