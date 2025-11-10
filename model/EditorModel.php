<?php

class EditorModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }


    public function getPreguntasPendientes() {
        $sql = "SELECT * FROM preguntas WHERE estado = 'pendiente'";
        $result = $this->db->query($sql);
        return $result ? $result : [];
    }


    public function getReportesPendientes() {
        $sql = "SELECT r.*, p.texto 
                FROM reportes_pregunta r
                INNER JOIN preguntas p ON r.id_pregunta = p.id_pregunta
                WHERE r.estado = 'pendiente'";
        $result = $this->db->query($sql);
        return $result ? $result : [];
    }


    public function aprobarPregunta($idPregunta, $idEditor) {
        $sql = "UPDATE preguntas 
                SET estado = 'aprobada', id_aprobador = $idEditor 
                WHERE id_pregunta = $idPregunta";
        $this->db->query($sql);
    }

    public function rechazarPregunta($idPregunta, $idEditor) {
        $sql = "UPDATE preguntas 
                SET estado = 'rechazada', id_aprobador = $idEditor 
                WHERE id_pregunta = $idPregunta";
        $this->db->query($sql);
    }

    // Crea una nueva pregunta
    public function crearPregunta($data, $idCreador) {
        $texto = $data["texto"];
        $a = $data["opcion_a"];
        $b = $data["opcion_b"];
        $c = $data["opcion_c"];
        $d = $data["opcion_d"];
        $respuesta = $data["respuesta_correcta"];
        $categoria = $data["categoria"];
        $dificultad = $data["dificultad"];

        $sql = "INSERT INTO preguntas 
                (texto, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, categoria, dificultad, id_creador, estado)
                VALUES ('$texto', '$a', '$b', '$c', '$d', '$respuesta', '$categoria', '$dificultad', $idCreador, 'pendiente')";
        $this->db->query($sql);
    }


    public function borrarPregunta($idPregunta) {
        $sql = "DELETE FROM preguntas WHERE id_pregunta = $idPregunta";
        $this->db->query($sql);
    }

    public function obtenerPreguntaPorId($idPregunta) {
        $sql = "SELECT * FROM preguntas WHERE id_pregunta = $idPregunta";
        $result = $this->db->query($sql);
        return $result ? $result[0] : null;
    }


    public function modificarPregunta($data) {
        $id = $data["id_pregunta"];
        $texto = $data["texto"];
        $a = $data["opcion_a"];
        $b = $data["opcion_b"];
        $c = $data["opcion_c"];
        $d = $data["opcion_d"];
        $respuesta = $data["respuesta_correcta"];
        $categoria = $data["categoria"];
        $dificultad = $data["dificultad"];

        $sql = "UPDATE preguntas 
                SET texto = '$texto', 
                    opcion_a = '$a', 
                    opcion_b = '$b', 
                    opcion_c = '$c', 
                    opcion_d = '$d', 
                    respuesta_correcta = '$respuesta', 
                    categoria = '$categoria', 
                    dificultad = '$dificultad'
                WHERE id_pregunta = $id";
        $this->db->query($sql);
    }

public function desestimarReporte($idReporte) {
    $sql = "UPDATE reportes_pregunta SET estado = 'resuelto' WHERE id_reporte = $idReporte";
    $this->db->query($sql);
}


    public function eliminarPreguntaReportada($idReporte, $idPregunta) {
        // primero eliminar la pregunta
        $this->borrarPregunta($idPregunta);

        // luego marcar el reporte como resuelto
        $sql = "UPDATE reportes_pregunta SET estado = 'resuelto' WHERE id_reporte = $idReporte";
        $this->db->query($sql);
    }
}
