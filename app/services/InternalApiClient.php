<?php

require_once __DIR__ . '/../../config/env.php';

loadEnv(__DIR__ . '/../../.env');

class InternalApiClient
{
    private string $baseUrl;
    private int $timeoutSeconds;
    private string $apiKey;

    public function __construct(?string $baseUrl = null, int $timeoutSeconds = 5)
    {
        $this->baseUrl = rtrim($baseUrl ?? $this->buildBaseUrl(), '/');
        $this->timeoutSeconds = $timeoutSeconds;
        $this->apiKey = (string) (getenv('INTERNAL_API_KEY') ?: 'dev-internal-key');
    }

    public function get(string $path, array $query = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeoutSeconds);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutSeconds);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-API-Key: ' . $this->apiKey,
        ]);

        $rawBody = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($rawBody === false || $rawBody === null) {
            throw new RuntimeException('Error de red consumiendo API interna: ' . $curlError);
        }

        $decoded = json_decode($rawBody, true);
        if (!is_array($decoded)) {
            $preview = trim(substr($rawBody, 0, 240));
            throw new RuntimeException('Respuesta no JSON de API interna: ' . ($preview !== '' ? $preview : 'sin contenido'));
        }

        if ($httpCode >= 400) {
            $message = $decoded['msg'] ?? 'Error en API interna';
            throw new RuntimeException($message);
        }

        if (!isset($decoded['status']) || !array_key_exists('data', $decoded)) {
            throw new RuntimeException('Contrato JSON invalido en API interna');
        }

        if ((bool) $decoded['status'] !== true) {
            $message = $decoded['msg'] ?? 'Error funcional en API interna';
            throw new RuntimeException($message);
        }

        return [
            'payload' => $decoded,
            'http_code' => $httpCode,
            'content_type' => $contentType,
        ];
    }

    private function buildBaseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $projectName = basename(realpath(__DIR__ . '/../..') ?: 'practicas');

        return $scheme . '://' . $host . '/' . $projectName . '/api';
    }
}
