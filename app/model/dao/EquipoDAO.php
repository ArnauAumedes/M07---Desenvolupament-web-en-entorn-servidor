<?php

require_once __DIR__ . '/../entities/Equipo.php';
require_once __DIR__ . '/../dao/DAO.php';

class EquipoDAO extends Equipo implements DAO
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Crea un nuevo equipo
    public function create($equipo)
    {
        $sql = "INSERT INTO equipos (pos, equip, user_id, escudo, jugados, ganados, empatados, perdidos, puntos, gf_gc, objetivo) VALUES (:pos, :equip, :user_id, :escudo, :jugados, :ganados, :empatados, :perdidos, :puntos, :gf_gc, :objetivo)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pos', $equipo->getPos(), PDO::PARAM_INT);
        $stmt->bindValue(':equip', $equipo->getEquip(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $equipo->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':escudo', $equipo->getEscudo(), PDO::PARAM_STR);
        $stmt->bindValue(':jugados', $equipo->getJugados(), PDO::PARAM_INT);
        $stmt->bindValue(':ganados', $equipo->getGanados(), PDO::PARAM_INT);
        $stmt->bindValue(':empatados', $equipo->getEmpatados(), PDO::PARAM_INT);
        $stmt->bindValue(':perdidos', $equipo->getPerdidos(), PDO::PARAM_INT);
        $stmt->bindValue(':puntos', $equipo->getPuntos(), PDO::PARAM_INT);
        $stmt->bindValue(':gf_gc', $equipo->getGfGc(), PDO::PARAM_STR);
        $stmt->bindValue(':objetivo', $equipo->getObjetivo(), PDO::PARAM_INT);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    // Actualiza un equipo existente
    public function update($equipo)
    {
        $sql = "UPDATE equipos SET pos = :pos, equip = :equip, user_id = :user_id, escudo = :escudo, jugados = :jugados, ganados = :ganados, empatados = :empatados, perdidos = :perdidos, puntos = :puntos, gf_gc = :gf_gc, objetivo = :objetivo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pos', $equipo->getPos(), PDO::PARAM_INT);
        $stmt->bindValue(':equip', $equipo->getEquip(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $equipo->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':escudo', $equipo->getEscudo(), PDO::PARAM_STR);
        $stmt->bindValue(':jugados', $equipo->getJugados(), PDO::PARAM_INT);
        $stmt->bindValue(':ganados', $equipo->getGanados(), PDO::PARAM_INT);
        $stmt->bindValue(':empatados', $equipo->getEmpatados(), PDO::PARAM_INT);
        $stmt->bindValue(':perdidos', $equipo->getPerdidos(), PDO::PARAM_INT);
        $stmt->bindValue(':puntos', $equipo->getPuntos(), PDO::PARAM_INT);
        $stmt->bindValue(':gf_gc', $equipo->getGfGc(), PDO::PARAM_STR);
        $stmt->bindValue(':objetivo', $equipo->getObjetivo(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $equipo->getId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Elimina un equipo por id
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM equipos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Obtiene todos los equipos
    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM equipos ORDER BY pos ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $equipos = [];
        foreach ($rows as $row) {
            $equipo = new Equipo(
                $row['id'],
                $row['pos'],
                $row['equip'],
                $row['user_id'],
                $row['escudo'],
                $row['jugados'],
                $row['ganados'],
                $row['empatados'],
                $row['perdidos'],
                $row['puntos'],
                $row['gf_gc'],
                $row['objetivo'] ?? 0
            );
            $equipos[] = $equipo;
        }
        return $equipos;
    }

    // Obtiene un equipo por id
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM equipos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Equipo(
                $row['id'],
                $row['pos'],
                $row['equip'],
                $row['user_id'],
                $row['escudo'],
                $row['jugados'],
                $row['ganados'],
                $row['empatados'],
                $row['perdidos'],
                $row['puntos'],
                $row['gf_gc'],
                $row['objetivo'] ?? 0
            );
        }
        return null;
    }

    // Calcula el valor total del equipo sumando el valor de sus jugadores
    public function getValorEquipo($equipoId)
    {
        $sql = "SELECT SUM(valor) as valor_total FROM jugadores WHERE equipo_id = :equipo_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':equipo_id', $equipoId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float) $row['valor_total'] : 0;
    }

    // Calcula la media del valor de los jugadores de un equipo
    public function getMediaValorJugadores($equipoId)
    {
        $sql = "SELECT AVG(valor) as valor_media FROM jugadores WHERE equipo_id = :equipo_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':equipo_id', $equipoId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (float) $row['valor_media'] : 0;
    }
}

?>