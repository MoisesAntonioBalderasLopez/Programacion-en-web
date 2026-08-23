<?php
/*
 * Pagina de inicio de sesion
 * Verifica credenciales y redirige segun el tipo de usuario (admin o usuario)
 */
session_start();
require_once 'conexion.php';

$mensaje = "";
$tipo_mensaje = "";

/*Si ya tiene sesion activa, redirigir*/
if (isset($_SESSION["usuario_id"])) {
    if (isset($_SESSION["usuario_tipo"]) && $_SESSION["usuario_tipo"] === "admin") {
        header("Location: admin.php");
    } else {
        header("Location: vehiculos.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["correo"]);
    $contrasena = trim($_POST["contrasena"]);

    /*Validar que los campos no esten vacios*/
    if (empty($correo) || empty($contrasena)) {
        $mensaje = "Todos los campos son obligatorios.";
        $tipo_mensaje = "error";
    } else {
        /*Verificar si el usuario existe*/
        $consulta = $conexion->prepare("SELECT ID, Nombre, Contraseña, Rol, Tipo FROM usuarios WHERE Correo = ?");
        $consulta->bind_param("s", $correo);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();

            /*Verificar la contraseña*/
            if ($contrasena == $usuario["Contraseña"]) {
                $_SESSION["usuario_id"] = $usuario["ID"];
                $_SESSION["usuario_nombre"] = $usuario["Nombre"];
                $_SESSION["usuario_rol"] = $usuario["Rol"];
                $_SESSION["usuario_correo"] = $correo;
                $_SESSION["usuario_tipo"] = $usuario["Tipo"];

                /*Redirigir segun el tipo de usuario*/
                if ($usuario["Tipo"] === "admin") {
                    header("Location: admin.php");
                    exit();
                } else {
                    /*Si viene de una redireccion, enviar a vehiculos*/
                    if (isset($_GET["redireccion"]) && $_GET["redireccion"] === "vehiculos") {
                        header("Location: vehiculos.php");
                        exit();
                    }
                    header("Location: vehiculos.php");
                    exit();
                }
            } else {
                $mensaje = "Contraseña incorrecta.";
                $tipo_mensaje = "error";
            }
        } else {
            $mensaje = "No se encontró un usuario con ese correo.";
            $tipo_mensaje = "error";
        }
        $consulta->close();
    }
}

/*Verificar si viene redirigido desde vehiculos*/
$redireccion = isset($_GET["redireccion"]) ? $_GET["redireccion"] : "";
?>
<!DOCTYPE html>
<html lang="es">
    <header>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/style.css">
        <title>Iniciar Sesión - Renta de Carros XYZ</title>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="conocenos.html">Conócenos</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <li><a href="vehiculos.php">Vehículos</a></li>
                <li><a href="#">Iniciar Sesión</a></li>
                <li><a href="registro.php">Registrarse</a></li>
            </ul>
        </nav>
    </header>
    <body>
        <head>
            <h1>Iniciar Sesión</h1>
        </head>
        <main>
            <section>
                <h2>Acceder a tu Cuenta</h2>
                <?php if ($redireccion === "vehiculos"): ?>
                    <p class="mensaje-error">
                        🔒 Debes iniciar sesión para completar una reserva de vehículo.
                    </p>
                <?php endif; ?>
                <?php if (!empty($mensaje)): ?>
                    <p class="mensaje-<?php echo $tipo_mensaje; ?>">
                        <?php echo $mensaje; ?>
                    </p>
                <?php endif; ?>
                <form method="POST" action="login.php<?php echo $redireccion ? '?redireccion=' . $redireccion : ''; ?>">
                    <fieldset>
                        <div>
                            <label>Correo Electrónico:</label>
                            <br>
                            <input type="email" name="correo" required />
                        </div>
                        <br>
                        <div>
                            <label>Contraseña:</label>
                            <br>
                            <input type="password" name="contrasena" maxlength="10" required />
                        </div>
                        <br>
                        <div>
                            <button type="submit">Iniciar Sesión</button>
                        </div>
                    </fieldset>
                </form>
                <br>
                <p>¿No tienes cuenta? <a href="registro.php">Registrarse</a></p>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>