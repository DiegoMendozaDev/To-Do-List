<?php
require 'functions/securizar.php';
require 'functions/verSession.php';
require 'classes/apps/UsuarioDb.php';
require 'classes/apps/TareaDb.php';
require 'classes/apps/GrupoDb.php';
require 'classes/apps/UsuariosGruposDb.php';
session_start();
//Declaracion de variables
$session = $token = '';
$errores = [];
$usuarioDb = new UsuarioDb();
$tareaDb = new TareaDb();
$grupoDb = new GrupoDb();
$usuariosGruposDb = new UsuariosGruposDb();
//Comprobamos si hay unsa sesión o un token
if (!isset($_SESSION['grupo']) || !$_SESSION['grupo']) {
    header('Location: index.php');
    exit;
}
if (isset($_SESSION['usuario'])) {
    $session = $_SESSION['usuario'];
}
if (isset($_COOKIE['recordar'])) {
    $token = $_COOKIE['recordar'];
}
verSession($session, $token, $usuarioDb);
$session = $_SESSION['usuario'];
$usuario = $usuarioDb->seleccionarUsuario($session);
$grupo = $grupoDb->seleccionarGrupo($_SESSION['grupo']);
$usuarioGrupo = $usuariosGruposDb->seleccionarUsuarioGrupo($grupo->getIdGrupo(), $usuario->getIdUsuario());
if($usuarioGrupo->getAdmin()){
    $grupoDb->eliminarGrupo($grupo->getIdGrupo());
    $tareaDb->eliminarTareaGrupo($grupo->getIdGrupo());
    $usuariosGruposDb->eliminarUsuarios($grupo->getIdGrupo());
}else{
    $tareaDb->eliminarTareaGrupoUsuario($grupo->getIdGrupo(),$usuario->getIdUsuario());
    $usuariosGruposDb->eliminarUsuariosGrupos($usuario->getIdUsuario(),$grupo->getIdGrupo());
}
header('Location: index.php');
exit;