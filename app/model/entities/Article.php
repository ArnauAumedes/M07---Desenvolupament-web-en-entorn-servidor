<?php

/**
 * Classe entitat Article
 * 
 * Representa un article amb les seves propietats i mètodes d'accés (getters/setters).
 * Aquesta classe segueix el patró de disseny Entity/Model en l'arquitectura MVC.
 * 
 * @author Arnau Aumedes
 * @version 1.0
 */
class Article
{
    /**
     * @var int|null ID únic de l'article (clau primària AUTO_INCREMENT)
     */
    public $id;
    
    /**
     * @var string DNI de l'autor de l'article
     */
    private $dni;
    
    /**
     * @var string Títol de l'article
     */
    private $titol;
    
    /**
     * @var string Cos o contingut complet de l'article
     */
    private $cos;

    /**
     * Constructor de la classe Article
     * 
     * Inicialitza un nou article amb els valors proporcionats.
     * L'ID es deixa null ja que s'assigna automàticament per la BD.
     * 
     * @param string $dni DNI de l'autor de l'article
     * @param string $titol Títol de l'article
     * @param string $cos Contingut complet de l'article
     */
    public function __construct($dni, $titol, $cos)
    {
        $this->dni = $dni;
        $this->titol = $titol;
        $this->cos = $cos;
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
     * Obté el DNI de l'autor
     * 
     * @return string DNI de l'autor de l'article
     */
    public function getDni()
    {
        return $this->dni;
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
     * Estableix el DNI de l'autor
     * 
     * @param string $dni DNI de l'autor de l'article
     * @return void
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
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
}
?>