<?php

class MyConexion
{

    private $conexion;

    public function __construct($server, $user, $pass, $database)
    {
        $this->conexion = new mysqli($server, $user, $pass, $database);
        if ($this->conexion->error) { die("Error en la conexión: " . $this->conexion->error); }
        $this->conexion->set_charset("utf8mb4");
    }

    public function query($sql)
    {
        $result = $this->conexion->query($sql);

        // La query puede devolver false (error), true (exito en operaciones no SELECT) o un mysqli_result (SELECT)
        if ($result === false) {
            return false; // error en la query
        }

        if ($result === true) {
            return true; // exito en operaciones no SELECT
        }

        // Es un resultado de SELECT
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return null; // no hay filas
    }
}