<?php

/**
 * InternalApiClient.php
 * Cliente HTTP para consumir la API interna del proyecto con contrato JSON uniforme.
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/../../config/env.php';

loadEnv(__DIR__ . '/../../.env');

class InternalApiClient
{
    /** @var string URL base de la API interna. */
    private string $baseUrl;

    /** @var int Timeout de conexion y lectura en segundos. */
    private int $timeoutSeconds;

    /** @var string API key usada para consumir la API interna. */
    private string $apiKey;

    /**
     * Inicializa el cliente HTTP interno.
     *
     * @param string|null $baseUrl URL base opcional para pruebas.
     * @param int $timeoutSeconds Timeout de peticion en segundos.
     * @return void
     */
    public function __construct(?string $baseUrl = null, int $timeoutSeconds = 5)
    {
        $this->baseUrl = rtrim($baseUrl ?? $this->buildBaseUrl(), '/');
        $this->timeoutSeconds = $timeoutSeconds;
        $this->apiKey = (string) (getenv('INTERNAL_API_KEY') ?: 'dev-internal-key');
    }

    /**
     * Ejecuta una peticion GET a la API interna y valida el contrato de respuesta.
     *
     * @param string $path Ruta relativa del recurso, por ejemplo equipos.
     * @param array $query Parametros query opcionales.
     * @return array Respuesta normalizada con payload, http_code y content_type.
     * @throws RuntimeException Cuando hay fallo de red, respuesta invalida o error funcional.
     */
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

    /**
     * Construye automaticamente la URL base de la API interna.
     *
     * @return string URL base construida.
     */
    private function buildBaseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $projectName = basename(realpath(__DIR__ . '/../..') ?: 'practicas');

        return $scheme . '://' . $host . '/' . $projectName . '/api';
    }
}
