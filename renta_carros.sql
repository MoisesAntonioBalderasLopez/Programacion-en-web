/*creacion de de base de datos de renta de carros*/
CREATE DATABASE IF NOT EXISTS renta_carros;


/*uso de la base de datos de renta de carros*/
USE renta_carros;


/*Creacion de la tabla usuarios*/
CREATE TABLE IF NOT EXISTS usuarios (
	ID INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Id de identificacion de usuario',
	Correo VARCHAR(255) NOT NULL COMMENT 'Correo electronico del usuario',
	Contraseña VARCHAR(10) NOT NULL COMMENT 'Contraseña del usuario',
	Rol VARCHAR(50) NOT NULL COMMENT 'Rol que tiene el usuario en su empresa',
	Nombre VARCHAR(255) NOT NULL COMMENT 'Nombre de identificacion del usuario'
);