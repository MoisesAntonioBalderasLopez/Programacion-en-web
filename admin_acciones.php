<?php
/*
 * Acciones CRUD para la administracion de usuarios
 * Crear, Editar y Eliminar usuarios desde el panel de administracion
 */
session_start();
require_once 'conexion.php';

/*Verificar que el usuario es administrador*/
if (!isset($_SESSION["usuario_id"]) || !isset($_SESSION["usuario_tipo"]) || $_SESSION["usuario_tipo"] !== "admin") {
    header("Location: login.php");
    exit();
}

/*Determinar la accion a realizar*/
$accion = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["accion"])) {
    $accion = $_POST["accion"];
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["accion"])) {
    $accion = $_GET["accion"];
}

switch ($accion) {

    /*=== CREAR USUARIO ===*/
    case "crear":
        $nombre = trim($_POST["nombre"]);
        $correo = trim($_POST["correo"]);
        $contrasena = trim($_POST["contrasena"]);
        $rol = trim($_POST["rol"]);
        $tipo = trim($_POST["tipo"]);

        /*Validar campos*/
        if (empty($nombre) || empty($correo) || empty($contrasena) || empty($rol) || empty($tipo)) {
            header("Location: admin.php?msg=error");
            exit();
        }

        /*Verificar si el correo ya existe*/
        $verificar = $conexion->prepare("SELECT ID FROM usuarios WHERE Correo = ?");
        $verificar->bind_param("s", $correo);
        $verificar->execute();
        $resultado = $verificar->get_result();

        if ($resultado->num_rows > 0) {
            header("Location: admin.php?msg=error");
            exit();
        }

        /*Insertar nuevo usuario*/
        $insertar = $conexion->prepare("INSERT INTO usuarios (Nombre, Correo, Contraseña, Rol, Tipo) VALUES (?, ?, ?, ?, ?)");
        $insertar->bind_param("sssss", $nombre, $correo, $contrasena, $rol, $tipo);

        if ($insertar->execute()) {
            header("Location: admin.php?msg=creado");
        } else {
            header("Location: admin.php?msg=error");
        }
        $insertar->close();
        break;

    /*=== EDITAR USUARIO ===*/
    case "editar":
        $id = intval($_POST["id"]);
        $nombre = trim($_POST["nombre"]);
        $correo = trim($_POST["correo"]);
        $contrasena = trim($_POST["contrasena"]);
        $rol = trim($_POST["rol"]);
        $tipo = trim($_POST["tipo"]);

        /*Validar campos*/
        if (empty($id) || empty($nombre) || empty($correo) || empty($contrasena) || empty($rol) || empty($tipo)) {
            header("Location: admin.php?msg=error");
            exit();
        }

        /*Actualizar usuario*/
        $actualizar = $conexion->prepare("UPDATE usuarios SET Nombre = ?, Correo = ?, Contraseña = ?, Rol = ?, Tipo = ? WHERE ID = ?");
        $actualizar->bind_param("sssssi", $nombre, $correo, $contrasena, $rol, $tipo, $id);

        if ($actualizar->execute()) {
            header("Location: admin.php?msg=actualizado");
        } else {
            header("Location: admin.php?msg=error");
        }
        $actualizar->close();
        break;

    /*=== ELIMINAR USUARIO ===*/
    case "eliminar":
        $id = intval($_GET["id"]);

        /*No permitir eliminar su propia cuenta*/
        if ($id == $_SESSION["usuario_id"]) {
            header("Location: admin.php?msg=no_eliminar_propio");
            exit();
        }

        /*Eliminar usuario*/
        $eliminar = $conexion->prepare("DELETE FROM usuarios WHERE ID = ?");
        $eliminar->bind_param("i", $id);

        if ($eliminar->execute()) {
            header("Location: admin.php?msg=eliminado");
        } else {
            header("Location: admin.php?msg=error");
        }
        $eliminar->close();
        break;

    default:
        header("Location: admin.php");
        break;
}

$conexion->close();
exit();
?>
