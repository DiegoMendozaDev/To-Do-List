<?php
require 'functions/securizar.php';
require 'functions/verSession.php';
require 'classes/apps/UsuarioDb.php';
require 'classes/apps/GrupoDb.php';
require 'classes/apps/UsuariosGruposDb.php';
session_start();
//Declaracion de variables
$session = $token = '';
$errores = [];
$usuarioDb = new UsuarioDb();
$gruposDb = new GrupoDb();
$usuariosGruposDb = new UsuariosGruposDb();
//Comprobamos si hay unsa sesión o un token
if (isset($_SESSION['usuario'])) {
    $session = $_SESSION['usuario'];
}
if (isset($_COOKIE['recordar'])) {
    $token = $_COOKIE['recordar'];
}
verSession($session, $token, $usuarioDb);
$session = $_SESSION['usuario'];
$usuario = $usuarioDb->seleccionarUsuario($session);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Variables de los inpurs
    $nombre = securizar($_POST['nombre'] ?? '');
    $grupo = $gruposDb->seleccionarGrupo($nombre);
    if (isset($_POST['add'])) {
        if (strlen($nombre) > 50 || strlen($nombre) < 3) {
            $errores[] = 'El nombre del grupo tiene que tener entre 3 y 50 caracteres';
        } elseif (!$nombre || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ' -]*$/u", $nombre)) {
            $errores[] = 'Tienes que introducir un nombre valido (solo letras y espacios)';
        } elseif ($grupo) {
            $errores[] = 'Ya hay un grupo con ese nombre';
        }
        if (empty($errores)) {

            $gruposDb->anadirGrupo($nombre);
            $grupo = $gruposDb->seleccionarGrupoNombre($nombre);
            $usuariosGruposDb->anadirUsuariosGrupos($usuario->getIdUsuario(), $grupo->getIdGrupo(), true);
            header('Location: index.php');
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
    <title>Crear Grupo</title>
</head>

<body>

    <head></head>
    <nav></nav>
    <main>
        <article>
            <fieldset>

                <head>
                    <h2>Add Group</h2>
                </head>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <label><input type="text" name="nombre" placeholder="Group name" required></label>
                    <input type="submit" name='add' value='create'>
                </form>
                <a href="index.php">Back</a>
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
        </article>
    </main>
    <footer></footer>
</body>

</html>