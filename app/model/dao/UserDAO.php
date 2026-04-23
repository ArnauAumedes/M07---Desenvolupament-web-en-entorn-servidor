<?php
/**
 * UserDAO.php
 * Gestiona l'accés a la taula `users` per registre i autenticació.
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../entities/User.php';
require_once __DIR__ . '/DAO.php';

class UserDAO implements DAO
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Crea un nou usuari a la base de dades
     * @param User $user Instància de l'usuari a crear
     * @return string ID del nou usuari inserit
     */
    public function create($user)
    {
        $sql = 'INSERT INTO users (username, email, password, trn_date) VALUES (:username, :email, :password, :trn_date)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $stmt->bindValue(':trn_date', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Actualitza un usuari existent a la base de dades
     * @param User $user Instància de l'usuari a actualitzar
     * @return int Nombre de files afectades
     */
    public function update($user)
    {
        $sql = 'UPDATE users SET username = :username, email = :email, password = :password WHERE user_id = :user_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Actualitza el perfil d'un usuari (sense contrasenya)
     * @param User $user Instància de l'usuari a actualitzar
     * @return int Nombre de files afectades
     */
    public function updateProfile($user)
    {
        $sql = 'UPDATE users SET username = :username, email = :email WHERE user_id = :user_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Elimina un usuari de la base de dades
     * @param int $id ID de l'usuari a eliminar
     * @return int Nombre de files afectades
     */
    public function delete($id)
    {
        $sql = 'DELETE FROM users WHERE user_id = :user_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Busca un usuari per ID
     * @param int $id ID de l'usuari a cercar
     * @return User|null Instància de User o null si no es troba
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = :user_id LIMIT 1');
        $stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['password'],
                $row['active'],
                $row['isAdmin'],
                $row['created_at']
            );
        }
        return null;
    }

    public function findByName($name)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username LIKE :name ORDER BY username ASC");
        $stmt->bindValue(':name', "%" . $name . "%", PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            $users[] = new User(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['password'],
                $row['active'],
                $row['isAdmin'],
                $row['created_at']
            );
        }
        return $users;
    }
    /**
     * Busca un usuari per username i email (per a canvi de contrasenya)
     * @param string $username Username de l'usuari a cercar
     * @param string  $email Email de l'usuari a cercar
     * @return User|null Instància de User o null si no es troba
     */
    public function findByUsernameAndEmail($username, $email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username AND email = :email LIMIT 1');
        $stmt->execute([':username' => $username, ':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['password'],
                $row['active'],
                $row['isAdmin'],
                $row['created_at']
            );
        }
        return null;
    }

    /**
     * Funcion para obtener todos los usuarios
     * @return User[] Array de instancias de User
     */

    public function findAll()
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE active = 1');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            $users[] = new User(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['password'],
                $row['active'],
                $row['isAdmin'],
                $row['created_at']
            );
        }
        return $users;
    }

    // Comprueba si existe un usuario por email
    public function existsByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT user_id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() !== false;
    }

    // Comprueba si existe un usuario por username
    public function existsByUsername($username)
    {
        $stmt = $this->db->prepare('SELECT user_id FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        return $stmt->fetch() !== false;
    }

    /**
     * Obtenir usuari per email
     * @param string $email Email de l'usuari
     * @return array|null Dades de l'usuari o null si no es troba
     */
    public function getByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualitza la contrasenya d'un usuari per email
     * @param string $email Email de l'usuari
     * @param string $hashedPassword Nova contrasenya hashada
     * @param string $trnDate Data de la transacció
     * @return void 
     */
    public function updatePasswordByEmail($email, $hashedPassword, $trnDate)
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :password, trn_date = :trn_date WHERE email = :email');
        $stmt->execute([
            ':password' => $hashedPassword,
            ':trn_date' => $trnDate,
            ':email' => $email
        ]);
    }

    /**
     * Funcio per comptar el nombre total d'usuaris a la base de dades
     * @return int Número total de usuarios en la base de datos
     */
    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int) $row['total'] : 0;
    }

    /**
     * Obtiene usuarios paginados
     * @param int $limit Número de registros por página
     * @param int $offset Desplazamiento de registros
     * @return User[] Array de instancias de User
     */
    public function getUsersPaginados($limit, $offset)
    {
        $sql = "SELECT * FROM users LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $usuarios = [];
        foreach ($rows as $row) {
            $usuarios[] = new User(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['password'],
                $row['active']
            );
        }
        return $usuarios;
    }

    /**
     * Actualitza la contrasenya d'un usuari per ID
     * @param int $userId ID de l'usuari
     * @param string $hashedPassword Nova contrasenya hashada
     * @return int Número de files afectades
     */
    public function updatePassword($userId, $hashedPassword)
    {
        $sql = 'UPDATE users SET password = :password WHERE user_id = :user_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Busca un usuari per email
     * @param string $email Email de l'usuari a cercar
     * @return User|null Instància de User o null si no es troba
     */
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new User($row['user_id'], $row['username'], $row['email'], null);
        } else {
            return null;
        }
    }

    /**
     * Crea un nou usuari a la base de dades a partir de dades d'OAuth
     * @param string $username Nom de l'usuari
     * @param string $email Email de l'usuari
     * @return int ID del nou usuari creat
     */
    public function createFromOAuth($username, $email)
    {
        $stmt = $this->db->prepare("INSERT INTO users (username, email) VALUES (:username, :email)");
        $stmt->execute(['username' => $username, 'email' => $email]);
        return $this->db->lastInsertId();
    }
}
?>