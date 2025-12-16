<?php

require_once __DIR__ . '/../entities/Jugador.php';
require_once __DIR__ . '/../dao/DAO.php';

class JugadorDAO extends Jugador implements DAO
{
    private $db;

    /**
     * Constructor de JugadorDAO
     * @param PDO $db Instancia de la conexión PDO
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Crea un nuevo jugador en la base de datos
     * @param Jugador $jugador Instancia del jugador a crear
     * @return int ID del nuevo jugador insertado
     */
    public function create($jugador)
    {
        $sql = "INSERT INTO jugadores (nombre_completo, equipo_id, valor, partidos, goles, asistencias) VALUES (:nombre_completo, :equipo_id, :valor, :partidos, :goles, :asistencias)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nombre_completo', $jugador->getNombreCompleto(), PDO::PARAM_STR);
        $stmt->bindValue(':equipo_id', $jugador->getEquipoId(), PDO::PARAM_INT);
        $stmt->bindValue(':valor', $jugador->getValor(), PDO::PARAM_STR);
        $stmt->bindValue(':partidos', $jugador->getPartidos(), PDO::PARAM_INT);
        $stmt->bindValue(':goles', $jugador->getGoles(), PDO::PARAM_INT);
        $stmt->bindValue(':asistencias', $jugador->getAsistencias(), PDO::PARAM_INT);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza un jugador existente en la base de datos
     * @param Jugador $jugador Instancia del jugador a actualizar
     * @return int Número de filas afectadas
     */
    public function update($jugador)
    {
        $sql = "UPDATE jugadores SET nombre_completo = :nombre_completo, equipo_id = :equipo_id, valor = :valor, partidos = :partidos, goles = :goles, asistencias = :asistencias WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nombre_completo', $jugador->getNombreCompleto(), PDO::PARAM_STR);
        $stmt->bindValue(':equipo_id', $jugador->getEquipoId(), PDO::PARAM_INT);
        $stmt->bindValue(':valor', $jugador->getValor(), PDO::PARAM_STR);
        $stmt->bindValue(':partidos', $jugador->getPartidos(), PDO::PARAM_INT);
        $stmt->bindValue(':goles', $jugador->getGoles(), PDO::PARAM_INT);
        $stmt->bindValue(':asistencias', $jugador->getAsistencias(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $jugador->getId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Elimina un jugador por su ID
     * @param int $id ID del jugador a eliminar
     * @return int Número de filas afectadas
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM jugadores WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Obtiene todos los jugadores de la base de datos
     * @return Jugador[] Array de instancias de Jugador
     */
    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM jugadores ORDER BY nombre_completo ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $jugadores = [];
        foreach ($rows as $row) {
            $jugador = new Jugador(
                $row['id'],
                $row['nombre_completo'],
                $row['equipo_id'],
                $row['valor'],
                $row['partidos'],
                $row['goles'],
                $row['asistencias']
            );
            $jugadores[] = $jugador;
        }
        return $jugadores;
    }

    /**
     * Obtiene un jugador por su ID
     * @param int $id ID del jugador
     * @return Jugador|null Instancia de Jugador o null si no existe
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM jugadores WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Jugador(
                $row['id'],
                $row['nombre_completo'],
                $row['equipo_id'],
                $row['valor'],
                $row['partidos'],
                $row['goles'],
                $row['asistencias']
            );
        }
        return null;
    }

    /**
     * Obtiene todos los jugadores de un equipo
     * @param int $equipoId ID del equipo
     * @return Jugador[] Array de instancias de Jugador
     */
    public function findByEquipoId($equipoId)
    {
        $stmt = $this->db->prepare("SELECT * FROM jugadores WHERE equipo_id = :equipo_id ORDER BY nombre_completo ASC");
        $stmt->bindValue(':equipo_id', $equipoId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $jugadores = [];
        foreach ($rows as $row) {
            $jugador = new Jugador(
                $row['id'],
                $row['nombre_completo'],
                $row['equipo_id'],
                $row['valor'],
                $row['partidos'],
                $row['goles'],
                $row['asistencias']
            );
            $jugadores[] = $jugador;
        }
        return $jugadores;
    }

    /**
     * Calcula el total de goles de un equipo
     * @param int $equipoId ID del equipo
     * @return int Total de goles del equipo
     */
    public function getTotalGolesEquipo($equipoId)
    {
        $sql = "SELECT SUM(goles) as total_goles FROM jugadores WHERE equipo_id = :equipo_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':equipo_id', $equipoId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['total_goles'] : 0;
    }

    /**
     * Calcula el total de asistencias de un equipo
     * @param int $equipoId ID del equipo
     * @return int Total de asistencias del equipo
     */
    public function getTotalAsistenciasEquipo($equipoId)
    {
        $sql = "SELECT SUM(asistencias) as total_asistencias FROM jugadores WHERE equipo_id = :equipo_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':equipo_id', $equipoId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['total_asistencias'] : 0;
    }

    /**
     * Calcula la media de un atributo (goles o asistencias) por partido para un jugador
     * @param int $jugadorId ID del jugador
     * @param string $atributo 'goles' o 'asistencias'
     * @return float Media del atributo por partido
     * @throws InvalidArgumentException Si el atributo no es válido
     */
    public function getMediaPorPartidoJugador($jugadorId, $atributo)
    {
        $validAttributes = ['goles', 'asistencias'];
        if (!in_array($atributo, $validAttributes)) {
            throw new InvalidArgumentException("Atributo no válido para calcular la media: " . $atributo);
        }

        $sql = "SELECT partidos, $atributo FROM jugadores WHERE id = :jugador_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':jugador_id', $jugadorId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['partidos'] > 0) {
            return $row[$atributo] / $row['partidos'];
        }
        return 0;
    }

    /**
     * Calcula la suma de goles y asistencias de un jugador
     * @param int $jugadorId ID del jugador
     * @return int Suma de goles y asistencias
     */
    public function getSumaGolesAsistencias($jugadorId)
    {
        $sql = "SELECT goles, asistencias FROM jugadores WHERE id = :jugador_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':jugador_id', $jugadorId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row['goles'] + $row['asistencias'];
        }
        return 0;
    }


    /**
     * Ordena un array de jugadores según un valor calculado por callback o método.
     * @param Jugador[] $jugadores Array de jugadores a ordenar
     * @param callable $valueCallback Callback que recibe el jugador y devuelve el valor para ordenar
     * @param string $order 'desc' para descendente, 'asc' para ascendente
     * @return Jugador[] Array ordenado
     */
    public function ordenarPorValor(array $jugadores, callable $valueCallback, string $order = 'desc')
    {
        usort($jugadores, function ($a, $b) use ($valueCallback, $order) {
            $valorA = $valueCallback($a);
            $valorB = $valueCallback($b);
            if ($order === 'desc') {
                return $valorB <=> $valorA;
            } else {
                return $valorA <=> $valorB;
            }
        });
        return $jugadores;
    }
}

?>