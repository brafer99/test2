DROP DATABASE IF EXISTS modulo_drsu;

CREATE DATABASE IF NOT EXISTS modulo_drsu;

USE modulo_drsu;

CREATE TABLE rol(
	sql_rol_id INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    sql_rol_nombre VARCHAR(80) UNIQUE NOT NULL
);

CREATE TABLE usuario(
	sql_usuario_id INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    sql_usuario_email VARCHAR(80) UNIQUE NOT NULL,
	sql_usuario_pass VARCHAR(100) NOT NULL,
    sql_usuario_rol_id INTEGER UNSIGNED NOT NULL,
	FOREIGN KEY (sql_usuario_rol_id) REFERENCES rol(sql_rol_id) 
		ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE area(
    sql_area_id INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    sql_area_nombre VARCHAR(500) NOT NULL,
    sql_area_sigla VARCHAR(50) NOT NULL
);

CREATE TABLE estado(
    sql_estado_id INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    sql_estado_nombre VARCHAR(500) NOT NULL
);

CREATE TABLE noticia(
	sql_noticia_id INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	sql_noticia_titulo VARCHAR(200) NOT NULL,
    sql_noticia_imagen VARCHAR(200) NOT NULL,
	sql_noticia_fecha VARCHAR(200) NOT NULL,
	sql_noticia_hora VARCHAR(200) NOT NULL,
    sql_noticia_enlace VARCHAR(500) NOT NULL,

	sql_noticia_area_id INTEGER UNSIGNED NOT NULL,
    sql_noticia_estado_id INTEGER UNSIGNED NOT NULL,

	FOREIGN KEY (sql_noticia_area_id) REFERENCES area(sql_area_id) 
		ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (sql_noticia_estado_id) REFERENCES estado(sql_estado_id) 
		ON DELETE RESTRICT ON UPDATE CASCADE
);


INSERT INTO rol(sql_rol_nombre) VALUES
    ('Administrador'),
    ('Usuario');

INSERT INTO usuario(sql_usuario_email,sql_usuario_pass,sql_usuario_rol_id) VALUES
    ('sistema@gmail.com','contraseña',1),
    ('usuario@gmail.com','contraseña',2);


INSERT INTO area(sql_area_nombre,sql_area_sigla) VALUES
    ('Dirección Académica de Responsabilidad Social Universitaria','DRSU'),
    ('Área de Extensión y Proyeción Cultural','AEP'),
    ('Área de Programas de Voluntariado Basadrino y Vinculación con Grupos de Interés','AVO'),
    ('Área de Extensión Univesitaria, Proyección Social y Servicio Social','AES'),
    ('Área de Coordinación de la RSU en la Investigación y Formación Continua','ACI');

INSERT INTO estado(sql_estado_nombre) VALUES
    ('En proceso'),
    ('Finalizado');

INSERT INTO noticia(sql_noticia_titulo,sql_noticia_imagen,sql_noticia_fecha,sql_noticia_hora,sql_noticia_enlace,sql_noticia_area_id,sql_noticia_estado_id) VALUES
    ('Canto andino de voluntarios','cantoandino.jpg','12 de septiembre de 2021','19:00 horas','https://github.com/brafer99/RSU',1,2),
    ('concurso bailarines de voluntarios','concursobailarines.jpg','20 de noviembre de 2021','14:00 horas','https://github.com/brafer99/RSU',3,1);

