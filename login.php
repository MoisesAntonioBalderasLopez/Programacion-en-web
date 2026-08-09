<?php
session_start();
require_once 'conexion.php';

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["correo"]);
    $contrasena = trim($_POST["contrasena"]);

    /*Validar que los campos no esten vacios*/
    if (empty($correo) || empty($contrasena)) {
        $mensaje = "Todos los campos son obligatorios.";
        $tipo_mensaje = "error";
    } else {
        /*Verificar si el usuario existe*/
        $consulta = $conexion->prepare("SELECT ID, Nombre, Contraseña, Rol FROM usuarios WHERE Correo = ?");
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

                $mensaje = "Bienvenido, " . $usuario["Nombre"] . ". Has iniciado sesión correctamente.";
                $tipo_mensaje = "exito";
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
                <li><a href="vehiculos.html">Vehículos</a></li>
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
                <?php if (!empty($mensaje)): ?>
                    <p class="mensaje-<?php echo $tipo_mensaje; ?>">
                        <?php echo $mensaje; ?>
                    </p>
                <?php endif; ?>
                <?php if (isset($_SESSION["usuario_id"]) && $tipo_mensaje == "exito"): ?>
                    <p>
                        <strong>Nombre:</strong> <?php echo $_SESSION["usuario_nombre"]; ?><br>
                        <strong>Correo:</strong> <?php echo $_SESSION["usuario_correo"]; ?><br>
                        <strong>Rol:</strong> <?php echo $_SESSION["usuario_rol"]; ?>
                    </p>
                    <br>
                    <a href="vehiculos.html"><button type="button">Ir a Vehículos</button></a>
                <?php else: ?>
                    <form method="POST" action="login.php">
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
                <?php endif; ?>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>