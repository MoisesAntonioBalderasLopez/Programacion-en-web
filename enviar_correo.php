<?php
/*
 * Configuracion de PHPMailer para envio de correos de confirmacion
 * INSTRUCCIONES: Reemplaza los valores de $correo_remitente y $contrasena_app
 * con tus credenciales reales de Gmail.
 * Para obtener una contraseña de aplicacion:
 * 1. Ve a https://myaccount.google.com/apppasswords
 * 2. Genera una nueva contraseña de aplicacion para "Correo"
 * 3. Copia la contraseña generada y pegala aqui
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

function enviarCorreoConfirmacion($destinatario, $nombreUsuario, $vehiculo, $fechaInicio, $fechaFin, $dias, $costoTotal) {
    $mail = new PHPMailer(true);

    /*=== CONFIGURACION SMTP - REEMPLAZA CON TUS DATOS ===*/
    $correo_remitente = 'TU_CORREO@gmail.com';      /* <-- Pon tu correo de Gmail aqui */
    $contrasena_app   = 'TU_CONTRASENA_APP';          /* <-- Pon tu contraseña de aplicacion aqui */
    $nombre_remitente = 'Renta de Carros XYZ';
    /*=====================================================*/

    try {
        /*Configuracion del servidor SMTP*/
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $correo_remitente;
        $mail->Password   = $contrasena_app;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        /*Remitente y destinatario*/
        $mail->setFrom($correo_remitente, $nombre_remitente);
        $mail->addAddress($destinatario, $nombreUsuario);

        /*Contenido del correo*/
        $mail->isHTML(true);
        $mail->Subject = '✅ Confirmación de Reserva - Renta de Carros XYZ';

        /*Formatear fechas para mostrar*/
        $fechaInicioFormateada = date('d/m/Y', strtotime($fechaInicio));
        $fechaFinFormateada = date('d/m/Y', strtotime($fechaFin));
        $costoFormateado = number_format($costoTotal, 2, '.', ',');
        $precioDia = number_format($vehiculo['Precio'], 2, '.', ',');

        /*Cuerpo del correo en HTML*/
        $mail->Body = "
        <html>
        <body style='font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif; background-color: #1a1a1a; color: #f0f0f0; padding: 0; margin: 0;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #222222; border-radius: 8px; overflow: hidden;'>
                <!-- Encabezado -->
                <div style='background-color: #DA3E44; padding: 25px; text-align: center;'>
                    <h1 style='color: white; margin: 0; font-size: 24px;'>🚗 Renta de Carros XYZ</h1>
                    <p style='color: #ffcccc; margin: 5px 0 0;'>Confirmación de Reserva</p>
                </div>

                <!-- Contenido -->
                <div style='padding: 30px;'>
                    <p style='color: #f0f0f0; font-size: 16px;'>Hola <strong>{$nombreUsuario}</strong>,</p>
                    <p style='color: #cccccc;'>Tu reserva ha sido confirmada exitosamente. Aquí están los detalles:</p>

                    <!-- Detalles de la reserva -->
                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                        <tr style='border-bottom: 1px solid #444;'>
                            <td style='padding: 12px; color: #999; font-size: 13px; text-transform: uppercase;'>Vehículo</td>
                            <td style='padding: 12px; color: #f0f0f0; font-weight: bold; text-align: right;'>{$vehiculo['Marca']} {$vehiculo['Modelo']}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #444;'>
                            <td style='padding: 12px; color: #999; font-size: 13px; text-transform: uppercase;'>Año</td>
                            <td style='padding: 12px; color: #f0f0f0; font-weight: bold; text-align: right;'>{$vehiculo['Anio']}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #444;'>
                            <td style='padding: 12px; color: #999; font-size: 13px; text-transform: uppercase;'>Fecha de Inicio</td>
                            <td style='padding: 12px; color: #f0f0f0; font-weight: bold; text-align: right;'>{$fechaInicioFormateada}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #444;'>
                            <td style='padding: 12px; color: #999; font-size: 13px; text-transform: uppercase;'>Fecha de Fin</td>
                            <td style='padding: 12px; color: #f0f0f0; font-weight: bold; text-align: right;'>{$fechaFinFormateada}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #444;'>
                            <td style='padding: 12px; color: #999; font-size: 13px; text-transform: uppercase;'>Días de Renta</td>
                            <td style='padding: 12px; color: #f0f0f0; font-weight: bold; text-align: right;'>{$dias} días</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #444;'>
                            <td style='padding: 12px; color: #999; font-size: 13px; text-transform: uppercase;'>Precio por Día</td>
                            <td style='padding: 12px; color: #f0f0f0; font-weight: bold; text-align: right;'>\${$precioDia} MXN</td>
                        </tr>
                    </table>

                    <!-- Total -->
                    <div style='background-color: #331a1b; border: 2px solid #DA3E44; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0;'>
                        <p style='color: #999; font-size: 12px; text-transform: uppercase; margin: 0 0 5px;'>Costo Total</p>
                        <p style='color: #DA3E44; font-size: 28px; font-weight: bold; margin: 0;'>\${$costoFormateado} MXN</p>
                    </div>

                    <p style='color: #cccccc; font-size: 14px;'>Gracias por tu preferencia. Si tienes alguna duda, no dudes en contactarnos.</p>
                </div>

                <!-- Pie de pagina -->
                <div style='background-color: #111111; padding: 15px; text-align: center; border-top: 2px solid #DA3E44;'>
                    <p style='color: #999; font-size: 12px; margin: 0;'>© " . date('Y') . " Renta de Carros XYZ — Todos los derechos reservados</p>
                </div>
            </div>
        </body>
        </html>";

        /*Texto alternativo sin HTML*/
        $mail->AltBody = "Confirmación de Reserva - Renta de Carros XYZ\n\n"
            . "Hola {$nombreUsuario},\n"
            . "Tu reserva ha sido confirmada.\n\n"
            . "Vehículo: {$vehiculo['Marca']} {$vehiculo['Modelo']} ({$vehiculo['Anio']})\n"
            . "Fecha de Inicio: {$fechaInicioFormateada}\n"
            . "Fecha de Fin: {$fechaFinFormateada}\n"
            . "Días: {$dias}\n"
            . "Costo Total: \${$costoFormateado} MXN\n\n"
            . "Gracias por tu preferencia.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        /*Si falla el envio, registrar el error pero no detener el proceso*/
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
        return false;
    }
}
?>
