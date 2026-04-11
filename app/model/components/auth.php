<?php
/**
 * auth.php
 * Funciones de autenticación y gestión de sesión
 * Autor: Arnau Aumedes Jimenez
 */
// Inicializar sesión si cal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Funcion qe comprueba si el usuario está logado o no
 * @return bool true si el usuario está logado, false en caso contrario
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user']) && is_array($_SESSION['user']);
}

/**
 * Funcion que devuelve la información del usuario logado (o null si no hay usuario logado)
 * @return array|null Un array con la información del usuario logado (puede contener 'id', 'email', 'dni', 'username', etc.) o null si no hay usuario logado
 */
function getLoggedUser(): ?array {
    if (!isLoggedIn()) return null;
    if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) return $_SESSION['user'];
    $u = [];
    if (!empty($_SESSION['user_id'])) $u['id'] = $_SESSION['user_id'];
    if (!empty($_SESSION['email'])) $u['email'] = $_SESSION['email'];
    if (!empty($_SESSION['dni'])) $u['dni'] = $_SESSION['dni'];
    return $u ?: null;
}

/**
 * Funcion que devuelve el DNI del usuario logado (o null si no hay usuario logado o no tiene DNI)
 * @return string|null El DNI del usuario logado o null si no hay usuario logado o no tiene DNI
 */
function getLoggedUserDni(): ?string {
    $u = getLoggedUser();
    return $u['dni'] ?? null;
}

/**
 * Funcion que redirige a la página de login si el usuario no está logado
 * @param string $redirect La URL a la que redirigir si el usuario no está logado (por defecto 'index.php')
 * @return void 
 */
function requireLogin(string $redirect = 'index.php'): void {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}