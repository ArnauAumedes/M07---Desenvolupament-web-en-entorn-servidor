<?php

/**
 * FootballMapper.php
 * Normaliza payloads del proveedor de futbol al formato interno de la app.
 * Autor: Arnau Aumedes Jimenez
 */

class FootballMapper
{
    /**
     * Valida que el payload de proveedor incluya la clave data esperada.
     *
     * @param array $providerPayload Respuesta decodificada del proveedor.
     * @return void
     * @throws Exception Cuando la estructura no cumple el contrato minimo.
     */
    public function validatePayload(array $providerPayload): void
    {
        if (!isset($providerPayload['data']) || !is_array($providerPayload['data'])) {
            throw new Exception("Provider JSON missing 'data' key");
        }
    }

    /**
     * Mapea equipos del proveedor a un formato estable para servicios internos.
     *
     * @param array $providerPayload Payload validado con la clave data.
     * @return array Listado de equipos normalizado.
     */
    public function mapTeams(array $providerPayload): array
    {
        $this->validatePayload($providerPayload);

        $mapped = [];
        foreach ($providerPayload['data'] as $item) {
            if (!is_array($item)) {
                continue;
            }

            $venue = isset($item['venue']) && is_array($item['venue']) ? $item['venue'] : [];
            $area = isset($item['area']) && is_array($item['area']) ? $item['area'] : [];

            $country = $item['country'] ?? ($area['name'] ?? null);
            $logo = $item['logo'] ?? ($item['crest'] ?? null);

            $venueName = null;
            $venueCity = null;
            if (is_string($item['venue'] ?? null)) {
                $venueName = $item['venue'];
            } else {
                $venueName = $venue['name'] ?? null;
                $venueCity = $venue['city'] ?? null;
            }

            $mapped[] = [
                'id' => $item['id'] ?? null,
                'name' => $item['name'] ?? null,
                'country' => $country,
                'logo' => $logo,
                'founded' => $item['founded'] ?? null,
                'venue_name' => $venueName,
                'venue_city' => $venueCity,
                'raw' => $item,
            ];
        }

        return $mapped;
    }
}
