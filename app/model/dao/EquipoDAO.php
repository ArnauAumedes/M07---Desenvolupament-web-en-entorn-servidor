<?php

require_once __DIR__ . '/../entities/Equipo.php';
require_once __DIR__ . '/../dao/DAO.php';

class EquipoDAO extends Equipo implements DAO
{
    private $db;

    /**
     * Constructor de EquipoDAO
     * @param PDO $db Instancia de la conexión PDO
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Crea un nuevo equipo en la base de datos
     * @param Equipo $equipo Instancia del equipo a crear
     * @return int ID del nuevo equipo insertado
     */
    public function create($equipo)
    {
        $sql = "INSERT INTO equipos (equip, user_id, escudo, jugados, ganados, empatados, perdidos, objetivo) VALUES (:equip, :user_id, :escudo, :jugados, :ganados, :empatados, :perdidos, :objetivo)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':equip', $equipo->getEquip(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $equipo->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':escudo', $equipo->getEscudo(), PDO::PARAM_STR);
        $stmt->bindValue(':jugados', $equipo->getJugados(), PDO::PARAM_INT);
        $stmt->bindValue(':ganados', $equipo->getGanados(), PDO::PARAM_INT);
        $stmt->bindValue(':empatados', $equipo->getEmpatados(), PDO::PARAM_INT);
        $stmt->bindValue(':perdidos', $equipo->getPerdidos(), PDO::PARAM_INT);
        $stmt->bindValue(':objetivo', $equipo->getObjetivo(), PDO::PARAM_INT);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza un equipo existente en la base de datos
     * @param Equipo $equipo Instancia del equipo a actualizar
     * @return int Número de filas afectadas
     */
    public function update($equipo)
    {
        $sql = "UPDATE equipos SET equip = :equip, user_id = :user_id, escudo = :escudo, jugados = :jugados, ganados = :ganados, empatados = :empatados, perdidos = :perdidos, objetivo = :objetivo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':equip', $equipo->getEquip(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $equipo->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':escudo', $equipo->getEscudo(), PDO::PARAM_STR);
        $stmt->bindValue(':jugados', $equipo->getJugados(), PDO::PARAM_INT);
        $stmt->bindValue(':ganados', $equipo->getGanados(), PDO::PARAM_INT);
        $stmt->bindValue(':empatados', $equipo->getEmpatados(), PDO::PARAM_INT);
        $stmt->bindValue(':perdidos', $equipo->getPerdidos(), PDO::PARAM_INT);
        $stmt->bindValue(':objetivo', $equipo->getObjetivo(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $equipo->getId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Elimina un equipo por su ID
     * @param int $id ID del equipo a eliminar
     * @return int Número de filas afectadas
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM equipos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Obtiene todos los equipos de la base de datos
     * @return Equipo[] Array de instancias de Equipo
     */
    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM equipos");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $equipos = [];
        foreach ($rows as $row) {
            $equipo = new Equipo(
                $row['id'],
                $row['equip'],
                $row['user_id'],
                $row['escudo'],
                $row['jugados'],
                $row['ganados'],
                $row['empatados'],
                $row['perdidos'],
                $row['objetivo'] ?? 0
            );
            $equipos[] = $equipo;
        }
        return $equipos;
    }

    /**
     * Obtiene un equipo por su ID
     * @param int $id ID del equipo
     * @return Equipo|null Instancia de Equipo o null si no existe
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM equipos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Equipo(
                $row['id'],
                $row['equip'],
                $row['user_id'],
                $row['escudo'],
                $row['jugados'],
                $row['ganados'],
                $row['empatados'],
                $row['perdidos'],
                $row['objetivo'] ?? 0
            );
        }
        return null;
    }

    /**
     * Funcio per obtenir el valor total dels jugadors d'un equip
     * @param mixed $equipoId ID de l'equip
     * @return float|int Valor total dels jugadors de l'equip
     */
    public function getValorEquipo($equipoId)
    {
        $sql = "SELECT SUM(valor) as valor_total FROM jugadores WHERE equipo_id = :equipo_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':equipo_id', $equipoId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float) $row['valor_total'] : 0;
    }

    /**
     * Funcio per obtenir la mitja del valor dels jugadors d'un equip
     * @param mixed $equipoId ID de l'equip
     * @return float|int Mitja del valor dels jugadors de l'equip
     */
    public function getMediaValorJugadores($equipoId)
    {
        $sql = "SELECT AVG(valor) as valor_media FROM jugadores WHERE equipo_id = :equipo_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':equipo_id', $equipoId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float) $row['valor_media'] : 0;
    }

    /**
     * Funcio per obtenir els punts d'un equip
     * @param mixed $equipoId ID de l'equip
     * @return int Punts totals de l'equip
     */
    public function getPuntos($equipoId)
    {
        $stmt = $this->db->prepare("SELECT ganados, empatados FROM equipos WHERE id = :id");
        $stmt->bindValue(':id', $equipoId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $ganados = (int) $row['ganados'];
            $empatados = (int) $row['empatados'];
            return ($ganados * 3) + ($empatados * 1);
        }
        return 0;
    }

    /**
     * Ordena un array de objetos (equipos o jugadores) según un valor calculado.
     * @param $items Array de objetos a ordenar
     * @param $value Recibe el objeto y devuelve el valor para ordenar
     * @param $order 'desc' para descendente, 'asc' para ascendente
     * @return array Array ordenado
     */
    public function ordenarPorValor($items, $value, $order = 'desc')
    {
        usort($items, function ($a, $b) use ($value, $order) {
            $valorA = $value($a);
            $valorB = $value($b);
            if ($order === 'desc') {
                return $valorB <=> $valorA;
            } else {
                return $valorA <=> $valorB;
            }
        });
        return $items;
    }

    /**
     * Devuelve la cantidad de jugadores de un equipo
     * @param int $equipoId
     * @return int
     */
    public function getCantidadJugadores($equipoId)
    {
        $sql = "SELECT COUNT(*) as cantidad FROM jugadores WHERE equipo_id = :equipo_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':equipo_id', $equipoId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['cantidad'] : 0;
    }

    /**
     * Calcula la diferencia entre el objetivo y la posición actual de un equipo.
     * Devuelve un array con el valor, el símbolo y la clase de color.
     * @param int $objetivo
     * @param int $posicionActual
     * @return array ['valor' => int, 'simbolo' =>, 'color' =>]
     */
    public function getDiferenciaObjetivoPosicion($objetivo, $posicionActual)
    {
        $diferencia = $objetivo - $posicionActual;
        if ($diferencia > 0) {
            return [
                'valor' => $diferencia,
                'simbolo' => '+',
                'color' => '#11461D'
            ];
        } elseif ($diferencia < 0) {
            return [
                'valor' => abs($diferencia),
                'simbolo' => '-',
                'color' => '#75151E'
            ];
        } else {
            return [
                'valor' => 0,
                'simbolo' => '',
                'color' => 'text-secondary'
            ];
        }
    }

    /**
     * Obtiene el valor mínimo de partidos jugados en la tabla equipos
     * @return int Valor mínimo de jugados
     */
    public function getMinJugados()
    {
        $sql = "SELECT MIN(jugados) as min_jugados FROM equipos";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['min_jugados'] : 0;
    }

    /**
     * Obtiene el valor máximo de partidos jugados en la tabla equipos
     * @return int Valor máximo de jugados
     */
    public function getMaxJugados()
    {
        $sql = "SELECT MAX(jugados) as max_jugados FROM equipos";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['max_jugados'] : 0;
    }
    
    /**
     * Funcio per comptar el nombre total d'equips a la base de dades
     * @return int Número total de equipos en la base de datos
     */
    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM equipos";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['total'] : 0;
    }

    /**
     * Obtiene equipos paginados
     * @param int $limit Número de registros por página
     * @param int $offset Desplazamiento de registros
     * @return Equipo[] Array de instancias de Equipo
     */
    public function getEquiposPaginados($limit, $offset)
    {
        $sql = "SELECT * FROM equipos LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $equipos = [];
        foreach ($rows as $row) {
            $equipos[] = new Equipo(
                $row['id'],
                $row['equip'],
                $row['user_id'],
                $row['escudo'],
                $row['jugados'],
                $row['ganados'],
                $row['empatados'],
                $row['perdidos'],
                $row['objetivo'] ?? 0
            );
        }
        return $equipos;
    }

    /**
     * Obtiene los equipos de un entrenador por su ID
     * @param int $entrenadorId ID del entrenador
     * @return Equipo[] Array de instancias de Equipo
     */
    function findByEntrenadorId($entrenadorId)
    {
        $stmt = $this->db->prepare("SELECT * FROM equipos WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $entrenadorId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $equipos = [];
        foreach ($rows as $row) {
            $equipos[] = new Equipo(
                $row['id'],
                $row['equip'],
                $row['user_id'],
                $row['escudo'],
                $row['jugados'],
                $row['ganados'],
                $row['empatados'],
                $row['perdidos'],
                $row['objetivo'] ?? 0
            );
        }
        return $equipos;
    }
}

?>