<?php
/**
 * UserDAO
 *
 * Gestiona l'accés a la taula `users` per registre i autenticació.
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../entities/User.php';

class UserDAO extends User
{   
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Processa el formulari de registre
     * - Valida dades
     * - Comprova si l'usuari ja existeix
     * - Hashea la contrasenya i insereix l'usuari
     */
    public function processRegister()
    {
        // Sessió amb cookie de 40 minuts
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_lifetime', 2400);
            ini_set('session.gc_maxlifetime', 2400);
            session_set_cookie_params(2400);
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnRegister'])) {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';

            // Validacions bàsiques
            if ($username === '' || $email === '' || $password === '' || $password2 === '') {
                echo '<div class="alert alert-danger">Tots els camps són obligatoris</div>';
                return;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '<div class="alert alert-danger">Email invàlid</div>';
                return;
            }
            if ($password !== $password2) {
                echo '<div class="alert alert-danger">Les contrasenyes no coincideixen</div>';
                return;
            }
            // Comprovar força de la contrasenya (mínim 8, lletres + números)
            if (strlen($password) < 8 || !preg_match('/[A-Z]/i', $password) || !preg_match('/[0-9]/', $password)) {
                echo '<div class="alert alert-danger">La contrasenya ha de tenir almenys 8 caràcters i incloure lletres i números</div>';
                return;
            }

                // Comprovar si existeix usuari per email
                try {
                    $check = $this->db->prepare('SELECT user_id FROM users WHERE email = :email LIMIT 1');
                    $check->execute([':email' => $email]);
                    if ($check->fetch()) {
                        echo '<div class="alert alert-danger">Ja existeix un usuari amb aquest email</div>';
                        return;
                    }

                    // Comprovar si existeix usuari per username (evitar duplicats)
                    $checkUser = $this->db->prepare('SELECT user_id FROM users WHERE username = :username LIMIT 1');
                    $checkUser->execute([':username' => $username]);
                    if ($checkUser->fetch()) {
                        echo '<div class="alert alert-danger">Ja existeix un usuari amb aquest nom d\'usuari</div>';
                        return;
                    }

                    // Hashear password
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    // Insert into users without dni column (dni does not exist in this schema)
                    $stmt = $this->db->prepare('INSERT INTO users (username, email, password, active) VALUES (:username, :email, :password, 1)');
                    $stmt->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':password' => $hash
                    ]);

                // Login automàtic després de registrar
                $userId = $this->db->lastInsertId();
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'user_id' => $userId,
                    'username' => $username,
                    'email' => $email
                ];
                $_SESSION['flash_welcome'] = $username;
                header('Location: /practicas/M07---Desenvolupament-web-en-entorn-servidor/public/index.php?action=menu');
                exit;
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Error del servidor. Torna-ho a intentar més tard.</div>';
                echo '<div class="alert alert-warning"><small>Debug: ' . htmlspecialchars($e->getMessage()) . '</small></div>';
                error_log('Register error: ' . $e->getMessage());
                return;
            }
        }
    }

    /**
     * Processa el formulari de login
     * 
     * Valida les credencials i inicia la sessió si són correctes.
     * Utilitza prepared statements per prevenir SQL Injection.
     * 
     * @return void
     */
    public function processLogin()
    {
        // Procesamiento del formulario de login
        // Aseguramos una sesión con cookie de 40 minutos (2400s)
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_lifetime', 2400);
            ini_set('session.gc_maxlifetime', 2400);
            session_set_cookie_params(2400);
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSubmit'])) {
            // Validar campos
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                echo '<div class="alert alert-danger">ELS CAMPS NO PODEN ESTAR BUITS</div>';
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '<div class="alert alert-danger">Email invàlid</div>';
                return;
            }

            // La connexió PDO s'ha de passar al constructor i estar en $this->db
            if (!isset($this->db) || !($this->db instanceof PDO)) {
                echo '<div class="alert alert-danger">Error de configuració: connexió a la BD no trobada.</div>';
                return;
            }

            try {
                $stmt = $this->db->prepare('SELECT user_id, username, email, password, active FROM users WHERE email = :email LIMIT 1');
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    echo '<div class="alert alert-danger">Credencials incorrectes.</div>';
                    return;
                }

                // Determinar quina columna guarda la contrasenya (password o password)
                $hashColumn = null;
                if (isset($user['password'])) {
                    $hashColumn = 'password';
                } elseif (isset($user['password'])) {
                    $hashColumn = 'password';
                }

                if ($hashColumn === null) {
                    // Mostrar keys per ajudar a depurar (temporal)
                    $cols = implode(', ', array_keys($user));
                    echo '<div class="alert alert-danger">Error al verificar la contrasenya: la fila d\'usuari no conté cap columna de contrasenya coneguda. Columnes: ' . htmlspecialchars($cols) . '</div>';
                    return;
                }

                // Verificar contrasenya
                if (!password_verify($password, $user[$hashColumn])) {
                    echo '<div class="alert alert-danger">Credencials incorrectes.</div>';
                    return;
                }

                // Comprovar si el compte està actiu (opcional)
                if (isset($user['active']) && !$user['active']) {
                    echo '<div class="alert alert-danger">El compte no està actiu.</div>';
                    return;
                }

                // Login correcte: guardar dades en sessió
                // Regenerar id de sessió per seguretat
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'dni' => $user['dni'] ?? null
                ];

                // Preparar missatge flash de benvinguda (es mostrarà una sola vegada al header)
                $_SESSION['flash_welcome'] = $user['username'] ?? ($user['email'] ?? 'Usuari');

                // Redirigir al menú / pàgina principal
                header('Location: /practicas/M07---Desenvolupament-web-en-entorn-servidor/public/index.php?action=menu');
                exit;
            } catch (Exception $e) {
                // Mostrar missatge genèric per a l'usuari i el missatge d'error real per a debug
                echo '<div class="alert alert-danger">Error del servidor. Torna a intentar-ho més tard.</div>';
                // Debug (temporal): mostrar excepció per ajudar a localitzar el problema
                echo '<div class="alert alert-warning"><small>Debug: ' . htmlspecialchars($e->getMessage()) . '</small></div>';
                error_log('Login error: ' . $e->getMessage());
                return;
            }
        }
    }

    public function updatePasswordByEmail($email, $hashedPassword, $trnDate) {
        $stmt = $this->db->prepare('UPDATE users SET password = :password, trn_date = :trn_date WHERE email = :email');
        $stmt->execute([
            ':password' => $hashedPassword,
            ':trn_date' => $trnDate,
            ':email' => $email
        ]);
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>