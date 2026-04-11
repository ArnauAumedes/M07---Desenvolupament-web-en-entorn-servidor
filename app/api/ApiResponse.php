<?php

class ApiResponse
{
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

    public static function success(string $msg = 'Operacion correcta', $data = [], array $meta = [], int $httpCode = 200): void
    {
        self::send($httpCode, true, $msg, $data, [], $meta);
    }

    public static function error(string $msg, int $httpCode = 500, array $errors = [], $data = [], array $meta = []): void
    {
        self::send($httpCode, false, $msg, $data, $errors, $meta);
    }

    public static function notFound(string $msg = 'Recurso no encontrado'): void
    {
        self::error($msg, 404);
    }

    public static function methodNotAllowed(string $msg = 'Metodo no permitido', array $allowedMethods = []): void
    {
        if (!empty($allowedMethods)) {
            header('Allow: ' . implode(', ', $allowedMethods));
        }
        self::error($msg, 405);
    }

    public static function validationError(string $msg = 'Error de validacion', array $errors = []): void
    {
        self::error($msg, 422, $errors);
    }
}
