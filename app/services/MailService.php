<?php
/**
 * MailService
 * Wrapper simple per PHPMailer. 
 * Autor: Arnau Aumedes Jimenez
 */

// Forçar carga manual de PHPMailer 
require_once __DIR__ . '/../../app/lib/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../app/lib/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../app/lib/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $config;

    public function __construct(?array $config = null)
    {
        if ($config !== null) {
            $this->config = $config;
            return;
        }

        // Try load config file app/config/smtp.php if exists
        $cfgFile = __DIR__ . '/../config/smtp.php';
        if (file_exists($cfgFile)) {
            $this->config = require $cfgFile;
            return;
        }

        // Default empty config (will fail on send)
        $this->config = [];
    }

    /**
     * Send password reset email
     * @param string $toEmail
     * @param string $toName
     * @param string $resetLink
     * @return bool
     */
    public function sendPasswordReset(string $toEmail, string $toName, string $resetLink): bool
    {
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            error_log('MailService: PHPMailer not found. Install via composer or place PHPMailer in app/lib/phpmailer.');
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            // Basic config validation
            $host = $this->config['host'] ?? null;
            $username = $this->config['username'] ?? null;
            $password = $this->config['password'] ?? null;
            $port = $this->config['port'] ?? 587;
            $smtp_secure = $this->config['smtp_secure'] ?? 'tls';
            $from_email = $this->config['from_email'] ?? $username;
            $from_name = $this->config['from_name'] ?? 'NoReply';

            if (!$host || !$username || !$password) {
                error_log('MailService: SMTP configuration missing.');
                return false;
            }

            // Server settings
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->SMTPAuth = true;
            $mail->Username = $username;
            $mail->Password = $password;
            $mail->SMTPSecure = $smtp_secure;
            $mail->Port = $port;

            // Recipients
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($toEmail, $toName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Restabliment de contrasenya';
            $mail->Body = "Hola " . htmlspecialchars($toName) . ",<br><br>Per restablir la teva contrasenya fes clic l'enllaç següent:<br><a href='" . htmlspecialchars($resetLink) . "'>Restablir contrasenya</a><br><br>Aquest enllaç caduca en 1 hora.";
            $mail->AltBody = 'Usa aquest enllaç per restablir la contrasenya: ' . $resetLink;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('MailService send error: ' . $e->getMessage());
            return false;
        }
    }
}

?>