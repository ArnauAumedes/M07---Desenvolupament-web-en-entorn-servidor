<?php

require_once __DIR__ . '/FootballMapper.php';
require_once __DIR__ . '/../../config/env.php';

loadEnv(__DIR__ . '/../../.env');

class FootballApiService
{
    private FootballMapper $mapper;
    private int $timeoutSeconds;
    private int $cacheTtlSeconds;
    private string $baseUrl;
    private string $apiKey;

    public function __construct(?FootballMapper $mapper = null, int $timeoutSeconds = 8)
    {
        $this->mapper = $mapper ?? new FootballMapper();
        $this->timeoutSeconds = $timeoutSeconds;
        $this->cacheTtlSeconds = (int) (getenv('FOOTBALL_API_CACHE_TTL') ?: 120);
        $this->baseUrl = rtrim((string) (getenv('FOOTBALL_API_BASE_URL') ?: 'https://api.football-data.org/v4'), '/');
        $this->apiKey = (string) (getenv('FOOTBALL_API_KEY') ?: '');
    }

    public function getTeams(array $query = []): array
    {
        $competitionCode = $this->resolveCompetitionCode($query['competition'] ?? null);
        unset($query['competition']);

        $cacheKey = 'teams_' . $competitionCode . '_' . md5(http_build_query($query));
        $cachedPayload = $this->readCache($cacheKey);
        if (is_array($cachedPayload)) {
            return $this->mapper->mapTeams($cachedPayload);
        }

        $url = $this->baseUrl . '/competitions/' . rawurlencode($competitionCode) . '/teams';
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $headers = ['Accept: application/json'];
        if ($this->apiKey !== '') {
            $headers[] = 'X-Auth-Token: ' . $this->apiKey;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeoutSeconds);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutSeconds);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $responseBody = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseBody === false || $responseBody === null) {
            throw new Exception('Provider request failed: ' . $curlError);
        }

        if ($httpCode >= 400) {
            throw new Exception('Provider request failed with HTTP ' . $httpCode);
        }

        $json = json_decode($responseBody, true);

        if (!is_array($json)) {
            throw new Exception('Provider response is not valid JSON');
        }

        // Football-Data expone los equipos en la clave "teams".
        if (isset($json['teams']) && is_array($json['teams'])) {
            $json['data'] = $json['teams'];
        }

        if (!isset($json['data'])) {
            throw new Exception("Provider JSON missing 'data' key");
        }

        $this->writeCache($cacheKey, $json);

        return $this->mapper->mapTeams($json);
    }

    public function getStandings(?string $competitionCode = null): array
    {
        $resolvedCompetition = $this->resolveCompetitionCode($competitionCode);
        $cacheKey = 'standings_' . $resolvedCompetition;

        $cachedStats = $this->readCache($cacheKey);
        if (is_array($cachedStats)) {
            return $cachedStats;
        }

        $url = $this->baseUrl . '/competitions/' . rawurlencode($resolvedCompetition) . '/standings';

        $headers = ['Accept: application/json'];
        if ($this->apiKey !== '') {
            $headers[] = 'X-Auth-Token: ' . $this->apiKey;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeoutSeconds);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutSeconds);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $responseBody = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseBody === false || $responseBody === null) {
            throw new Exception('Provider request failed: ' . $curlError);
        }

        if ($httpCode >= 400) {
            throw new Exception('Provider request failed with HTTP ' . $httpCode);
        }

        $json = json_decode($responseBody, true);
        if (!is_array($json)) {
            throw new Exception('Provider response is not valid JSON');
        }

        if (!isset($json['standings']) || !is_array($json['standings'])) {
            throw new Exception("Provider JSON missing 'data' key");
        }

        $selectedStanding = null;
        foreach ($json['standings'] as $standing) {
            if (!is_array($standing)) {
                continue;
            }
            if (($standing['type'] ?? null) === 'TOTAL') {
                $selectedStanding = $standing;
                break;
            }
            if ($selectedStanding === null) {
                $selectedStanding = $standing;
            }
        }

        $table = is_array($selectedStanding['table'] ?? null) ? $selectedStanding['table'] : [];
        $statsByTeamId = [];

        foreach ($table as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $team = is_array($entry['team'] ?? null) ? $entry['team'] : [];
            $teamId = (int) ($team['id'] ?? 0);
            if ($teamId <= 0) {
                continue;
            }

            $statsByTeamId[$teamId] = [
                'jugados' => (int) ($entry['playedGames'] ?? 0),
                'ganados' => (int) ($entry['won'] ?? 0),
                'empatados' => (int) ($entry['draw'] ?? 0),
                'perdidos' => (int) ($entry['lost'] ?? 0),
                'puntos' => (int) ($entry['points'] ?? 0),
                'position' => (int) ($entry['position'] ?? 0),
            ];
        }

        $this->writeCache($cacheKey, $statsByTeamId);

        return $statsByTeamId;
    }

    private function resolveCompetitionCode(?string $competitionCode): string
    {
        return strtoupper((string) ($competitionCode ?: getenv('FOOTBALL_DEFAULT_COMPETITION') ?: 'PL'));
    }

    private function readCache(string $cacheKey)
    {
        $cacheFile = $this->getCacheFile($cacheKey);
        if (!is_file($cacheFile)) {
            return null;
        }

        $raw = @file_get_contents($cacheFile);
        if (!is_string($raw) || $raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || !isset($decoded['expires_at']) || !array_key_exists('payload', $decoded)) {
            return null;
        }

        if ((int) $decoded['expires_at'] < time()) {
            return null;
        }

        return $decoded['payload'];
    }

    private function writeCache(string $cacheKey, $payload): void
    {
        $cacheFile = $this->getCacheFile($cacheKey);
        $cacheData = [
            'expires_at' => time() + max(10, $this->cacheTtlSeconds),
            'payload' => $payload,
        ];

        @file_put_contents($cacheFile, json_encode($cacheData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function getCacheFile(string $cacheKey): string
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'practicas_football_cache_' . md5($cacheKey) . '.json';
    }
}
