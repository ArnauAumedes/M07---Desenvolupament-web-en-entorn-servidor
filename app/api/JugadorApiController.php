<?php

require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/dao/JugadorDAO.php';
require_once __DIR__ . '/ApiResponse.php';

class JugadorApiController
{
    private $jugadorDAO;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->jugadorDAO = new JugadorDAO($db);
    }

    public function handle(string $method, ?int $id = null): void
    {
        if ($method !== 'GET') {
            ApiResponse::methodNotAllowed('Metodo no permitido para jugadores', ['GET']);
            return;
        }

        if ($id === null) {
            $this->list();
            return;
        }

        $this->show($id);
    }

    private function list(): void
    {
        $jugadores = $this->jugadorDAO->findAll();
        $payload = [];

        foreach ($jugadores as $jugador) {
            $payload[] = $this->serializeJugador($jugador);
        }

        ApiResponse::success('Jugadores obtenidos correctamente', $payload, [
            'resource' => 'jugadores',
            'count' => count($payload),
        ]);
    }

    private function show(int $id): void
    {
        $jugador = $this->jugadorDAO->findById($id);
        if ($jugador === null) {
            ApiResponse::notFound('Jugador no encontrado');
            return;
        }

        ApiResponse::success('Jugador obtenido correctamente', $this->serializeJugador($jugador), [
            'resource' => 'jugadores',
        ]);
    }

    private function serializeJugador($jugador): array
    {
        $jugadorId = (int) $jugador->getId();
        $partidos = (int) $jugador->getPartidos();
        $goles = (int) $jugador->getGoles();
        $asistencias = (int) $jugador->getAsistencias();

        return [
            'id' => $jugadorId,
            'nombre_completo' => (string) $jugador->getNombreCompleto(),
            'equipo_id' => (int) $jugador->getEquipoId(),
            'valor' => (float) $jugador->getValor(),
            'partidos' => $partidos,
            'goles' => $goles,
            'asistencias' => $asistencias,
            'suma_goles_asistencias' => $goles + $asistencias,
            'media_goles_por_partido' => $partidos > 0 ? ($goles / $partidos) : 0,
            'media_asistencias_por_partido' => $partidos > 0 ? ($asistencias / $partidos) : 0,
        ];
    }
}
