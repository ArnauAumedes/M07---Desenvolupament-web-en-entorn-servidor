<?php

/**
 * ApiResponse.php
 * Utilidad para construir respuestas JSON uniformes en la API.
 * Autor: Arnau Aumedes Jimenez
 */

class ApiResponse
{
    /**
     * Envia una respuesta JSON con el contrato comun de la API.
     *
     * @param int $httpCode Codigo HTTP de salida.
     * @param bool $status Estado funcional de la operacion.
     * @param string $msg Mensaje principal de la respuesta.
     * @param mixed $data Payload principal de la respuesta.
     * @param array $errors Lista de errores funcionales.
     * @param array $meta Metadatos adicionales de la respuesta.
     * @return void
     */
    public static function send(int $httpCode, bool $status, string $msg, $data = [], array $errors = [], array $meta = []): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode([
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
            'errors' => $errors,
            'meta' => $meta,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Envia una respuesta de exito.
     *
     * @param string $msg Mensaje de exito.
     * @param mixed $data Payload de salida.
     * @param array $meta Metadatos adicionales.
     * @param int $httpCode Codigo HTTP a devolver.
     * @return void
     */
    public static function success(string $msg = 'Operacion correcta', $data = [], array $meta = [], int $httpCode = 200): void
    {
        self::send($httpCode, true, $msg, $data, [], $meta);
    }

    /**
     * Envia una respuesta de error.
     *
     * @param string $msg Mensaje principal de error.
     * @param int $httpCode Codigo HTTP a devolver.
     * @param array $errors Lista de errores detallados.
     * @param mixed $data Datos adicionales del error.
     * @param array $meta Metadatos adicionales.
     * @return void
     */
    public static function error(string $msg, int $httpCode = 500, array $errors = [], $data = [], array $meta = []): void
    {
        self::send($httpCode, false, $msg, $data, $errors, $meta);
    }

    /**
     * Envia una respuesta de recurso no encontrado.
     *
     * @param string $msg Mensaje personalizado opcional.
     * @return void
     */
    public static function notFound(string $msg = 'Recurso no encontrado'): void
    {
        self::error($msg, 404);
    }

    /**
     * Envia una respuesta de metodo no permitido.
     *
     * @param string $msg Mensaje personalizado opcional.
     * @param array $allowedMethods Metodos HTTP permitidos para el recurso.
     * @return void
     */
    public static function methodNotAllowed(string $msg = 'Metodo no permitido', array $allowedMethods = []): void
    {
        if (!empty($allowedMethods)) {
            header('Allow: ' . implode(', ', $allowedMethods));
        }
        self::error($msg, 405);
    }

    /**
     * Envia una respuesta de validacion funcional.
     *
     * @param string $msg Mensaje principal de validacion.
     * @param array $errors Lista de errores de validacion.
     * @return void
     */
    public static function validationError(string $msg = 'Error de validacion', array $errors = []): void
    {
        self::error($msg, 422, $errors);
    }
}
