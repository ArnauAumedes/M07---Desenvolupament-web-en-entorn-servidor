<?php
interface DAO
{
    public function create($entity);
    public function update($entity);
    public function delete($id);

    public function findAll();
    public function findById($id);
}
?>