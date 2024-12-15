<?php
require 'functions/securizar.php';
require 'functions/verSession.php';
require 'classes/apps/UsuarioDb.php';
require 'classes/apps/TareaDb.php';
require 'classes/apps/UsuariosGruposDb.php';
session_start();
//Declaracion de variables
$session = $token = '';
$errores = [];
$usuarioDb = new UsuarioDb();
$tareaDb = new TareaDb();
$usuariosGruposDb = new UsuariosGruposDb();
//Comprobamos si hay unsa sesión o un token
if (isset($_SESSION['usuario'])) {
    $session = $_SESSION['usuario'];
}
if (isset($_COOKIE['recordar'])) {
    $token = $_COOKIE['recordar'];
}
if (isset($_SESSION['grupo'])) {
    unset($_SESSION['grupo']);
}
verSession($session, $token, $usuarioDb);
$session = $_SESSION['usuario'];
$usuario = $usuarioDb->seleccionarUsuario($session);
//Procesamos la solicitudes en metodo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Variables de los inputs
    $nombre = securizar($_POST['nombre'] ?? '');
    $descripcion = securizar($_POST['descripcion'] ?? '');
    $fechaFin = securizar($_POST['fechaFin'] ?? '');
    $idTarea = securizar($_POST['idTarea'] ?? '');
    $grupoNombre = securizar($_POST['grupoNombre'] ?? '');
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
            $tareaDb->anadirTarea($nombre, $descripcion, $fechaFin, NULL, $usuario->getIdUsuario());
        }
    } elseif (isset($_POST['eliminar'])) {
        $tareaDb->eliminarTarea($idTarea);
    } elseif (isset($_POST['editar'])) {
        $tarea = $tareaDb->seleccionarTarea($idTarea);
        $nombreAc = $tarea->getNombre();
        $descripcionAc = $tarea->getDescripcion();
        $fechaFinAc = $tarea->getFechaFin();
    } elseif (isset($_POST['actualizar'])) {
        $tareaDb->editarTarea($idTarea, $nombre, $descripcion, $fechaFin);
    } elseif (isset($_POST['irGrupo'])) {
        $_SESSION['grupo'] = $grupoNombre;
        header('Location: grupo.php');
    }
}
$todosGrupos = $usuariosGruposDb->seleccionarGruposUsuarios($usuario->getIdUsuario());
$todasTareas = $tareaDb->seleccionarTareasUsuarios($usuario->getIdUsuario());

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/to-do.css" type='text/css'>
    <title>To-do</title>
</head>

<body>
    <header>
        <p>Hi, <?= $usuario->getNombre() ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <select name="grupoNombre">
                <?php while ($grupo = $todosGrupos->fetch()): ?>
                    <option value="<?= $grupo->getIdGrupo() ?>"><?php echo $grupo->nombre ?></option>
                <?php endwhile ?>
            </select>
            <input type="submit" name="irGrupo" value='Enter'>
        </form>
        <a href="crearGrupo.php">Create Group</a>
        <a href="logoff.php">logoff</a>
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
                    <input type="date" name="fechaFin" min="<?= date('Y-m-d') ?>" value="<?= $fechaFinAc ?? date('Y-m-d') ?>" required>
                    <?php if (!isset($_POST['editar'])): ?>
                        <input type="submit" value='Add' name="anadir">
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
    </main>
    <footer></footer>
</body>

</html>