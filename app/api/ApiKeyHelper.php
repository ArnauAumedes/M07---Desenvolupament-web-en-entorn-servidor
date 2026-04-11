<?php

/**
 * ApiKeyHelper.php
 * Valida la API key de entrada para proteger recursos de la API interna.
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/ApiResponse.php';
require_once __DIR__ . '/../../config/env.php';

loadEnv(__DIR__ . '/../../.env');

class ApiKeyHelper
{
    /**
     * Clave por defecto de desarrollo cuando INTERNAL_API_KEY no esta definida.
     */
    private const DEFAULT_API_KEY = 'dev-internal-key';

    /**
     * Valida la API key entrante y envia respuesta de error si no es valida.
     *
     * @return bool True cuando la API key es valida, false en caso contrario.
     */
    public static function validateOrFail(): bool
    {
        $expectedKey = getenv('INTERNAL_API_KEY') ?: self::DEFAULT_API_KEY;
        $providedKey = self::getIncomingApiKey();

        if ($providedKey === null || $providedKey === '') {
            ApiResponse::error(
                'API key requerida',
                401,
                ['Missing API key. Use header X-API-Key or query param api_key.'],
                [],
                ['auth' => 'api-key']
            );
            return false;
        }

        if (!hash_equals((string) $expectedKey, (string) $providedKey)) {
            ApiResponse::error(
                'API key invalida',
                403,
                ['Invalid API key.'],
                [],
                ['auth' => 'api-key']
            );
            return false;
        }

        return true;
    }

    /**
     * Obtiene la API key de entrada.
     * Prioridad: cabecera X-API-Key (getallheaders/$_SERVER) y despues query api_key.
     *
     * @return string|null API key recibida o null cuando no existe.
     */
    private static function getIncomingApiKey(): ?string
    {
        $headerValue = null;

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                foreach ($headers as $name => $value) {
                    if (strtolower((string) $name) === 'x-api-key') {
                        $headerValue = is_string($value) ? trim($value) : null;
                        break;
                    }
                }
            }
        }

        if (($headerValue === null || $headerValue === '') && isset($_SERVER['HTTP_X_API_KEY'])) {
            $headerValue = trim((string) $_SERVER['HTTP_X_API_KEY']);
        }

        if ($headerValue !== null && $headerValue !== '') {
            return $headerValue;
        }

        if (isset($_GET['api_key'])) {
            return trim((string) $_GET['api_key']);
        }

        return null;
    }
}
