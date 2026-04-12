<?php
/**
 * ForgotPasswordController.php
 * Controlador para la recuperación de contraseña por email
 * Autor: Arnau Aumedes Jimenez
 */
// app/controlador/ForgotPasswordController.php
require_once __DIR__ . '/../model/dao/UserDAO.php';
require_once __DIR__ . '/../model/dao/PasswordResetDAO.php';
use PHPMailer\PHPMailer\PHPMailer;

class ForgotPasswordController
{
    public $error = '';
    public $success = '';
    public $email = '';

    private $db;
    private $userDao;
    private $passwordResetDao;

    public function __construct($db = null)
    {
        if ($db === null) {
            // Cargar conexión PDO por defecto si no se pasa
            require_once __DIR__ . '/../../config/db-connection.php';
            $dbInstance = new Database();
            $this->db = $dbInstance->getConnection();
        } else {
            $this->db = $db;
        }
        $this->userDao = new UserDAO($this->db);
        $this->passwordResetDao = new PasswordResetDAO($this->db);
    }

    public function handleRequest()
    {
        if (isset($_POST["email"]) && (!empty($_POST["email"]))) {
            $this->email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
            $this->email = filter_var($this->email, FILTER_VALIDATE_EMAIL);
            if (!$this->email) {
                $this->error = "Invalid email address";
            } else {
                $user = $this->userDao->getByEmail($this->email);
                if (!$user) {
                    $this->error = "User Not Found";
                }
            }
            if ($this->error !== '') {
                return;
            }
            $expFormat = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y"));
            $expDate = date("Y-m-d H:i:s", $expFormat);
            $key = bin2hex(random_bytes(32));
            $this->passwordResetDao->insert($this->email, $key, $expDate);
            $output = '<p>Please click on the following link to reset your password.</p>';
            $output .= '<p><a href="http://localhost/practicas/index.php?action=reset-password&key=' . $key . '&email=' . $this->email . '" target="_blank">http://localhost/practicas/index.php?action=reset-password&key=' . $key . '&email=' . $this->email . '</a></p>';
            $body = $output;
            $subject = "Password Recovery";
            $email_to = $this->email;
            require_once __DIR__ . '/../../lib/PHPMailer/src/PHPMailer.php';
            require_once __DIR__ . '/../../lib/PHPMailer/src/SMTP.php';
            require_once __DIR__ . '/../../lib/PHPMailer/src/Exception.php';
            $mail = new PHPMailer();
            $config = include(__DIR__ . '/../../config/smtp.php');
            $mail->IsSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = $config['host'];
            $mail->SMTPSecure = $config['smtp_secure'];
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->Port = $config['port'];
            $mail->IsHTML(true);
            $mail->From = $config['from_email'];
            $mail->FromName = $config['from_name'];
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AddAddress($email_to);
            if (!$mail->Send()) {
                $this->error = "Mailer Error: " . $mail->ErrorInfo;
            } else {
                $this->success = "An email has been sent";
            }
        }
        $error = $this->error;
        $success = $this->success;
        $email = $this->email;
        require_once __DIR__ . '/../vista/send-email.php';
    }
}
