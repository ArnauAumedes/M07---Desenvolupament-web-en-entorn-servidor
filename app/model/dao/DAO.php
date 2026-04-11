<?php
/**
 * DAO.php
 * Interfaz genérica para los Data Access Objects (DAO) de la aplicación
 * Autor: Arnau Aumedes Jimenez
 */
interface DAO
{
    public function create($entity);
    public function update($entity);
    public function delete($id);

    public function findAll();
    public function findById($id);
}
?>