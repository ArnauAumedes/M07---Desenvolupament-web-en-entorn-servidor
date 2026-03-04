<?php
/**
 * ChangePasswordController.php
 * Controlador para el cambio de contraseña
 * Estructura procedural similar a registerController.php
 */
require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';

$messages = '';
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    error_log('DB init error (change password controller): ' . $e->getMessage());
    $pdo = null;
}

if ($pdo instanceof PDO) {
    $userDAO = new UserDAO($pdo);
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_lifetime', 2400);
        ini_set('session.gc_maxlifetime', 2400);
        session_set_cookie_params(2400);
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnChangePassword'])) {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['password'] ?? '';
        $newPassword2 = $_POST['password2'] ?? '';

        // Validaciones
        if ($oldPassword === '' || $newPassword === '' || $newPassword2 === '') {
            $messages = '<div class="alert alert-danger">Todos los campos son obligatorios.</div>';
        } elseif ($newPassword !== $newPassword2) {
            $messages = '<div class="alert alert-danger">Las contraseñas nuevas no coinciden.</div>';
        } elseif (strlen($newPassword) < 8 || !preg_match('/[A-Z]/i', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
            $messages = '<div class="alert alert-danger">La nueva contraseña debe tener al menos 8 caracteres e incluir letras y números.</div>';
        } else {
            // Obtener usuario logueado
            $userId = $_SESSION['user']['user_id'] ?? null;
            if (!$userId) {
                $messages = '<div class="alert alert-danger">No hay usuario autenticado.</div>';
            } else {
                $user = $userDAO->findById($userId);
                if (!$user) {
                    $messages = '<div class="alert alert-danger">Usuario no encontrado.</div>';
                } elseif (!password_verify($oldPassword, $user->getPassword())) {
                    $messages = '<div class="alert alert-danger">La contraseña antigua no es correcta.</div>';
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    try {
                        $result = $userDAO->updatePassword($user->getId(), $hashedPassword);
                        if ($result) {
                            $messages = '<div class="alert alert-success">Contraseña cambiada correctamente.</div>';
                        } else {
                            $messages = '<div class="alert alert-danger">No se pudo cambiar la contraseña. Inténtalo de nuevo.</div>';
                        }
                    } catch (Exception $e) {
                        $messages = '<div class="alert alert-danger">Error del servidor. Inténtalo más tarde.</div>';
                        $messages .= '<div class="alert alert-warning"><small>Debug: ' . htmlspecialchars($e->getMessage()) . '</small></div>';
                        error_log('Change password error: ' . $e->getMessage());
                    }
                }
            }
        }
    }
} else {
    $messages = '<div class="alert alert-danger">Error de conexión a la base de datos.</div>';
}

require_once __DIR__ . '/../vista/change-password.php';
?>