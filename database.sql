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
    tipo_usuario ENUM('jugador', 'editor', 'admin') DEFAULT 'jugador',
    nivel ENUM('bajo', 'medio', 'alto') DEFAULT 'bajo',
    puntaje_total INT DEFAULT 0,
    puntaje_partida INT DEFAULT 0,
    cuenta_activa BOOLEAN DEFAULT FALSE,
    token_validacion VARCHAR(100),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nombre_completo, anio_nacimiento, sexo, pais, ciudad, email, contrasena, nombre_usuario, tipo_usuario) VALUES ('Admin User', 1990, 'Masculino', 'Argentina', 'Buenos Aires', 'admin@example.com', 'admin', 'admin', 'admin');


-- CREATE TABLE preguntas (
--                            id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
--                            texto_pregunta VARCHAR(255) NOT NULL,
--                            opcion_a VARCHAR(255) NOT NULL,
--                            opcion_b VARCHAR(255) NOT NULL,
--                            opcion_c VARCHAR(255) NOT NULL,
--                            opcion_d VARCHAR(255) NOT NULL,
--                            respuesta_correcta CHAR(1) NOT NULL CHECK (respuesta_correcta IN ('A','B','C','D')),
--                            categoria VARCHAR(50) NOT NULL,
--                            dificultad ENUM('facil', 'media', 'dificil') DEFAULT 'media',
--                            id_creador INT,
--                            reportada BOOLEAN DEFAULT FALSE,
--                            aprobada BOOLEAN DEFAULT TRUE,
--                            FOREIGN KEY (id_creador) REFERENCES usuarios(id_usuario)
-- );


-- CREATE TABLE partidas (
--                           id_partida INT AUTO_INCREMENT PRIMARY KEY,
--                           id_jugador INT NOT NULL,
--                           fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
--                           fecha_fin DATETIME,
--                           puntaje_obtenido INT DEFAULT 0,
--                           estado ENUM('en_curso', 'finalizada') DEFAULT 'en_curso',
--                           FOREIGN KEY (id_jugador) REFERENCES usuarios(id_usuario)
-- );


-- segunda opcion

CREATE TABLE preguntas (
    id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    opcion_a VARCHAR(255),
    opcion_b VARCHAR(255),
    opcion_c VARCHAR(255),
    opcion_d VARCHAR(255),
    respuesta_correcta CHAR(1) CHECK (respuesta_correcta IN ('A','B','C','D')),
    dificultad ENUM('facil', 'media', 'dificil') DEFAULT 'facil',
    categoria ENUM('Historia', 'Ciencia', 'Deportes', 'Arte', 'Geografia', 'Entretenimiento') NOT NULL,
    correcta_count INT DEFAULT 0,
    incorrecta_count INT DEFAULT 0,
    estado ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente',
    id_creador INT,
    id_aprobador INT NULL,
    FOREIGN KEY (id_creador) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_aprobador) REFERENCES usuarios(id_usuario)
);

CREATE TABLE partidas (
    id_partida INT AUTO_INCREMENT PRIMARY KEY,
    modo ENUM('solitario', 'versus') NOT NULL,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_fin DATETIME NULL,
    id_jugador1 INT NOT NULL,
    id_jugador2 INT NULL, -- NULL si es solitario
    ganador INT NULL,
    FOREIGN KEY (id_jugador1) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_jugador2) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (ganador) REFERENCES usuarios(id_usuario)
);

-- Registra las preguntas respondidas durante una partida
CREATE TABLE respuestas_partida (
    id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
    id_partida INT NOT NULL,
    id_pregunta INT NOT NULL,
    id_usuario INT NOT NULL,
    respuesta CHAR(1) CHECK (respuesta IN ('A','B','C','D')),
    correcta BOOLEAN,
    FOREIGN KEY (id_partida) REFERENCES partidas(id_partida),
    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Para guardar los reportes de usuarios
CREATE TABLE reportes_pregunta (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    id_pregunta INT NOT NULL,
    id_usuario INT NOT NULL,
    motivo TEXT NOT NULL,
    comentario TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'revisado') DEFAULT 'pendiente',
    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);


INSERT INTO preguntas (texto, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, categoria, dificultad, estado)
VALUES
    ('¿En qué año llegó el hombre a la luna?', '1965', '1969', '1971', '1975', 'B', 'Historia', 'facil', 'aprobada'),
    ('¿Cuál es el país con más Copas del Mundo de fútbol?', 'Argentina', 'Alemania', 'Brasil', 'Italia', 'C', 'Deportes', 'facil', 'aprobada'),
    ('¿Cuál es el océano más grande?', 'Atlántico', 'Índico', 'Ártico', 'Pacífico', 'D', 'Geografia', 'media', 'aprobada'),
    ('¿Quién pintó la Mona Lisa?', 'Van Gogh', 'Leonardo Da Vinci', 'Picasso', 'Rembrandt', 'B', 'Arte', 'facil', 'aprobada');

INSERT INTO preguntas (texto, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, categoria, dificultad, estado)
VALUES
-- HISTORIA
('¿En qué año comenzó la Primera Guerra Mundial?', '1914', '1918', '1939', '1945', 'A', 'Historia', 'media', 'aprobada'),
('¿Quién fue el primer presidente de los Estados Unidos?', 'George Washington', 'Thomas Jefferson', 'Abraham Lincoln', 'John Adams', 'A', 'Historia', 'facil', 'aprobada'),
('¿En qué país se construyó el Muro de Berlín?', 'Francia', 'Alemania', 'Polonia', 'Rusia', 'B', 'Historia', 'facil', 'aprobada'),
('¿Qué civilización construyó Machu Picchu?', 'Azteca', 'Inca', 'Maya', 'Olmeca', 'B', 'Historia', 'facil', 'aprobada'),
('¿En qué año terminó la Segunda Guerra Mundial?', '1945', '1942', '1939', '1950', 'A', 'Historia', 'facil', 'aprobada'),

-- CIENCIA
('¿Cuál es el planeta más grande del sistema solar?', 'Saturno', 'Júpiter', 'Urano', 'Neptuno', 'B', 'Ciencia', 'facil', 'aprobada'),
('¿Cuál es la fórmula química del agua?', 'CO2', 'H2O', 'O2', 'NaCl', 'B', 'Ciencia', 'facil', 'aprobada'),
('¿Qué científico propuso la teoría de la relatividad?', 'Isaac Newton', 'Albert Einstein', 'Galileo Galilei', 'Niels Bohr', 'B', 'Ciencia', 'media', 'aprobada'),
('¿Cuál es el órgano más grande del cuerpo humano?', 'Corazón', 'Hígado', 'Piel', 'Cerebro', 'C', 'Ciencia', 'media', 'aprobada'),
('¿Qué gas necesitan las plantas para hacer la fotosíntesis?', 'Oxígeno', 'Dióxido de carbono', 'Hidrógeno', 'Nitrógeno', 'B', 'Ciencia', 'facil', 'aprobada'),

-- DEPORTES
('¿Cuántos jugadores hay en un equipo de fútbol en el campo?', '9', '10', '11', '12', 'C', 'Deportes', 'facil', 'aprobada'),
('¿En qué deporte se utiliza un disco?', 'Béisbol', 'Hockey sobre hielo', 'Balonmano', 'Tenis', 'B', 'Deportes', 'facil', 'aprobada'),
('¿Quién tiene más títulos de Grand Slam en tenis masculino?', 'Roger Federer', 'Novak Djokovic', 'Rafael Nadal', 'Andy Murray', 'B', 'Deportes', 'media', 'aprobada'),
('¿En qué país se originó el sumo?', 'China', 'Corea del Sur', 'Japón', 'Tailandia', 'C', 'Deportes', 'facil', 'aprobada'),
('¿Cuántos puntos vale un triple en baloncesto?', '2', '3', '4', '1', 'B', 'Deportes', 'facil', 'aprobada'),

-- ARTE
('¿Quién pintó “La noche estrellada”?', 'Vincent Van Gogh', 'Claude Monet', 'Salvador Dalí', 'Leonardo Da Vinci', 'A', 'Arte', 'facil', 'aprobada'),
('¿A qué movimiento pertenece Picasso?', 'Cubismo', 'Surrealismo', 'Impresionismo', 'Realismo', 'A', 'Arte', 'media', 'aprobada'),
('¿Cuál de las siguientes es una obra de Miguel Ángel?', 'La última cena', 'La creación de Adán', 'El grito', 'Guernica', 'B', 'Arte', 'media', 'aprobada'),
('¿En qué país nació el artista Salvador Dalí?', 'Francia', 'Italia', 'España', 'Portugal', 'C', 'Arte', 'facil', 'aprobada'),
('¿Qué instrumento musical tiene teclas negras y blancas?', 'Violín', 'Piano', 'Guitarra', 'Arpa', 'B', 'Arte', 'facil', 'aprobada'),

-- GEOGRAFÍA
('¿Cuál es el río más largo del mundo?', 'Amazonas', 'Nilo', 'Yangtsé', 'Misisipi', 'A', 'Geografia', 'media', 'aprobada'),
('¿En qué continente se encuentra Egipto?', 'África', 'Asia', 'Europa', 'Oceanía', 'A', 'Geografia', 'facil', 'aprobada'),
('¿Cuál es el país más grande del mundo?', 'Canadá', 'China', 'Rusia', 'Estados Unidos', 'C', 'Geografia', 'facil', 'aprobada'),
('¿Cuál es la capital de Australia?', 'Sídney', 'Melbourne', 'Canberra', 'Perth', 'C', 'Geografia', 'media', 'aprobada'),
('¿En qué país se encuentra la Torre Eiffel?', 'Italia', 'Francia', 'Inglaterra', 'España', 'B', 'Geografia', 'facil', 'aprobada'),

-- ENTRETENIMIENTO
('¿Cuál es el nombre del mago protagonista en “Harry Potter”?', 'Ron Weasley', 'Harry Potter', 'Hermione Granger', 'Draco Malfoy', 'B', 'Entretenimiento', 'facil', 'aprobada'),
('¿Qué empresa creó el videojuego “Super Mario”?', 'Sega', 'Sony', 'Nintendo', 'Microsoft', 'C', 'Entretenimiento', 'facil', 'aprobada'),
('¿Quién interpretó a Iron Man en el Universo Marvel?', 'Chris Evans', 'Chris Hemsworth', 'Robert Downey Jr.', 'Mark Ruffalo', 'C', 'Entretenimiento', 'facil', 'aprobada'),
('¿Qué serie tiene como protagonistas a Ross, Rachel, Monica, Chandler, Joey y Phoebe?', 'Friends', 'How I Met Your Mother', 'The Office', 'Seinfeld', 'A', 'Entretenimiento', 'facil', 'aprobada'),
('¿Cuál de estos es un personaje de Star Wars?', 'Harry Potter', 'Frodo Bolsón', 'Luke Skywalker', 'Gandalf', 'C', 'Entretenimiento', 'facil', 'aprobada');
