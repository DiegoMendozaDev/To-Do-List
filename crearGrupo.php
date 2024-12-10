<?php
require 'functions/securizar.php';
require 'functions/verSession.php';
require 'classes/apps/GrupoDb.php';
require 'classes/apps/UsuariosGruposDb.php';
session_start();
//Declaracion de variables
$session = $token = '';
$errores = [];
$usuarioDb = new UsuarioDb();
$gruposDb = new GrupoBd();
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
    if (isset($_POST['crear'])) {
        if (strlen($nombre) > 50 || strlen($nombre) < 3) {
            $errores[] = 'El nombre del grupo tiene que tener entre 3 y 50 caracteres';
        } elseif (!$nombre || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ' -]*$/u", $nombre)) {
            $errores[] = 'Tienes que introducir un nombre valido (solo letras y espacios)';
        }elseif(!$grupo){
            $errores[] ='Ya hay un grupo con ese nombre';
        }
        if (empty($errores)) {
            $gruposDb->anadirGrupo($nombre);
            $grupo = $gruposDb->seleccionarGrupo($nombre);
            $usuariosGruposDb->anadirUsuariosGrupos($usuario->getIdUsuario(), $grupo->getIdGrupo(), true);
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

                <head>Crear grupo</head>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <label><input type="text" name="nombre" placeholder="Group name"></label>
                    <input type="submit" value="Crear" name='crear'>
                </form>
            </fieldset>
        </article>
    </main>
    <footer></footer>
</body>

</html>