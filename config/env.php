<?php
/**
 * env.php
 * Función para cargar variables de entorno desde un archivo .env
 * Autor: Arnau Aumedes Jimenez
 */

/**
 * Carrega les variables d'entorn des d'un fitxer .env
 * @param string $path Ruta del fitxer .env
 */
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = array_map('trim', explode('=', $line, 2));
        if (!getenv($name)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}