<?php
/*
 * Pagina de registro de usuarios
 * Crea un nuevo usuario en la base de datos con tipo 'usuario' por defecto
 */
session_start();
require_once 'conexion.php';

/*Si ya tiene sesion activa, redirigir*/
if (isset($_SESSION["usuario_id"])) {
    header("Location: vehiculos.php");
    exit();
}

$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $contrasena = trim($_POST["contrasena"]);
    $rol = trim($_POST["rol"]);
    $tipo = "usuario"; /*Los usuarios registrados desde el formulario siempre son tipo 'usuario'*/

    /*Validar que los campos no esten vacios*/
    if (empty($nombre) || empty($correo) || empty($contrasena) || empty($rol)) {
        $mensaje = "Todos los campos son obligatorios.";
        $tipo_mensaje = "error";
    } else {
        /*Verificar si el correo ya existe*/
        $consulta = $conexion->prepare("SELECT ID FROM usuarios WHERE Correo = ?");
        $consulta->bind_param("s", $correo);
        $consulta->execute();
        $resultado = $consulta->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "El correo ya está registrado.";
            $tipo_mensaje = "error";
        } else {
            /*Insertar el nuevo usuario*/
            $insertar = $conexion->prepare("INSERT INTO usuarios (Correo, Contraseña, Rol, Nombre, Tipo) VALUES (?, ?, ?, ?, ?)");
            $insertar->bind_param("sssss", $correo, $contrasena, $rol, $nombre, $tipo);

            if ($insertar->execute()) {
                $mensaje = "Usuario registrado exitosamente. Ahora puedes iniciar sesión.";
                $tipo_mensaje = "exito";
            } else {
                $mensaje = "Error al registrar el usuario.";
                $tipo_mensaje = "error";
            }
            $insertar->close();
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
        <title>Registro - Renta de Carros XYZ</title>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="conocenos.html">Conócenos</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <li><a href="vehiculos.php">Vehículos</a></li>
                <li><a href="login.php">Iniciar Sesión</a></li>
                <li><a href="#">Registrarse</a></li>
            </ul>
        </nav>
    </header>
    <body>
        <head>
            <h1>Registro de Usuario</h1>
        </head>
        <main>
            <section>
                <h2>Crear Cuenta</h2>
                <?php if (!empty($mensaje)): ?>
                    <p class="mensaje-<?php echo $tipo_mensaje; ?>">
                        <?php echo $mensaje; ?>
                    </p>
                <?php endif; ?>
                <?php if ($tipo_mensaje === "exito"): ?>
                    <p><a href="login.php"><button type="button">Ir a Iniciar Sesión</button></a></p>
                <?php else: ?>
                    <form method="POST" action="registro.php">
                        <fieldset>
                            <div>
                                <label>Nombre:</label>
                                <br>
                                <input type="text" name="nombre" required />
                            </div>
                            <br>
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
                                <label>Rol:</label>
                                <br>
                                <input type="text" name="rol" required />
                            </div>
                            <br>
                            <div>
                                <button type="submit">Registrarse</button>
                            </div>
                        </fieldset>
                    </form>
                    <br>
                    <p>¿Ya tienes cuenta? <a href="login.php">Iniciar Sesión</a></p>
                <?php endif; ?>
            </section>
        </main>
        <footer>
        </footer>
    </body>
</html>
