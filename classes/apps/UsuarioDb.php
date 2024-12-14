<?php
require 'classes/bds/Conex.php';
class UsuarioDb extends Conex
{

    private $idUsuario;
    private $email;
    private $contrasena;
    private $token;
    private $nombre;
    private $foto;
    private $descripcion;

    public function __construct()
    {
        parent::__construct();
    }
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getContrasena()
    {
        return $this->contrasena;
    }
    public function getToken()
    {
        return $this->token;
    }

    public function anadirUsuario($email, $contrasena)
    {
        $query = 'INSERT INTO usuario(email,contraseña) VALUES (:email,:contrasena)';
        try {
            $insertar = $this->conexBd->prepare($query);
            $insertar->execute([':email' => $email, ':contrasena' => $contrasena]);
        } catch (PDOException $e) {
            echo 'Error al insertar usuario en la base de datos: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function eliminarUsuario($email)
    {
        $query = 'DELETE FROM usuario WHERE email=:$email';
        try {
            $eliminar = $this->conexBd->prepare($query);
            $eliminar->execute([':email' => $email]);
        } catch (PDOException $e) {
            echo 'Error al eliminar usuario en la base de datos: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function editarUsuarioEmail($email, $nuevoEmail)
    {
        $query = 'UPDATE usuario SET email=:nuevoEmail WHERE email=:$email';
        try {
            $actualizar = $this->conexBd->prepare($query);
            $actualizar->execute([':email' => $email, ':nuevoEmail' => $nuevoEmail]);
        } catch (PDOException $e) {
            echo 'Error al actualizar el email del usuario: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function editarUsuarioContraseña($email, $contrasena)
    {
        $query = 'UPDATE usuario SET contraseña=:contrasena WHERE email=:email';
        try {
            $actualizar = $this->conexBd->prepare($query);
            $actualizar->execute([':email' => $email, ':contrasena' => $contrasena]);
        } catch (PDOException $e) {
            echo 'Error al actualizar el contraseña del usuario: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function editarUsuarioToken($email, $token)
    {
        $query = 'UPDATE usuario SET token=:token WHERE email=:email';
        try {
            $actualizar = $this->conexBd->prepare($query);
            $actualizar->execute([':email' => $email, ':token' => $token]);
        } catch (PDOException $e) {
            echo 'Error al actualizar el token del usuario: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function seleccionarUsuario($email)
    {
        $query = 'SELECT * FROM usuario WHERE email=:email';
        try {
            $selecionar = $this->conexBd->prepare($query);
            $selecionar->execute([':email' => $email]);
            $selecionar->setFetchMode(PDO::FETCH_CLASS, 'UsuarioDb');
            $usuario = $selecionar->fetch();
            return $usuario;
        } catch (PDOException $e) {
            echo 'Error al seleccionar el grupo: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function seleccionarUsuarioToken($token)
    {
        $query = 'SELECT * FROM usuario WHERE token=:token';
        try {
            $selecionar = $this->conexBd->prepare($query);
            $selecionar->execute([':token' => $token]);
            $selecionar->setFetchMode(PDO::FETCH_CLASS, 'UsuarioDb');
            $usuario = $selecionar->fetch();
            return $usuario;
        } catch (PDOException $e) {
            echo 'Error al seleccionar el grupo: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
    public function seleccionarUsuarioId($idUsuario)
    {
        $query = 'SELECT * FROM usuario WHERE idUsuario=:idUsuario';
        try {
            $selecionar = $this->conexBd->prepare($query);
            $selecionar->execute([':idUsuario' => $idUsuario]);
            $selecionar->setFetchMode(PDO::FETCH_CLASS, 'UsuarioDb');
            $usuario = $selecionar->fetch();
            return $usuario;
        } catch (PDOException $e) {
            echo 'Error al seleccionar el usuario: ' . $e->getMessage() . ', en la linea: ' . $e->getLine() . ', en el archivo: ' . $e->getFile();
        }
    }
}
