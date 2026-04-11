<?php

require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';
require_once __DIR__ . '/ApiResponse.php';

class EquipoApiController
{
    private $equipoDAO;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->equipoDAO = new EquipoDAO($db);
    }

    public function handle(string $method, ?int $id = null): void
    {
        if ($method !== 'GET') {
            ApiResponse::methodNotAllowed('Metodo no permitido para equipos', ['GET']);
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
        $equipos = $this->equipoDAO->findAll();
        $payload = [];

        foreach ($equipos as $equipo) {
            $payload[] = $this->serializeEquipo($equipo);
        }

        ApiResponse::success('Equipos obtenidos correctamente', $payload, [
            'resource' => 'equipos',
            'count' => count($payload),
        ]);
    }

    private function show(int $id): void
    {
        $equipo = $this->equipoDAO->findById($id);
        if ($equipo === null) {
            ApiResponse::notFound('Equipo no encontrado');
            return;
        }

        ApiResponse::success('Equipo obtenido correctamente', $this->serializeEquipo($equipo), [
            'resource' => 'equipos',
        ]);
    }

    private function serializeEquipo($equipo): array
    {
        $equipoId = (int) $equipo->getId();
        $ganados = (int) $equipo->getGanados();
        $empatados = (int) $equipo->getEmpatados();

        $cantidadJugadores = $this->equipoDAO->getCantidadJugadores($equipoId);

        return [
            'id' => $equipoId,
            'equip' => (string) $equipo->getEquip(),
            'entrenador' => $equipo->getEntrenador(),
            'escudo' => (string) $equipo->getEscudo(),
            'jugados' => (int) $equipo->getJugados(),
            'ganados' => $ganados,
            'empatados' => $empatados,
            'perdidos' => (int) $equipo->getPerdidos(),
            'objetivo' => (int) $equipo->getObjetivo(),
            'creador_id' => $equipo->getCreadorId() !== null ? (int) $equipo->getCreadorId() : null,
            'puntos' => ($ganados * 3) + $empatados,
            'valor_total' => (float) $this->equipoDAO->getValorEquipo($equipoId),
            'cantidad_jugadores' => (int) $cantidadJugadores,
            'valor_promedio' => $cantidadJugadores > 0 ? (float) $this->equipoDAO->getMediaValorJugadores($equipoId) : 0.0,
        ];
    }
}
