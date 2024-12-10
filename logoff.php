<?php
require 'classes/apps/UsuarioDb.php';
session_start();
$usuarioDb = new UsuarioDb();
if (isset($_SESSION['usuario'])) {
    $usuarioDb->editarUsuarioToken($_SESSION['usuario'], NULL);
}
//cerrar session
session_destroy();
session_unset();
session_abort();
//Eliminar la cookie de la session
setcookie(session_name(), '', time() - 3600, '/');
setcookie('recordar', '', time() - (3600 * 24 * 7));
header('Location: login.php');
exit;
