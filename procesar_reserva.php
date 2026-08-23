<?php
/*
 * Procesamiento de reserva de vehiculo
 * Verifica la sesion, guarda en la base de datos y envia correo de confirmacion
 */
session_start();
require_once 'conexion.php';
require_once 'enviar_correo.php';

/*Verificar que el usuario haya iniciado sesion*/
if (!isset($_SESSION["usuario_id"])) {
    /*Redirigir a login si no hay sesion activa*/
    header("Location: login.php?redireccion=vehiculos");
    exit();
}

/*Verificar que sea una solicitud POST*/
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: vehiculos.php");
    exit();
}

/*Obtener los datos del formulario*/
$vehiculo_id = intval($_POST["vehiculo_id"]);
$fecha_inicio = trim($_POST["fecha_inicio"]);
$fecha_fin = trim($_POST["fecha_fin"]);

/*Validar que los campos no esten vacios*/
if (empty($vehiculo_id) || empty($fecha_inicio) || empty($fecha_fin)) {
    header("Location: vehiculos.php?error=campos_vacios");
    exit();
}

/*Validar las fechas*/
$inicio = new DateTime($fecha_inicio);
$fin = new DateTime($fecha_fin);

if ($fin <= $inicio) {
    header("Location: vehiculos.php?error=fechas_invalidas");
    exit();
}

/*Calcular los dias y el costo*/
$diferencia = $inicio->diff($fin);
$dias = $diferencia->days;

/*Obtener los datos del vehiculo desde la base de datos*/
$consulta_vehiculo = $conexion->prepare("SELECT * FROM vehiculos WHERE ID = ?");
$consulta_vehiculo->bind_param("i", $vehiculo_id);
$consulta_vehiculo->execute();
$resultado_vehiculo = $consulta_vehiculo->get_result();

if ($resultado_vehiculo->num_rows == 0) {
    header("Location: vehiculos.php?error=vehiculo_no_encontrado");
    exit();
}

$vehiculo = $resultado_vehiculo->fetch_assoc();
$costo_total = $dias * $vehiculo["Precio"];

/*Guardar la reserva en la base de datos*/
$insertar_reserva = $conexion->prepare(
    "INSERT INTO reservas (UsuarioID, VehiculoID, FechaInicio, FechaFin, Dias, CostoTotal) VALUES (?, ?, ?, ?, ?, ?)"
);
$insertar_reserva->bind_param(
    "iissid",
    $_SESSION["usuario_id"],
    $vehiculo_id,
    $fecha_inicio,
    $fecha_fin,
    $dias,
    $costo_total
);

if ($insertar_reserva->execute()) {
    /*Intentar enviar correo de confirmacion*/
    $correo_enviado = enviarCorreoConfirmacion(
        $_SESSION["usuario_correo"],
        $_SESSION["usuario_nombre"],
        $vehiculo,
        $fecha_inicio,
        $fecha_fin,
        $dias,
        $costo_total
    );

    /*Redirigir con mensaje de exito*/
    $params = "exito=reserva_confirmada";
    $params .= "&vehiculo=" . urlencode($vehiculo["Marca"] . " " . $vehiculo["Modelo"]);
    $params .= "&anio=" . $vehiculo["Anio"];
    $params .= "&fecha_inicio=" . $fecha_inicio;
    $params .= "&fecha_fin=" . $fecha_fin;
    $params .= "&dias=" . $dias;
    $params .= "&costo=" . $costo_total;
    $params .= "&precio_dia=" . $vehiculo["Precio"];
    if ($correo_enviado) {
        $params .= "&correo=enviado";
    }
    header("Location: vehiculos.php?" . $params);
} else {
    header("Location: vehiculos.php?error=reserva_fallida");
}

$insertar_reserva->close();
$consulta_vehiculo->close();
$conexion->close();
exit();
?>
