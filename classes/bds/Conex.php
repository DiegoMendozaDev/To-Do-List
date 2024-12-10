<?php
require './conf/config.php';
class Conex
{
    protected $conexBd;
    public function __construct()
    {
        try {
            $this->conexBd = new PDO(DNS_DB, USER_DB, PASS_DB);
            $this->conexBd->setAttribute(PDO::ERRMODE_EXCEPTION, PDO::ATTR_ERRMODE);
        } catch (PDOException $e) {
            echo 'Error al conextarse en la base de datos: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
}
