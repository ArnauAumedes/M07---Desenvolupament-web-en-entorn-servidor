<?php
// Inicializar sesión si cal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Devuelve true si hay un usuario autenticado.
 * Ajusta las claves de $_SESSION según tu implementación ('user','email','dni',...)
 */
function isLoggedIn(): bool {
    if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
        return true;
    }
    if (!empty($_SESSION['user_id']) || !empty($_SESSION['email']) || !empty($_SESSION['dni'])) {
        return true;
    }
    return false;
}

/** Devuelve array con datos del usuario si está logado o null */
function getLoggedUser(): ?array {
    if (!isLoggedIn()) return null;
    if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) return $_SESSION['user'];
    $u = [];
    if (!empty($_SESSION['user_id'])) $u['id'] = $_SESSION['user_id'];
    if (!empty($_SESSION['email'])) $u['email'] = $_SESSION['email'];
    if (!empty($_SESSION['dni'])) $u['dni'] = $_SESSION['dni'];
    return $u ?: null;
}

/** Devuelve el dni del usuari logat (o null) */
function getLoggedUserDni(): ?string {
    $u = getLoggedUser();
    return $u['dni'] ?? null;
}

/** Redirige si no está logado */
function requireLogin(string $redirect = '/practicas/Pràctica 03 - Paginació/public/index.php'): void {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}