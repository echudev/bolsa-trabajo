<?php
// indicar al intérprete de PHP qué espacio de nombres va a usar PHP, para no tener que poner por ejemplo: new \PHPMailer\PHPMailer\PHPMailer()
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'libs/PHPMailer/PHPMailer.php';
require_once 'libs/PHPMailer/SMTP.php';
require_once 'libs/PHPMailer/Exception.php';



/**
 * MailerHelper.php
 * 
 * Provee una clase Mailer simple basada en PHPMailer para enviar correos electrónicos.
 * 
 * Permite:
 * - Configurar automáticamente la conexión SMTP.
 * - Enviar correos con cuerpo HTML y alternativo en texto plano.
 * - Manejar y recuperar errores de envío.
 * 
 * El helper se encapsula en la clase `Mailer`, que puede ser utilizada por controladores para tareas como:
 * - Confirmaciones de registro.
 * - Recuperación de contraseña.
 * - Notificaciones a usuarios.
 * 
 * Notas:
 * - Es importante configurar correctamente la cuenta de correo y los permisos de acceso (app passwords si se usa Gmail).
 * - La instancia se puede reutilizar, pero es necesario llamar a `clearAllRecipients()` en cada envío.
 * 
 * @package Helpers
 */

class Mailer
{
    private $mail;

    /**
     * Constructor.
     * 
     * Configura la instancia de PHPMailer con parámetros SMTP predeterminados para Gmail.
     * Se establece el remitente y que los correos serán enviados en formato HTML.
     * 
     * @throws Exception Si la configuración inicial de PHPMailer falla.
     */
    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'bariete.agenda@gmail.com';
        $this->mail->Password = 'fmrh hbwq xhkk hodl'; // EN PRODUCCIÓN MOVER A VARIABLE DE ENTORNO
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;
        $this->mail->setFrom('bariete.agenda@gmail.com', 'Bolsa de Trabajo');
        $this->mail->isHTML(true);
    }

    /**
     * Obtiene la información del último error producido por PHPMailer.
     * 
     * @return string Descripción del último error.
     */
    public function getErrorInfo()
    {
        return $this->mail->ErrorInfo;
    }


    /**
     * Envía un correo electrónico.
     * 
     * @param string $destinatario Email del destinatario.
     * @param string $nombreDestinatario Nombre del destinatario (opcional para mostrar en el cliente de correo).
     * @param string $asunto Asunto del correo.
     * @param string $cuerpoHtml Contenido HTML del correo.
     * @param string $cuerpoTexto Contenido alternativo en texto plano (si se omite, se usará una versión simplificada del cuerpo HTML).
     * 
     * @return bool true si el envío fue exitoso, false en caso de error.
     */
    public function enviar($destinatario, $nombreDestinatario, $asunto, $cuerpoHtml, $cuerpoTexto = '')
    {
        try {
            $this->mail->clearAllRecipients(); // importante si se reutiliza la instancia
            $this->mail->addAddress($destinatario, $nombreDestinatario);
            $this->mail->Subject = $asunto;
            $this->mail->Body    = $cuerpoHtml;
            $this->mail->AltBody = $cuerpoTexto ?: strip_tags($cuerpoHtml);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            // Se puede registrar $this->mail->ErrorInfo para diagnóstico en logs
            //echo '<pre style="color:red;">Mailer Error: ' . $this->mail->ErrorInfo . '</pre>';

            return false;
        }
    }
}
