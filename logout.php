<?php
/*
 * Cerrar sesion del usuario
 * Destruye la sesion y redirige a la pagina de inicio
 */
session_start();
session_unset();
session_destroy();
header("Location: index.html");
exit();
?>
