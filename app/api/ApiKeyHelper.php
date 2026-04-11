<?php

require_once __DIR__ . '/ApiResponse.php';
require_once __DIR__ . '/../../config/env.php';

loadEnv(__DIR__ . '/../../.env');

class ApiKeyHelper
{
    private const DEFAULT_API_KEY = 'dev-internal-key';

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
