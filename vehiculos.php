<?php
/*
 * Pagina de vehiculos disponibles para renta
 * Incluye verificacion de sesion para reservas y redireccion de usuarios no autenticados
 */
session_start();

/*Verificar si el usuario tiene sesion activa*/
$sesion_activa = isset($_SESSION["usuario_id"]);
$nombre_usuario = $sesion_activa ? $_SESSION["usuario_nombre"] : "";
$es_admin = $sesion_activa && isset($_SESSION["usuario_tipo"]) && $_SESSION["usuario_tipo"] === "admin";
?>
<!DOCTYPE html>
<html lang="es">
    <header>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="vehiculos.css">
        <title>Vehículos - Renta de Carros XYZ</title>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="conocenos.html">Conócenos</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <li><a href="#">Vehículos</a></li>
                <?php if ($sesion_activa): ?>
                    <?php if ($es_admin): ?>
                        <li><a href="admin.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Cerrar Sesión (<?php echo $nombre_usuario; ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                    <li><a href="registro.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <body>
        <head>
            <h1>Vehículos Disponibles</h1>
        </head>
        <main>
            <?php if (isset($_GET["exito"]) && $_GET["exito"] === "reserva_confirmada"): ?>
                <div class="mensaje-exito" style="margin-bottom: 20px; padding: 15px;">
                    ✅ <strong>¡Reserva confirmada exitosamente!</strong>
                    <?php if (isset($_GET["correo"]) && $_GET["correo"] === "enviado"): ?>
                        <br>📧 Se ha enviado un correo de confirmación a tu dirección de correo electrónico.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET["error"])): ?>
                <div class="mensaje-error" style="margin-bottom: 20px; padding: 15px;">
                    ❌ <strong>
                    <?php
                    switch ($_GET["error"]) {
                        case "campos_vacios":
                            echo "Todos los campos son obligatorios.";
                            break;
                        case "fechas_invalidas":
                            echo "La fecha de finalización debe ser posterior a la fecha de inicio.";
                            break;
                        case "vehiculo_no_encontrado":
                            echo "El vehículo seleccionado no fue encontrado.";
                            break;
                        case "reserva_fallida":
                            echo "Hubo un error al procesar la reserva. Intenta nuevamente.";
                            break;
                        default:
                            echo "Ocurrió un error inesperado.";
                    }
                    ?>
                    </strong>
                </div>
            <?php endif; ?>

            <section>
                <h2>Nuestra Flota</h2>
                <p>Selecciona el vehículo que deseas rentar haciendo clic sobre él.</p>

                <div class="busqueda-container">
                    <div class="busqueda-campo">
                        <label for="busqueda-vehiculo">🔍 Buscar:</label>
                        <input type="text" id="busqueda-vehiculo" placeholder="Buscar por marca o modelo...">
                    </div>
                    <div class="busqueda-campo">
                        <label for="filtro-marca">🏷️ Filtrar por marca:</label>
                        <select id="filtro-marca">
                            <option value="">Todas las marcas</option>
                            <option value="Toyota">Toyota</option>
                            <option value="Honda">Honda</option>
                            <option value="Ford">Ford</option>
                            <option value="Chevrolet">Chevrolet</option>
                            <option value="BMW">BMW</option>
                            <option value="Mercedes-Benz">Mercedes-Benz</option>
                            <option value="Nissan">Nissan</option>
                            <option value="Volkswagen">Volkswagen</option>
                        </select>
                    </div>
                </div>

                <div id="vehiculos-grid" class="vehiculos-grid"></div>
            </section>

            <section id="seccion-reserva">
                <h2>Formulario de Reserva</h2>
                <?php if (!$sesion_activa): ?>
                    <div class="mensaje-sesion-requerida">
                        <p>🔒 <strong>Debes iniciar sesión para realizar una reserva.</strong></p>
                        <p>Si ya tienes cuenta, <a href="login.php?redireccion=vehiculos">inicia sesión aquí</a>. 
                           Si no, <a href="registro.php">regístrate primero</a>.</p>
                    </div>
                <?php else: ?>
                    <form id="formulario-reserva" method="POST" action="procesar_reserva.php">
                        <fieldset>
                            <div>
                                <label>Vehículo Seleccionado:</label>
                                <br>
                                <div id="vehiculo-seleccionado-display" class="vehiculo-display">
                                    Haz clic en un vehículo de la lista para seleccionarlo
                                </div>
                                <input type="hidden" id="vehiculo-id" name="vehiculo_id" value="">
                            </div>
                            <br>
                            <div>
                                <label for="fecha-inicio">Fecha de Inicio:</label>
                                <br>
                                <input type="date" id="fecha-inicio" name="fecha_inicio" required>
                            </div>
                            <br>
                            <div>
                                <label for="fecha-fin">Fecha de Finalización:</label>
                                <br>
                                <input type="date" id="fecha-fin" name="fecha_fin" required>
                            </div>
                            <br>
                            <div>
                                <button type="submit">Confirmar Reservación</button>
                            </div>
                        </fieldset>
                    </form>
                <?php endif; ?>
            </section>

            <div id="confirmacion-reserva">
                <?php if (isset($_GET["exito"]) && $_GET["exito"] === "reserva_confirmada"): ?>
                    <div class="confirmacion-section">
                        <div class="confirmacion-header">
                            <span class="confirmacion-icon">✅</span>
                            <div>
                                <div class="confirmacion-titulo">¡Reserva Confirmada!</div>
                                <div class="confirmacion-subtitulo">Resumen de tu reservación</div>
                            </div>
                        </div>
                        <div class="confirmacion-grid">
                            <div class="confirmacion-item">
                                <div class="confirmacion-item__label">Vehículo</div>
                                <div class="confirmacion-item__valor"><?php echo htmlspecialchars($_GET["vehiculo"] ?? ""); ?></div>
                            </div>
                            <div class="confirmacion-item">
                                <div class="confirmacion-item__label">Año</div>
                                <div class="confirmacion-item__valor"><?php echo htmlspecialchars($_GET["anio"] ?? ""); ?></div>
                            </div>
                            <div class="confirmacion-item">
                                <div class="confirmacion-item__label">Fecha de Inicio</div>
                                <div class="confirmacion-item__valor"><?php echo htmlspecialchars($_GET["fecha_inicio"] ?? ""); ?></div>
                            </div>
                            <div class="confirmacion-item">
                                <div class="confirmacion-item__label">Fecha de Fin</div>
                                <div class="confirmacion-item__valor"><?php echo htmlspecialchars($_GET["fecha_fin"] ?? ""); ?></div>
                            </div>
                            <div class="confirmacion-item">
                                <div class="confirmacion-item__label">Días de Renta</div>
                                <div class="confirmacion-item__valor"><?php echo htmlspecialchars($_GET["dias"] ?? ""); ?> días</div>
                            </div>
                            <div class="confirmacion-item">
                                <div class="confirmacion-item__label">Precio por Día</div>
                                <div class="confirmacion-item__valor">$<?php echo number_format(floatval($_GET["precio_dia"] ?? 0), 2, '.', ','); ?> MXN</div>
                            </div>
                            <div class="confirmacion-item confirmacion-total">
                                <div class="confirmacion-item__label">Costo Total de la Renta</div>
                                <div class="confirmacion-item__valor">$<?php echo number_format(floatval($_GET["costo"] ?? 0), 2, '.', ','); ?> MXN</div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </main>
        <footer>
        </footer>
    </body>
    <script>
        /*Variable para saber si el usuario tiene sesion activa*/
        var sesionActiva = <?php echo $sesion_activa ? 'true' : 'false'; ?>;
    </script>
    <script src="vehiculos.js"></script>
</html>
