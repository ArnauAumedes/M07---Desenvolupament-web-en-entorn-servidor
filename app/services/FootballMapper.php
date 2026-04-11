<?php

class FootballMapper
{
    public function validatePayload(array $providerPayload): void
    {
        if (!isset($providerPayload['data']) || !is_array($providerPayload['data'])) {
            throw new Exception("Provider JSON missing 'data' key");
        }
    }

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
