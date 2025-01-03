<?php
require 'functions/securizar.php';
require 'functions/verSession.php';
require 'classes/apps/UsuarioDb.php';
require 'classes/apps/TareaDb.php';
require 'classes/apps/GrupoDb.php';
session_start();
//Declaracion de variables
$session = $token = '';
$errores = [];
$usuarioDb = new UsuarioDb();
$tareaDb = new TareaDb();
$grupoDb = new GrupoDb();
//Comprobamos si hay unsa sesión o un token
if (!isset($_SESSION['grupo']) || !$_SESSION['grupo']) {
    header('Location: index.php');
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
//Procesamos la solicitudes en metodo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Variables de los inputs
    $nombre = securizar($_POST['nombre'] ?? '');
    $descripcion = securizar($_POST['descripcion'] ?? '');
    $fechaFin = securizar($_POST['fechaFin'] ?? '');
    $idTarea = securizar($_POST['idTarea'] ?? '');
    $usuarioNew = securizar($_POST['usuarioNew'] ?? '');
    //Procesar la solicitud de añadir tarea
    if (isset($_POST['anadir'])) {
        //Comprobaciones
        if (!$nombre || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ' -]*$/u", $nombre)) {
            $errores[] = 'Tienes que introducir un nombre valido (solo letras y espacios)';
        } elseif (strlen($nombre) < 1 || strlen($nombre) > 50) {
            $errores[] = 'El nombre solo puede contener entre 1 y 50 caratéres';
        } elseif (!$fechaFin || date('Y-m-d') > $fechaFin) {
            $errores[] = 'Tienes que introducir una fecha válida';
        }
        //Si no hay ningun error añadimos la tarea
        if (empty($errores)) {
            $tareaDb->anadirTarea($nombre, $descripcion, $fechaFin, $grupo->getIdGrupo(), NULL);
        }
    } elseif (isset($_POST['eliminar'])) {
        $tareaDb->eliminarTarea($idTarea);
    } elseif (isset($_POST['editar'])) {
        $tarea = $tareaDb->seleccionarTarea($idTarea);
        $nombreAc = $tarea->getNombre();
        $descripcionAc = $tarea->getDescripcion();
        $fechaFinAc = $tarea->getFechaFin();
        if ($tarea->getIdUsuario()) {
            $idUsuario = $tarea->getIdUsuario();
            $usuarioNew = $usuarioDb->seleccionarUsuarioId($idUsuario);
            $usuarioAc = $usuario->getEmail();
        }
    } elseif (isset($_POST['actualizar'])) {
        $tareaDb->editarTareaGrupo($idTarea, $nombre, $descripcion, $fechaFin, $usuarioNew);
    }
}
$todasTareas = $tareaDb->seleccionarTareasGrupo($grupo->getIdGrupo());
$todasTareasUsuario = $tareaDb->seleccionarTareasGrupoUsuarios($grupo->getIdGrupo(), $usuario->getIdUsuario());
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/to-do.css" type='text/css'>
    <title>Grupo</title>
</head>

<body>
    <header>
        <p>User: <?= $session ?></p>
        <button type="submit" name="anadirUsuario" class="button">Add User</button>
        <a href="index.php" id="style-4">back</a>
        <a href="logoff.php" id="style-4">logoff</a>
    </header>
    <hr>
    <nav></nav>
    <main>
        <article>
            <header>
                <h2>Add task</h2>
            </header>
            <fieldset>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="nombre" placeholder="Name" value="<?= $nombreAc ?? NULL ?>" required>
                    <textarea name="descripcion" placeholder="Description"><?= $descripcionAc ?? NULL ?></textarea>
                    <input type="text" name="usuarioNew" placeholder="Email(optional)" value="<?= $usuarioAc ?? NULL ?>">
                    <input type="date" name="fechaFin" min="<?= date('Y-m-d') ?>" value="<?= $fechaFinAc ?? date('Y-m-d') ?>" required>
                    <?php if (!isset($_POST['editar'])): ?>
                        <input type="submit" value="Add" name="anadir">
                    <?php else: ?>
                        <input type="submit" value="Update" name='actualizar'>
                        <input type="hidden" name="idTarea" value='<?= $idTarea ?>'>
                </form>
                <form action="" method="post">
                    <input type="submit" value="Back" name='volver'>
                </form>
            <?php endif ?>
            </form>
            <?php if (!empty($errores)): ?>
                <?php foreach ($errores as $error): ?>
                    <p class='errores'>*<?= $error ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
            </fieldset>
        </article>
        <hr>
        <article>
            <header>
                <h2>To-do List</h2>
            </header>
            <?php while ($tarea = $todasTareas->fetch()): ?>
                <input type="checkbox" name="completada">
                <?= $tarea->getNombre() ?> - <?= $tarea->getDescripcion() ?> - <?= $tarea->getFechaFin() ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <button type="submit" name="eliminar">Eliminar</button>
                    <button type="submit" name="editar">editar</button>
                    <input type="hidden" name="idTarea" value='<?= $tarea->getIdTarea() ?>'>
                </form>
            <?php endwhile; ?>
        </article>
        <?php if ($todasTareasUsuario): ?>
            <article>
                <header>
                    <h2>To-do List User</h2>
                </header>
                <?php while ($tarea = $todasTareasUsuario->fetch()): ?>
                    <input type="checkbox" name="completada">
                    <?= $tarea->getNombre() ?> - <?= $tarea->getDescripcion() ?> - <?= $tarea->getFechaFin() ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <button type="submit" name="eliminar">Eliminar</button>
                        <button type="submit" name="editar">editar</button>
                        <input type="hidden" name="idTarea" value='<?= $tarea->getIdTarea() ?>'>
                    </form>
                <?php endwhile; ?>
            </article>
        <?php endif ?>
    </main>
    <footer></footer>
</body>

</html>