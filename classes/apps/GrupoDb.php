<?php

class GrupoBd extends Conex
{

    private $idGrupo;
    private $nombre;


    public function __construct()
    {
        parent::__construct();
    }

    public function getIdGrupo()
    {
        return $this->idGrupo;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function anadirGrupo($nombre)
    {
        $query = 'INSERT INTO grupo(nombre) VALUES (:nombre)';
        try {
            $insertar = $this->conexBd->prepare($query);
            $insertar->execute([':nombre' => $nombre]);
        } catch (PDOException $e) {
            echo 'Error al insertar grupo en la base de datos: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function eliminarGrupo($idGrupo)
    {
        $query = 'DELETE FROM grupo WHERE idGrupo=:idGrupo';
        try {
            $eliminar = $this->conexBd->prepare($query);
            $eliminar->execute([':idGrupo' => $idGrupo]);
        } catch (PDOException $e) {
            echo 'Error al eliminar grupo en la base de datos: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function editarGrupo($idGrupo, $nombre)
    {
        $query = 'UPDATE grupo SET nombre=:nombre WHERE idGrupo=:idGrupo';
        try {
            $actualizar = $this->conexBd->prepare($query);
            $actualizar->execute([':idGrupo' => $idGrupo, ':nombre' => $nombre]);
        } catch (PDOException $e) {
            echo 'Error al actualizar el nombre del grupo: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function seleccionarGrupo($nombre)
    {
        $query = 'SELECT * FROM grupo WHERE nombre=:nombre';
        try {
            $selecionar = $this->conexBd->prepare($query);
            $selecionar->execute([':nombre' => $nombre]);
            $selecionar->setFetchMode(PDO::FETCH_CLASS, 'GrupoDb');
            $grupo = $selecionar->fetch();
            return $grupo;
        } catch (PDOException $e) {
            echo 'Error al seleccionar el grupo: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
}
