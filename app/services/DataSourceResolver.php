<?php

class DataSourceResolver
{
    public const COOKIE_NAME = 'data_source_preference';
    public const DEFAULT_SOURCE = 'bdd';

    private const VALID_SOURCES = ['bdd', 'api'];

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

    public static function isValid(string $source): bool
    {
        return in_array($source, self::VALID_SOURCES, true);
    }

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
