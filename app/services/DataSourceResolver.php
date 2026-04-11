<?php

/**
 * DataSourceResolver.php
 * Resuelve y persiste la fuente de datos activa (bdd o api).
 * Autor: Arnau Aumedes Jimenez
 */

class DataSourceResolver
{
    /**
     * Nombre de la cookie donde se persiste la fuente elegida.
     */
    public const COOKIE_NAME = 'data_source_preference';

    /**
     * Fuente por defecto cuando no hay source valido.
     */
    public const DEFAULT_SOURCE = 'bdd';

    /**
     * Fuentes permitidas por la aplicacion.
     */
    private const VALID_SOURCES = ['bdd', 'api'];

    /**
     * Resuelve la fuente activa con prioridad query > cookie > default.
     *
     * @return string Fuente resuelta, solo bdd o api.
     */
    public static function resolve(): string
    {
        if (isset($_GET['source'])) {
            $source = strtolower(trim((string) $_GET['source']));

            if (self::isValid($source)) {
                self::persist($source);
                return $source;
            }

            error_log('[data-source] source invalido recibido: ' . $source);
            self::persist(self::DEFAULT_SOURCE);
            return self::DEFAULT_SOURCE;
        }

        $cookieSource = strtolower(trim((string) ($_COOKIE[self::COOKIE_NAME] ?? '')));
        if (self::isValid($cookieSource)) {
            return $cookieSource;
        }

        return self::DEFAULT_SOURCE;
    }

    /**
     * Valida si una fuente esta permitida.
     *
     * @param string $source Valor de fuente a validar.
     * @return bool True si la fuente es valida, false en caso contrario.
     */
    public static function isValid(string $source): bool
    {
        return in_array($source, self::VALID_SOURCES, true);
    }

    /**
     * Persiste la fuente en cookie y en el array $_COOKIE de la peticion actual.
     *
     * @param string $source Fuente a persistir.
     * @return void
     */
    public static function persist(string $source): void
    {
        $_COOKIE[self::COOKIE_NAME] = $source;

        if (!headers_sent()) {
            setcookie(self::COOKIE_NAME, $source, time() + 2592000, '/');
            return;
        }

        error_log('[data-source] headers ya enviados, no se puede persistir cookie');
    }
}
