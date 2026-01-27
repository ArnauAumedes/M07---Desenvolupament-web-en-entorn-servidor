<?php

class CookieHelper
{
    // Obtiene el valor de una cookie, o un valor por defecto si no existe
    public static function get($name, $default = null)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

    // Establece una cookie (por defecto, expira en 30 días)
    public static function set($name, $value, $expire = 2592000, $path = "/")
    {
        setcookie($name, $value, time() + $expire, $path);
    }

    // Ejemplo específico para obtener la preferencia de orden
    public static function getOrderPreference($paramName = 'order', $cookieName = 'order_preference', $default = 'nombre')
    {
        if (isset($_GET[$paramName])) {
            self::set($cookieName, $_GET[$paramName]);
            return $_GET[$paramName];
        }
        return self::get($cookieName, $default);
    }
}