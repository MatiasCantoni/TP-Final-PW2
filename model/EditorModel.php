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
        $nombreCategoria = $data["categoria"]; // viene como texto (ej: "Ciencia")
        $dificultad = $data["dificultad"];

        // Buscar el ID de la categoría en la base
        $sql = "SELECT id_categoria FROM categorias WHERE nombre = '$nombreCategoria'";
        $resultado = $this->db->query($sql);

        if (!$resultado || count($resultado) == 0) {
            echo "Error: la categoría no existe.";
            return;
        }

        $idCategoria = $resultado[0]["id_categoria"];

        // Usar id_categoria en el INSERT
        $sql = "INSERT INTO preguntas 
            (texto, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, id_categoria, dificultad, id_creador, estado)
            VALUES ('$texto', '$a', '$b', '$c', '$d', '$respuesta', '$idCategoria', '$dificultad', $idCreador, 'pendiente')";
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
        $nombreCategoria = $data["categoria"];
        $dificultad = $data["dificultad"];

        // Buscar id de la categoría
        $sqlCat = "SELECT id_categoria FROM categorias WHERE nombre = '$nombreCategoria'";
        $resCat = $this->db->query($sqlCat);
        if (!$resCat || count($resCat) == 0) {
            return;
        }
        $idCategoria = $resCat[0]["id_categoria"];

        $sql = "UPDATE preguntas 
            SET texto = '$texto', 
                opcion_a = '$a', 
                opcion_b = '$b', 
                opcion_c = '$c', 
                opcion_d = '$d', 
                respuesta_correcta = '$respuesta', 
                id_categoria = '$idCategoria', 
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

    public function agregarCategoria($nombreCategoria) {
        // Verificar si ya existe una categoría con el mismo nombre
        $sqlCheck = "SELECT * FROM categorias WHERE nombre = '$nombreCategoria'";
        $existe = $this->db->query($sqlCheck);

        if ($existe && count($existe) > 0) {
            echo "<script>
                alert('⚠️ La categoría \"$nombreCategoria\" ya existe.');
                window.location = '/editor';
              </script>";
            exit;
        }

        // Insertar nueva categoría
        $sql = "INSERT INTO categorias (nombre) VALUES ('$nombreCategoria')";
        $resultado = $this->db->query($sql);

        if ($resultado) {
            echo "<script>
                alert('✅ Categoría agregada correctamente.');
                window.location = '/editor';
              </script>";
        } else {
            echo "<script>
                alert('❌ Error al agregar la categoría.');
                window.location = '/editor';
              </script>";
        }
        exit;
    }


}
