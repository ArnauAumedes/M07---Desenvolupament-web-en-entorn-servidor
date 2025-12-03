<?php

require_once __DIR__ . '/../entities/Equipo.php';

class EquipoDAO implements DAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Crea un nuevo equipo
    public function create($equipo) {
        $sql = "INSERT INTO equipos (pos, equip, user_id, escudo, jugados, ganados, empatados, perdidos, puntos, gf_gc) VALUES (:pos, :equip, :user_id, :escudo, :jugados, :ganados, :empatados, :perdidos, :puntos, :gf_gc)";
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
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    // Actualiza un equipo existente
    public function update($equipo) {
        $sql = "UPDATE equipos SET pos = :pos, equip = :equip, user_id = :user_id, escudo = :escudo, jugados = :jugados, ganados = :ganados, empatados = :empatados, perdidos = :perdidos, puntos = :puntos, gf_gc = :gf_gc WHERE id = :id";
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
        $stmt->bindValue(':id', $equipo->getId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Elimina un equipo por id
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM equipos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Obtiene todos los equipos
    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM equipos ORDER BY pos ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $equipos = [];
        foreach ($rows as $row) {
            $equipo = new Equipo(
                $row['id'], $row['pos'], $row['equip'], $row['user_id'], $row['escudo'],
                $row['jugados'], $row['ganados'], $row['empatados'], $row['perdidos'], $row['puntos'], $row['gf_gc']
            );
            $equipos[] = $equipo;
        }
        return $equipos;
    }

    // Obtiene un equipo por id
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM equipos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Equipo(
                $row['id'], $row['pos'], $row['equip'], $row['user_id'], $row['escudo'],
                $row['jugados'], $row['ganados'], $row['empatados'], $row['perdidos'], $row['puntos'], $row['gf_gc']
            );
        }
        return null;
    }
}

?>