<?php
session_start();
/**
 * Comprova si l'usuari està autenticat
 * @return bool Indica si l'usuari està autenticat
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}
/**
 * Obté l'ID de l'usuari actual
 * @return int|null Retorna l'ID de l'usuari actual o null si no està autenticat
 */
function currentUserID(): int | null {
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null; 
}

function login_user(array $userRow): void {
    // User row serà un array amb les dades de l'usuari obtingudes de la base de dades
    $_SESSION['user_id'] = (int) $userRow['id'];
    $_SESSION['gmail'] = $userRow['gmail'];
}

function loginUser(array $userRow): void {
    // User row serà un array amb les dades de l'usuari obtingudes de la base de dades
    $_SESSION['user_id'] = (int) $userRow['id'];
    $_SESSION['email'] = $userRow['email'];
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();
}
?>