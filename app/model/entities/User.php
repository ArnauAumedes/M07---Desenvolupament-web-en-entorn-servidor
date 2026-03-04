<?php
class User
{
    public ?int $id;
    private string $username;
    private string $email;
    private ?string $password;
    private int $active;
    private int $isAdmin;
    private ?string $created_at;
    private $updated_at;

    public function __construct(?int $id, string $username, string $email, ?string $password, int $active = 1, int $isAdmin = 0, $created_at = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->active = $active;
        $this->isAdmin = $isAdmin;
        $this->created_at = $created_at ?? date('Y-m-d H:i:s');
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function isActive()
    {
        return $this->active;
    }

    public function isAdmin()
    {
        return $this->isAdmin;
    }

    // Setters
    public function setUserName(string $username): void
    {
        $this->username = $username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setActive(int $active): void
    {
        $this->active = $active;
    }
    public function setIsAdmin(int $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }
}

?>