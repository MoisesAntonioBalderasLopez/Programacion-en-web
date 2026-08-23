/*Creacion de base de datos de renta de carros*/
CREATE DATABASE IF NOT EXISTS renta_carros;

/*Uso de la base de datos de renta de carros*/
USE renta_carros;

/*Creacion de la tabla usuarios*/
CREATE TABLE IF NOT EXISTS usuarios (
	ID INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Id de identificacion de usuario',
	Correo VARCHAR(255) NOT NULL COMMENT 'Correo electronico del usuario',
	Contraseña VARCHAR(10) NOT NULL COMMENT 'Contraseña del usuario',
	Rol VARCHAR(50) NOT NULL COMMENT 'Rol que tiene el usuario en su empresa',
	Nombre VARCHAR(255) NOT NULL COMMENT 'Nombre de identificacion del usuario',
	Tipo ENUM('usuario', 'admin') NOT NULL DEFAULT 'usuario' COMMENT 'Tipo de usuario en el sistema'
);

/*Creacion de la tabla vehiculos*/
CREATE TABLE IF NOT EXISTS vehiculos (
	ID INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Id del vehiculo',
	Marca VARCHAR(100) NOT NULL COMMENT 'Marca del vehiculo',
	Modelo VARCHAR(100) NOT NULL COMMENT 'Modelo del vehiculo',
	Anio INT(4) NOT NULL COMMENT 'Año del vehiculo',
	Precio DECIMAL(10,2) NOT NULL COMMENT 'Precio por dia en MXN',
	Imagen VARCHAR(255) NOT NULL COMMENT 'Ruta de la imagen del vehiculo'
);

/*Insertar los vehiculos disponibles*/
INSERT INTO vehiculos (Marca, Modelo, Anio, Precio, Imagen) VALUES
('Toyota', 'Corolla', 2024, 850.00, 'images/vehiculos/toyota.jpg'),
('Honda', 'Civic', 2023, 900.00, 'images/vehiculos/honda.JPG'),
('Ford', 'Mustang', 2024, 1500.00, 'images/vehiculos/ford.jpg'),
('Chevrolet', 'Camaro', 2023, 1400.00, 'images/vehiculos/chevrolet.jpg'),
('BMW', 'Serie 3', 2024, 1800.00, 'images/vehiculos/BMW.jpg'),
('Mercedes-Benz', 'Clase C', 2024, 2000.00, 'images/vehiculos/Mercedes-Benz.jpg'),
('Nissan', 'Sentra', 2023, 750.00, 'images/vehiculos/Nissan.jpg'),
('Volkswagen', 'Jetta', 2024, 800.00, 'images/vehiculos/Volkswagen.jpg');

/*Creacion de la tabla reservas*/
CREATE TABLE IF NOT EXISTS reservas (
	ID INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Id de la reserva',
	UsuarioID INT(11) NOT NULL COMMENT 'Id del usuario que realiza la reserva',
	VehiculoID INT(11) NOT NULL COMMENT 'Id del vehiculo reservado',
	FechaInicio DATE NOT NULL COMMENT 'Fecha de inicio de la renta',
	FechaFin DATE NOT NULL COMMENT 'Fecha de fin de la renta',
	Dias INT(11) NOT NULL COMMENT 'Numero de dias de la renta',
	CostoTotal DECIMAL(10,2) NOT NULL COMMENT 'Costo total de la renta',
	FechaReserva DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se realizo la reserva',
	FOREIGN KEY (UsuarioID) REFERENCES usuarios(ID),
	FOREIGN KEY (VehiculoID) REFERENCES vehiculos(ID)
);

/*Insertar un usuario administrador de prueba*/
INSERT INTO usuarios (Correo, Contraseña, Rol, Nombre, Tipo) VALUES
('admin@rentacarrosxyz.com', 'admin123', 'Administrador', 'Admin Sistema', 'admin');