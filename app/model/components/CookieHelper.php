<?php

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
        setcookie($name, $value, time() + $expire, $path);
    }

    /**
     * Obtiene la preferencia de ordenación del usuario.
     * @param string $paramName Nombre del parámetro GET
     * @param string $cookieName Nombre de la cookie
     * @param ?string $default Valor por defecto si no existe
     */
    public static function getOrderPreference($paramName = 'order', $cookieName = 'order_preference', $default = null)
    {
        if (isset($_GET[$paramName])) {
            self::set($cookieName, $_GET[$paramName]);
            return $_GET[$paramName];
        }
        return self::get($cookieName, $default);
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
}