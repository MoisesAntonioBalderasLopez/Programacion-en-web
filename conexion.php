<?php
/*Archivo de conexion a la base de datos renta_carros*/

$servidor = "127.0.0.1";
$puerto = "3307";
$usuario = "root";
$contrasena = "";
$base_datos = "renta_carros";

$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos, $puerto);

/*Verificar si la conexion fue exitosa*/
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>
