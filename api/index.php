<?php

require_once __DIR__ . '/../app/api/ApiResponse.php';
require_once __DIR__ . '/../app/api/ApiKeyHelper.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$apiPos = strpos($requestUri, '/api');
$resourcePath = $apiPos !== false ? substr($requestUri, $apiPos + 4) : '/';
$segments = array_values(array_filter(explode('/', trim($resourcePath, '/')), 'strlen'));

if (count($segments) > 2) {
    ApiResponse::notFound('Ruta API no encontrada');
    exit;
}

$resource = $segments[0] ?? '';
$id = null;

if (isset($segments[1])) {
    if (!ctype_digit($segments[1])) {
        ApiResponse::error('ID invalido', 400, ['id debe ser numerico']);
        exit;
    }
    $id = (int) $segments[1];
}

try {
    switch ($resource) {
        case 'equipos':
            if (!ApiKeyHelper::validateOrFail()) {
                exit;
            }
            require_once __DIR__ . '/../app/api/EquipoApiController.php';
            $controller = new EquipoApiController();
            $controller->handle($method, $id);
            break;

        case 'jugadores':
            if (!ApiKeyHelper::validateOrFail()) {
                exit;
            }
            require_once __DIR__ . '/../app/api/JugadorApiController.php';
            $controller = new JugadorApiController();
            $controller->handle($method, $id);
            break;

        case 'usuarios':
            if (!ApiKeyHelper::validateOrFail()) {
                exit;
            }
            require_once __DIR__ . '/../app/api/UserApiController.php';
            $controller = new UserApiController();
            $controller->handle($method, $id);
            break;

        default:
            ApiResponse::notFound('Recurso API no encontrado');
            break;
    }
} catch (Throwable $e) {
    error_log('[api] error interno: ' . $e->getMessage());
    ApiResponse::error('Error interno controlado', 500);
}
