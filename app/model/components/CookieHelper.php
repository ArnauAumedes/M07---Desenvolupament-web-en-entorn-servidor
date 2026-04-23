<?php
/**
 * CookieHelper.php
 * Componente para la gestión de cookies de preferencias de ordenación, paginación y límite de resultados
 * Autor: Arnau Aumedes Jimenez
 */
class CookieHelper
{
    /**
     * Obtiene el valor de una cookie.
     * @param string $name Nombre de la cookie
     * @param ?string $default Valor por defecto si la cookie no existe
     */
    public static function get($name, $default = null)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

    // Establece una cookie (por defecto, expira en 30 días)
    public static function set($name, $value, $expire = 2592000, $path = "/")
    {
        // Mantener valor disponible en esta misma request aunque no se pueda enviar cabecera
        $_COOKIE[$name] = (string) $value;

        if (!headers_sent()) {
            setcookie($name, (string) $value, time() + $expire, $path);
            return;
        }

        error_log('[cookie] headers ya enviados, no se puede setear cookie: ' . $name);
    }
    /**
     * Obtiene la preferencia de ordenación del usuario.
     * @param string $paramName Nombre del parámetro GET
     * @param string $cookieName Nombre de la cookie
     * @param ?string $default Valor por defecto si no existe
     */
    public static function getOrderPreference($paramName = 'order', $cookieName = 'order_preference', $default = null)
    {
        $validOrders = ['asc', 'desc'];
        $normalizedDefault = in_array($default, $validOrders, true) ? $default : 'desc';

        if (isset($_GET[$paramName])) {
            $requested = strtolower(trim((string) $_GET[$paramName]));
            if (in_array($requested, $validOrders, true)) {
                self::set($cookieName, $requested);
                return $requested;
            }

            self::set($cookieName, $normalizedDefault);
            return $normalizedDefault;
        }

        $stored = strtolower(trim((string) self::get($cookieName, $normalizedDefault)));
        return in_array($stored, $validOrders, true) ? $stored : $normalizedDefault;
    }

    /**
     * Obtiene la preferencia de página del usuario.
     * @param string $paramName Nombre del parámetro GET
     * @param string $cookieName Nombre de la cookie
     * @param int $default Valor por defecto si no existe
     */
    public static function getPagePreference($paramName = 'page', $cookieName = 'page_preference', $default = 1)
    {
        if (isset($_GET[$paramName]) && is_numeric($_GET[$paramName]) && $_GET[$paramName] > 0) {
            self::set($cookieName, $_GET[$paramName]);
            return (int) $_GET[$paramName];
        }
        $page = self::get($cookieName, $default);
        return is_numeric($page) && $page > 0 ? (int) $page : $default;
    }

    /**
     * Obtiene la preferencia de límite de resultados del usuario.
     * @param string $paramName Nombre del parámetro GET
     * @param string $cookieName Nombre de la cookie
     * @param int $default Valor por defecto si no existe
     */
    public static function getLimitPreference($paramName = 'limit', $cookieName = 'limit_preference', $default = 5)
    {
        $validLimits = [1, 5, 10, 20];
        if (isset($_GET[$paramName]) && is_numeric($_GET[$paramName])) {
            $limit = (int) $_GET[$paramName];
            if (in_array($limit, $validLimits)) {
                self::set($cookieName, $limit);
                return $limit;
            }
        }
        $limit = self::get($cookieName, $default);
        return (is_numeric($limit) && in_array((int) $limit, $validLimits)) ? (int) $limit : $default;
    }

    /**
     * Obtiene la preferencia de fuente de datos (bdd|api).
     * Prioriza query param, luego cookie, y por ultimo fallback.
     *
     * @param string $paramName Nombre del parámetro GET
     * @param string $cookieName Nombre de la cookie
     * @param string $default Valor por defecto si no existe
     * @return string
     */
    public static function getSourcePreference($paramName = 'source', $cookieName = 'data_source_preference', $default = 'bdd')
    {
        $validSources = ['bdd', 'api'];

        if (isset($_GET[$paramName])) {
            $source = strtolower(trim((string) $_GET[$paramName]));
            if (in_array($source, $validSources, true)) {
                self::set($cookieName, $source);
                return $source;
            }
            error_log('[data-source] source invalido recibido: ' . $source);
            self::set($cookieName, $default);
            return $default;
        }

        $source = strtolower(trim((string) self::get($cookieName, $default)));
        return in_array($source, $validSources, true) ? $source : $default;
    }
}