<?php
/*
 * Panel de Administracion - CRUD de usuarios
 * Solo accesible para usuarios con Tipo = 'admin'
 * Utiliza DataTables para la tabla interactiva
 */
session_start();
require_once 'conexion.php';

/*Verificar que el usuario tiene sesion y es administrador*/
if (!isset($_SESSION["usuario_id"]) || !isset($_SESSION["usuario_tipo"]) || $_SESSION["usuario_tipo"] !== "admin") {
    header("Location: login.php");
    exit();
}

/*Obtener todos los usuarios de la base de datos*/
$consulta = $conexion->query("SELECT ID, Nombre, Correo, Contraseña, Rol, Tipo FROM usuarios ORDER BY ID ASC");
$usuarios = [];
while ($fila = $consulta->fetch_assoc()) {
    $usuarios[] = $fila;
}

$mensaje = "";
$tipo_mensaje = "";

/*Procesar mensajes de acciones realizadas*/
if (isset($_GET["msg"])) {
    switch ($_GET["msg"]) {
        case "eliminado":
            $mensaje = "Usuario eliminado exitosamente.";
            $tipo_mensaje = "exito";
            break;
        case "actualizado":
            $mensaje = "Usuario actualizado exitosamente.";
            $tipo_mensaje = "exito";
            break;
        case "creado":
            $mensaje = "Usuario creado exitosamente.";
            $tipo_mensaje = "exito";
            break;
        case "error":
            $mensaje = "Ocurrió un error al procesar la solicitud.";
            $tipo_mensaje = "error";
            break;
        case "no_eliminar_propio":
            $mensaje = "No puedes eliminar tu propia cuenta de administrador.";
            $tipo_mensaje = "error";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <header>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="vehiculos.css">
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <title>Panel de Administración - Renta de Carros XYZ</title>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="conocenos.html">Conócenos</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <li><a href="vehiculos.php">Vehículos</a></li>
                <li><a href="#">Admin Panel</a></li>
                <li><a href="logout.php">Cerrar Sesión (<?php echo $_SESSION["usuario_nombre"]; ?>)</a></li>
            </ul>
        </nav>
    </header>
    <body>
        <head>
            <h1>Panel de Administración</h1>
        </head>
        <main style="max-width: 1100px;">
            <?php if (!empty($mensaje)): ?>
                <p class="mensaje-<?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                </p>
            <?php endif; ?>

            <!-- Seccion: Crear nuevo usuario -->
            <section>
                <h2>Crear Nuevo Usuario</h2>
                <form method="POST" action="admin_acciones.php">
                    <input type="hidden" name="accion" value="crear">
                    <fieldset>
                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 180px;">
                                <label>Nombre:</label><br>
                                <input type="text" name="nombre" required />
                            </div>
                            <div style="flex: 1; min-width: 180px;">
                                <label>Correo:</label><br>
                                <input type="email" name="correo" required />
                            </div>
                            <div style="flex: 1; min-width: 120px;">
                                <label>Contraseña:</label><br>
                                <input type="password" name="contrasena" maxlength="10" required />
                            </div>
                            <div style="flex: 1; min-width: 120px;">
                                <label>Rol:</label><br>
                                <input type="text" name="rol" required />
                            </div>
                            <div style="flex: 1; min-width: 120px;">
                                <label>Tipo:</label><br>
                                <select name="tipo" style="width: 100%; padding: 12px 15px; margin-top: 8px; border: 1px solid #555; border-radius: 6px; background-color: #333; color: #f0f0f0; font-size: 1em;">
                                    <option value="usuario">Usuario</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <button type="submit">➕ Crear Usuario</button>
                        </div>
                    </fieldset>
                </form>
            </section>

            <!-- Seccion: Tabla de usuarios con DataTables -->
            <section>
                <h2>Gestión de Usuarios</h2>
                <p>Total de usuarios registrados: <strong><?php echo count($usuarios); ?></strong></p>
                <div style="overflow-x: auto;">
                    <table id="tabla-usuarios" class="display" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Contraseña</th>
                                <th>Rol</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario["ID"]; ?></td>
                                <td><?php echo htmlspecialchars($usuario["Nombre"]); ?></td>
                                <td><?php echo htmlspecialchars($usuario["Correo"]); ?></td>
                                <td><?php echo htmlspecialchars($usuario["Contraseña"]); ?></td>
                                <td><?php echo htmlspecialchars($usuario["Rol"]); ?></td>
                                <td>
                                    <span class="badge-tipo badge-<?php echo $usuario["Tipo"]; ?>">
                                        <?php echo $usuario["Tipo"] === "admin" ? "Admin" : "Usuario"; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-editar" onclick="abrirModalEditar(<?php echo $usuario['ID']; ?>, '<?php echo htmlspecialchars(addslashes($usuario['Nombre'])); ?>', '<?php echo htmlspecialchars(addslashes($usuario['Correo'])); ?>', '<?php echo htmlspecialchars(addslashes($usuario['Contraseña'])); ?>', '<?php echo htmlspecialchars(addslashes($usuario['Rol'])); ?>', '<?php echo $usuario['Tipo']; ?>')">
                                        ✏️ Editar
                                    </button>
                                    <?php if ($usuario["ID"] != $_SESSION["usuario_id"]): ?>
                                        <button class="btn-eliminar" onclick="confirmarEliminacion(<?php echo $usuario['ID']; ?>, '<?php echo htmlspecialchars(addslashes($usuario['Nombre'])); ?>')">
                                            🗑️ Eliminar
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Seccion: Tabla de reservas -->
            <section>
                <h2>Reservas Realizadas</h2>
                <?php
                $consulta_reservas = $conexion->query(
                    "SELECT r.ID, u.Nombre AS Usuario, u.Correo, v.Marca, v.Modelo, v.Anio,
                            r.FechaInicio, r.FechaFin, r.Dias, r.CostoTotal, r.FechaReserva
                     FROM reservas r
                     INNER JOIN usuarios u ON r.UsuarioID = u.ID
                     INNER JOIN vehiculos v ON r.VehiculoID = v.ID
                     ORDER BY r.FechaReserva DESC"
                );
                ?>
                <div style="overflow-x: auto;">
                    <table id="tabla-reservas" class="display" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Vehículo</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Días</th>
                                <th>Costo Total</th>
                                <th>Fecha Reserva</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($reserva = $consulta_reservas->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $reserva["ID"]; ?></td>
                                <td><?php echo htmlspecialchars($reserva["Usuario"]); ?></td>
                                <td><?php echo $reserva["Marca"] . " " . $reserva["Modelo"] . " (" . $reserva["Anio"] . ")"; ?></td>
                                <td><?php echo $reserva["FechaInicio"]; ?></td>
                                <td><?php echo $reserva["FechaFin"]; ?></td>
                                <td><?php echo $reserva["Dias"]; ?></td>
                                <td>$<?php echo number_format($reserva["CostoTotal"], 2, '.', ','); ?> MXN</td>
                                <td><?php echo $reserva["FechaReserva"]; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <!-- Modal de edicion -->
        <div id="modal-editar" class="modal-overlay" style="display: none;">
            <div class="modal-contenido">
                <h2 style="color: #DA3E44; margin-bottom: 20px;">✏️ Editar Usuario</h2>
                <form method="POST" action="admin_acciones.php">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" id="editar-id">
                    <div style="margin-bottom: 15px;">
                        <label>Nombre:</label><br>
                        <input type="text" name="nombre" id="editar-nombre" required />
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Correo:</label><br>
                        <input type="email" name="correo" id="editar-correo" required />
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Contraseña:</label><br>
                        <input type="text" name="contrasena" id="editar-contrasena" maxlength="10" required />
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Rol:</label><br>
                        <input type="text" name="rol" id="editar-rol" required />
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Tipo:</label><br>
                        <select name="tipo" id="editar-tipo" style="width: 100%; padding: 12px 15px; border: 1px solid #555; border-radius: 6px; background-color: #333; color: #f0f0f0; font-size: 1em;">
                            <option value="usuario">Usuario</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit">💾 Guardar Cambios</button>
                        <button type="button" onclick="cerrarModal()" style="background-color: #555;">❌ Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <footer>
        </footer>

        <!-- jQuery y DataTables JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

        <script>
            /*Inicializar DataTables*/
            $(document).ready(function() {
                $('#tabla-usuarios').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
                    },
                    pageLength: 10,
                    responsive: true
                });

                $('#tabla-reservas').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-MX.json'
                    },
                    pageLength: 10,
                    responsive: true,
                    order: [[0, 'desc']]
                });
            });

            /*Funcion para abrir el modal de edicion con los datos del usuario*/
            function abrirModalEditar(id, nombre, correo, contrasena, rol, tipo) {
                document.getElementById('editar-id').value = id;
                document.getElementById('editar-nombre').value = nombre;
                document.getElementById('editar-correo').value = correo;
                document.getElementById('editar-contrasena').value = contrasena;
                document.getElementById('editar-rol').value = rol;
                document.getElementById('editar-tipo').value = tipo;
                document.getElementById('modal-editar').style.display = 'flex';
            }

            /*Funcion para cerrar el modal*/
            function cerrarModal() {
                document.getElementById('modal-editar').style.display = 'none';
            }

            /*Cerrar modal al hacer clic fuera del contenido*/
            document.getElementById('modal-editar').addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModal();
                }
            });

            /*Funcion para confirmar eliminacion de usuario*/
            function confirmarEliminacion(id, nombre) {
                if (confirm('¿Estás seguro de que deseas eliminar al usuario "' + nombre + '"?\nEsta acción no se puede deshacer.')) {
                    window.location.href = 'admin_acciones.php?accion=eliminar&id=' + id;
                }
            }
        </script>
    </body>
</html>
