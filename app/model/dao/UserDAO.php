<?php
/**
 * UserDAO
 *
 * Gestiona l'accés a la taula `users` per registre i autenticació.
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../entities/User.php';

class UserDAO extends User
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
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

    // Inserta un nuevo usuario
    public function createUser($username, $email, $hashedPassword)
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password, active) VALUES (:username, :email, :password, 1)');
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
        return $this->db->lastInsertId();
    }

    // Obtiene usuario por email
    public function getByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene usuario por username
    public function getByUsername($username)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza la contraseña por email
    public function updatePasswordByEmail($email, $hashedPassword, $trnDate)
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :password, trn_date = :trn_date WHERE email = :email');
        $stmt->execute([
            ':password' => $hashedPassword,
            ':trn_date' => $trnDate,
            ':email' => $email
        ]);
    }

    // Obtiene usuario por id
    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
            );
        }
        return $usuarios;
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
     * Funcion para obtener todos los usuarios
     * @return User[] Array de instancias de User
     */
    public function findAll()
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $usuarios = [];
        foreach ($rows as $row) {
            $usuarios[] = new User(
                $row['user_id'],
                $row['username'],
                $row['email'],
                $row['password'],
            );
        }
        return $usuarios;
    }
}
?>