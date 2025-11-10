<?php

class RankingModel {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function getTopJugadores($limite = 10) {
        $sql = "SELECT 
                    id_usuario,
                    nombre_usuario,
                    nombre_completo,
                    foto_perfil,
                    puntaje_total,
                    pais,
                    ciudad,
                    (SELECT COUNT(DISTINCT id_partida) 
                     FROM partidas 
                     WHERE (id_jugador1 = usuarios.id_usuario OR id_jugador2 = usuarios.id_usuario) 
                       AND fecha_fin IS NOT NULL) as partidas_jugadas
                FROM usuarios 
                WHERE cuenta_activa = 1
                ORDER BY puntaje_total DESC, id_usuario ASC 
                LIMIT $limite";
        
        $result = $this->conexion->query($sql);
        return is_array($result) ? $result : [];
    }

    public function getPosicionUsuario($idUsuario) {
        $sqlUser = "SELECT puntaje_total FROM usuarios WHERE id_usuario = $idUsuario AND cuenta_activa = 1";
        $resultUser = $this->conexion->query($sqlUser);

        if (!is_array($resultUser) || count($resultUser) == 0) {
            return null; 
        }
        
        $puntajeUsuario = (int)$resultUser[0]['puntaje_total'];

        $sql = "SELECT COUNT(*) + 1 as posicion
                FROM usuarios
                WHERE cuenta_activa = 1
                  AND (
                       puntaje_total > $puntajeUsuario
                       OR (puntaje_total = $puntajeUsuario AND id_usuario < $idUsuario)
                      )";
        
        $result = $this->conexion->query($sql);
        
        if (is_array($result) && count($result) > 0) {
            return (int)$result[0]['posicion'];
        }
        
        return null; 
    }

    public function getUsuarioById($idUsuario) {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = $idUsuario AND cuenta_activa = 1";
        $result = $this->conexion->query($sql);
        
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return null;
    }
}