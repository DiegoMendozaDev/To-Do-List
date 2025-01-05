<?php
class UsuariosGruposDb extends Conex
{

    private $idUsuario;
    private $idGrupo;
    private $admin;

    public function __construct()
    {
        parent::__construct();
    }
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
    public function getIdGrupo()
    {
        return $this->idGrupo;
    }
    public function getAdmin()
    {
        return $this->admin;
    }
    public function anadirUsuariosGrupos($idUsuario, $idGrupo, $admin)
    {
        $query = 'INSERT INTO usuarios_grupos(idUsuario,idGrupo,admin) VALUES (:idUsuario,:idGrupo,:admin)';
        try {
            $insertar = $this->conexBd->prepare($query);
            $insertar->execute([':idUsuario' => $idUsuario, ':idGrupo' => $idGrupo, ':admin' => $admin]);
        } catch (PDOException $e) {
            echo 'Error al insertar usuarios_grupos en la base de datos: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function eliminarUsuariosGrupos($idUsuario, $idGrupo)
    {
        $query = 'DELETE FROM usuarios_grupos WHERE idUsuario=:idUsuario AND idGrupo=:idGrupo';
        try {
            $eliminar = $this->conexBd->prepare($query);
            $eliminar->execute([':idUsuario' => $idUsuario, ':idGrupo' => $idGrupo]);
        } catch (PDOException $e) {
            echo 'Error al eliminar usuarios_grupos en la base de datos: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function editarUsuariosGrupos($idUsuario, $idGrupo, $admin)
    {
        $query = 'UPDATE usuarios_grupos SET admin=:admin WHERE idUsuario=:idUsuario AND idGrupo=:idGrupo';
        try {
            $actualizar = $this->conexBd->prepare($query);
            $actualizar->execute([':idUsuario' => $idUsuario, ':idGrupo' => $idGrupo, ':admin' => $admin]);
        } catch (PDOException $e) {
            echo 'Error al actualizar el admin del grupo: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function seleccionarUsuariosGrupos($idGrupo)
    {
        $query = 'SELECT * FROM usuarios_grupos WHERE idGrupo=:idGrupo';
        try {
            $selecionar = $this->conexBd->prepare($query);
            $selecionar->execute([':idGrupo' => $idGrupo]);
            $selecionar->setFetchMode(PDO::FETCH_CLASS, 'UsuariosGruposDb');
            return $selecionar;
        } catch (PDOException $e) {
            echo 'Error al seleccionar el todos los usuarios del grupo: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function seleccionarGruposUsuarios($idUsuario)
    {
        $query = 'SELECT * FROM usuarios_grupos, grupo WHERE grupo.idGrupo=usuarios_grupos.idGrupo AND usuarios_grupos.idUsuario = :idUsuario';
        try {
            $selecionar = $this->conexBd->prepare($query);
            $selecionar->execute([':idUsuario' => $idUsuario]);
            $selecionar->setFetchMode(PDO::FETCH_CLASS, 'UsuariosGruposDb');
            return $selecionar;
        } catch (PDOException $e) {
            echo 'Error al seleccionar el todos los grupos del usuario: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function seleccionarUsuarioGrupo($idGrupo,$idUsuario)
    {
        $query = 'SELECT * FROM usuarios_grupos WHERE idGrupo=:idGrupo AND idUsuario=:idUsuario';
        try {
            $selecionar = $this->conexBd->prepare($query);
            $selecionar->execute([':idGrupo' => $idGrupo,':idUsuario' => $idUsuario]);
            $selecionar->setFetchMode(PDO::FETCH_CLASS, 'UsuariosGruposDb');
            $usuario = $selecionar->fetch();
            return $usuario;
        } catch (PDOException $e) {
            echo 'Error al seleccionar el todos los usuarios del grupo: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
}
