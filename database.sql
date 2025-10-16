CREATE DATABASE IF NOT EXISTS preguntados;
USE preguntados;

DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    anio_nacimiento YEAR NOT NULL,
    sexo ENUM('Masculino', 'Femenino', 'Prefiero no cargarlo') DEFAULT 'Prefiero no cargarlo',
    pais VARCHAR(100) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    foto_perfil VARCHAR(255) DEFAULT 'default.png',
    tipo_usuario ENUM('jugador', 'editor') DEFAULT 'jugador',
    puntaje_total INT DEFAULT 0,
    cuenta_activa BOOLEAN DEFAULT FALSE,
    token_validacion VARCHAR(100),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nombre_completo, anio_nacimiento, sexo, pais, ciudad, email, contrasena, nombre_usuario) VALUES ('Admin User', 1990, 'Masculino', 'Argentina', 'Buenos Aires', 'admin@example.com', 'admin', 'admin');


