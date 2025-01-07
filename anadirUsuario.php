<?php
require 'functions/securizar.php';
require 'classes/apps/UsuarioDb.php';
require 'functions/verSession.php';
require 'classes/apps/GrupoDb.php';
require 'classes/apps/UsuariosGruposDb.php';
//Variables
$session = $token = '';
$errores = [];
$usuarioDb = new UsuarioDb();
$grupoDb = new GrupoDb();
$usuariosGruposDb = new UsuariosGruposDb();
//Inicio de session
session_start();
//Comprobamos si hay unsa sesión o un token
if (!isset($_SESSION['grupo']) || !$_SESSION['grupo']) {
    header('Location: index.php');
    exit;
}
if (isset($_COOKIE['recordar'])) {
    $token = $_COOKIE['recordar'];
}
if (isset($_SESSION['usuario'])) {
    $session = $_SESSION['usuario'];
}
verSession($session, $token, $usuarioDb);
$session = $_SESSION['usuario'];
$usuario = $usuarioDb->seleccionarUsuario($session);
$grupo = $grupoDb->seleccionarGrupo($_SESSION['grupo']);

//Procesamos las solicitudes en metodo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Variables de los inputs
    $email = securizar($_POST['email'] ?? '');

    //Procesar la solicitud de registrar a alguin
    if (isset($_POST['registrar'])) {
        //Vemos si hay algun usuario con este email
        $usuario = $usuarioDb->seleccionarUsuario($email);
        //Validaciones de los campos
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Tienes que introducir un email válido';
        } elseif (!$usuario) {
            $errores[] = 'Usuario no válido';
        } elseif ($usuariosGruposDb->seleccionarUsuarioGrupo($grupo->getIdGrupo(), $usuario->getIdUsuario())) {
            $errores[] = 'Este usuario ya está en el grupo';
        }
        //Si no hay errores se registra
        if (empty($errores)) {
            $usuario = $usuarioDb->seleccionarUsuario($email);
            $usuariosGruposDb->anadirUsuariosGrupos($usuario->getIdUsuario(), $grupo->getIdGrupo(), false);
            header('Location: grupo.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css" type='text/css'>
    <title>Add user</title>
</head>

<body>

    <head></head>
    <nav></nav>
    <main>
        <section class="login">
            <header>
                <h2>Add user</h2>
            </header>
            <fieldset>
                <legend>Register</legend>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="wave-group">
                        <input type="email" name="email" class="input" required>
                        <span class="bar"></span>
                        <label class="label">
                            <span class="label-char" style="--index: 0">E</span>
                            <span class="label-char" style="--index: 1">m</span>
                            <span class="label-char" style="--index: 2">a</span>
                            <span class="label-char" style="--index: 3">i</span>
                            <span class="label-char" style="--index: 4">l</span>
                        </label>
                    </div>
                    <br>
                    <input type="submit" value="Add" name="registrar" button class="button-59" role="button">
                    <a href="grupo.php" id="style-4">Back</a>
                </form>
                <br>
                <?php if (!empty($errores)): ?>
                    <?php foreach ($errores as $error): ?>
                        <div class="notifications-container">
                            <div class="error-alert">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" class="error-svg">
                                            <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" fill-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="error-prompt-container">
                                        <p class="error-prompt-heading"><?= $error ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </fieldset>
        </section>
    </main>
    <footer></footer>
</body>

</html>