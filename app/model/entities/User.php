<?php
class User
{
    public $id;
    private $username;
    private $email;
    private $password_hash;
    private $active;
    private $created_at;
    private $updated_at;
    private $last_login;

    public function __construct($username, $email, $password_hash, $active = 1)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->active = $active;
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
    public function getPasswordHash()
    {
        return $this->password_hash;
    }
    public function isActive()
    {
        return $this->active;
    }

    // Setters
    public function setId($timestamp)
    {
        $this->last_login = $timestamp;
    }

    public function setName($username)
    {
        $this->username = $username;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }
}
?>