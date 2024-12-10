<?php
/**
 * Función para ver si hay una sesión
 */
function verSession($usuario, $token, $usuarioDb)
{
    if ($token) {
        $existe = $usuarioDb->seleccionarUsuarioToken($token);
        if ($existe) {
            $_SESSION['usuario'] = $existe->getEmail();
            $usuario = $existe->getEmail();
        }
    }
    if (!$usuario) {
        header('Location: login.php');
        exit;
    }
}
