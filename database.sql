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


CREATE TABLE preguntas (
                           id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
                           texto_pregunta VARCHAR(255) NOT NULL,
                           opcion_a VARCHAR(255) NOT NULL,
                           opcion_b VARCHAR(255) NOT NULL,
                           opcion_c VARCHAR(255) NOT NULL,
                           opcion_d VARCHAR(255) NOT NULL,
                           respuesta_correcta CHAR(1) NOT NULL CHECK (respuesta_correcta IN ('A','B','C','D')),
                           categoria VARCHAR(50) NOT NULL,
                           dificultad ENUM('facil', 'media', 'dificil') DEFAULT 'media',
                           id_creador INT,
                           reportada BOOLEAN DEFAULT FALSE,
                           aprobada BOOLEAN DEFAULT TRUE,
                           FOREIGN KEY (id_creador) REFERENCES usuarios(id_usuario)
);


CREATE TABLE partidas (
                          id_partida INT AUTO_INCREMENT PRIMARY KEY,
                          id_jugador INT NOT NULL,
                          fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
                          fecha_fin DATETIME,
                          puntaje_obtenido INT DEFAULT 0,
                          estado ENUM('en_curso', 'finalizada') DEFAULT 'en_curso',
                          FOREIGN KEY (id_jugador) REFERENCES usuarios(id_usuario)
);


INSERT INTO preguntas (texto_pregunta, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, categoria, dificultad)
VALUES
    ('¿En qué año llegó el hombre a la luna?', '1965', '1969', '1971', '1975', 'B', 'Historia', 'facil'),
    ('¿Cuál es el país con más Copas del Mundo de fútbol?', 'Argentina', 'Alemania', 'Brasil', 'Italia', 'C', 'Deportes', 'facil'),
    ('¿Cuál es el océano más grande?', 'Atlántico', 'Índico', 'Ártico', 'Pacífico', 'D', 'Geografía', 'media'),
    ('¿Quién pintó la Mona Lisa?', 'Van Gogh', 'Leonardo Da Vinci', 'Picasso', 'Rembrandt', 'B', 'Arte', 'facil');
