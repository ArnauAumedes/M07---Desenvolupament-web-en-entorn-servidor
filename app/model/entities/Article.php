<?php

/**
 * Classe entitat Article
 * 
 * Representa un article amb les seves propietats i mètodes d'accés (getters/setters).
 * Aquesta classe segueix el patró de disseny Entity/Model en l'arquitectura MVC.
 * 
 * @author Arnau Aumedes Jimenez
 * @version 1.0
 */
class Article
{
    /**
     * @var int|null ID únic de l'article (clau primària AUTO_INCREMENT)
     */
    public $id;
    
    /**
     * @var int|null ID de l'usuari creador de l'article
     */
    private $user_id;
    /**
     * @var string|null Nom d'usuari (autor) per mostrar al frontend
     */
    private $author_name;
    
    /**
     * @var string Títol de l'article
     */
    private $titol;
    
    /**
     * @var string Cos o contingut complet de l'article
     */
    private $cos;
    /**
     * @var string|null Data de creació de l'article (format timestamp)
     */
    private $data_creacio;

    /**
     * Constructor de la classe Article
     * 
     * Inicialitza un nou article amb els valors proporcionats.
     * L'ID es deixa null ja que s'assigna automàticament per la BD.
     * 
     * @param int|null $user_id ID de l'usuari creador
     * @param string $titol Títol de l'article
     * @param string $cos Contingut complet de l'article
     */
    public function __construct($user_id, $titol, $cos)
    {
        $this->user_id = $user_id;
        $this->titol = $titol;
        $this->cos = $cos;
        $this->author_name = null;
        $this->data_creacio = null;
    }

    // ==================== GETTERS ====================
    
    /**
     * Obté l'ID de l'article
     * 
     * @return int|null ID de l'article o null si encara no s'ha assignat
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obté l'ID de l'usuari creador
     * 
     * @return int|null ID de l'usuari o null
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Obté el nom d'usuari autor (si està disponible)
     *
     * @return string|null Nom d'usuari o null
     */
    public function getAuthorName()
    {
        return $this->author_name;
    }

    /**
     * Obté el títol de l'article
     * 
     * @return string Títol de l'article
     */
    public function getTitol()
    {
        return $this->titol;
    }

    /**
     * Obté el cos de l'article
     * 
     * @return string Contingut complet de l'article
     */
    public function getCos()
    {
        return $this->cos;
    }

    // ==================== SETTERS ====================
    
    /**
     * Estableix l'ID de l'article
     * 
     * @param int $id ID únic de l'article
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Estableix l'ID de l'usuari autor
     * 
     * @param int|null $user_id ID de l'usuari
     * @return void
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Estableix el nom d'usuari autor (opcional)
     * 
     * @param string $name Nom d'usuari
     * @return void
     */
    public function setAuthorName($name)
    {
        $this->author_name = $name;
    }

    /**
     * Estableix el títol de l'article
     * 
     * @param string $titol Nou títol de l'article
     * @return void
     */
    public function setTitol($titol)
    {
        $this->titol = $titol;
    }

    /**
     * Estableix el cos de l'article
     * 
     * @param string $cos Nou contingut de l'article
     * @return void
     */
    public function setCos($cos)
    {
        $this->cos = $cos;
    }

    /**
     * Estableix la data de creació de l'article
     *
     * @param string $data_creacio Timestamp o data en format readable
     * @return void
     */
    public function setDataCreacio($data_creacio)
    {
        $this->data_creacio = $data_creacio;
    }

    /**
     * Obté la data de creació de l'article
     *
     * @return string|null
     */
    public function getDataCreacio()
    {
        return $this->data_creacio;
    }
}
?>