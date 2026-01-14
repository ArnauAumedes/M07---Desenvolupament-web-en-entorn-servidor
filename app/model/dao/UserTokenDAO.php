<?php
require_once __DIR__ . '/../entities/UserToken.php';
require_once __DIR__ . '/../database/database.php';
require_once __DIR__ . '/DAO.php';

class UserTokenDAO extends UserToken implements DAO {
    private $db;
    public function __construct(PDO $db) { $this->db = $db; }

    /**
     * Crea un nou token d'usuari a la base de dades
     * @param UserToken $userToken Instancia de UserToken a crear
     * @return bool|string ID del nou token insertat 
     */
    public function create($userToken): bool {
        $sql = "INSERT INTO user_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userToken->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':token', $userToken->getToken(), PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', $userToken->getExpiresAt(), PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Actualitza un token d'usuari existent a la base de dades
     * @param UserToken $userToken Instancia de UserToken a actualitzar
     * @return bool Indica si l'actualització ha estat exitosa
     */
    public function update($userToken): bool {
        $sql = "UPDATE user_tokens SET user_id = :user_id, token = :token, expires_at = :expires_at WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userToken->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':token', $userToken->getToken(), PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', $userToken->getExpiresAt(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $userToken->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Elimina un token d'usuari de la base de dades
     * @param int $id ID del token a eliminar
     * @return bool Indica si l'eliminació ha estat exitosa
     */
    public function delete($id): bool {
        $sql = "DELETE FROM user_tokens WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obté un token d'usuari per ID
     * @param mixed $id ID del token a obtenir 
     * @return UserToken|null Instància de UserToken o null si no es troba
     */
    public function findById($id): ?UserToken {
        $sql = "SELECT * FROM user_tokens WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new UserToken(
                $row['user_id'],
                $row['token'],
                $row['expires_at']
            );
        }
        return null;
    }

    /**
     * Obté tots els tokens d'usuari
     * @return UserToken[] Array d'instàncies de UserToken
     */
    public function findAll(): array {
        $sql = "SELECT * FROM user_tokens";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $tokens = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tokens[] = new UserToken(
                $row['user_id'],
                $row['token'],
                $row['expires_at']
            );
        }
        return $tokens;
    }

}