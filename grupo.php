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
//Procesamos la solicitudes en metodo post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Variables de los inputs
    $nombre = securizar($_POST['nombre'] ?? '');
    $descripcion = securizar($_POST['descripcion'] ?? '');
    $fechaFin = securizar($_POST['fechaFin'] ?? '');
    $idTarea = securizar($_POST['idTarea'] ?? '');
    $usuarioElim = securizar($_POST['idUsuario'] ?? '');
    if ($usuarioGrupo->getAdmin()) {
        $usuarioNew = securizar($_POST['usuarioNew'] ?? '');
    } else {
        $usuarioNew = $usuario->getEmail();
    }

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
            if ($usuarioNew) {
                if (!$usuarioDb->seleccionarUsuario($usuarioNew)) {
                    $errores[] = 'Tienes que seleccionar un usuario válido';
                } elseif (!$usuariosGruposDb->seleccionarUsuarioGrupo($grupo->getIdGrupo(), $usuarioDb->seleccionarUsuario($usuarioNew)->getIdUsuario())) {
                    $errores[] = 'Este usuario no está en el grupo';
                }else{
                    $tareaDb->anadirTarea($nombre, $descripcion, $fechaFin, $grupo->getIdGrupo(), $usuarioDb->seleccionarUsuario($usuarioNew)->getIdUsuario());
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                }
            } else {
                if ($usuarioGrupo->getAdmin()) {
                    $tareaDb->anadirTarea($nombre, $descripcion, $fechaFin, $grupo->getIdGrupo(), NULL);
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $tareaDb->anadirTarea($nombre, $descripcion, $fechaFin, $grupo->getIdGrupo(), $usuario->getEmail(), $usuario->getUsuarioId());
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                }
            }

        }
    } elseif (isset($_POST['eliminar'])) {
        $tareaDb->eliminarTarea($idTarea);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
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
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['completada'])) {
        $completada = $_POST['completada'];
        if ($completada) {
            $tareaDb->completarTarea($idTarea);
        } else {
            $tareaDb->descompletarTarea($idTarea);
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif (isset($_POST['eliminarUsuario'])){
        $usuariosGruposDb->eliminarUsuariosGrupos($usuarioElim,$grupo->getIdGrupo());
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
$todasTareas = $tareaDb->seleccionarTareasGrupo($grupo->getIdGrupo());
$todasTareasUsuario = $tareaDb->seleccionarTareasGrupoUsuarios($grupo->getIdGrupo(), $usuario->getIdUsuario());
$todosUsariosGrupo = $usuariosGruposDb->seleccionarUsuariosGrupos($grupo->getIdGrupo());
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
        <img src="<?=$usuario->getImagen() ?? 'fotos/avatar.png'?>">
        <a href="anadirUsuario.php" id="style-4">Add User</a>
        <a href="index.php" id="style-4">back</a>
        <?php if ($usuarioGrupo->getAdmin()): ?>
            <a href="eliminarGrupo.php" id="style-4">Delete Group</a>
        <?php else:?>
            <a href="eliminarGrupo.php" id="style-4">leave the group</a>
        <?php endif;?>
        <a href="logoff.php" id="style-4">logoff</a>
    </header>
    <hr>
    <nav></nav>
    <main>
        <article>
            <header>
                <h2>Users list</h2>
            </header>
            <?php while($usuario = $todosUsariosGrupo->fetch()):?>
                <div style="display: flex;">
                    <p style="margin-right: 2vh;"><?=$usuario->email?></p>
                    <?php if ($usuarioGrupo->getAdmin()): ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <?php if(!$usuariosGruposDb->seleccionarUsuarioGrupo($grupo->getIdGrupo(), $usuario->getIdUsuario())->getAdmin()):?>
                                <button type="submit" name="eliminarUsuario" class="elim"><img src="Css/delete.png"></button>
                                <input type="hidden" name="idUsuario" value='<?= $usuario->getIdUsuario() ?>'>
                            <?php endif;?>
                        </form>
                    <?php endif; ?>
                    </div>
                <br>
            <?php endwhile;?>
        </article>
        <article class="task" >
            <header>
                <h2>Add task</h2>
            </header>
            <fieldset>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="coolinput">
                        <label for="input" class="text">Name:</label>
                        <input type="text" name="nombre" placeholder="Write here..." class="input" value="<?= $nombreAc ?? NULL ?>" required>
                    </div>
                    <div class="coolinput">
                        <label for="input" class="text">Description:</label>
                        <textarea name="descripcion" placeholder="Write here..."><?= $descripcionAc ?? NULL ?></textarea>
                    </div>
                    <?php if ($usuarioGrupo->getAdmin()): ?>
                        <div class="coolinput">
                            <label for="input" class="text">Email(Opcional):</label>
                            <input type="text" name="usuarioNew" placeholder="Write here..." class="input" value="<?= $usuarioAc ?? NULL ?>">
                        </div>
                    <?php endif; ?>
                    <div class="coolinput">
                        <label for="input" class="text">Create date</label>
                        <input type="date" name="fechaFin" class="input" min="<?= date('Y-m-d') ?>" value="<?= $fechaFinAc ?? date('Y-m-d') ?>" required>
                    </div>
                    <div style="display: flex;">
                        <?php if (!isset($_POST['editar'])): ?>
                            <button type="submit" name="anadir" class="button">Add</button>

                        <?php else: ?>
                            <button type="submit" name='actualizar' class="button">Update</button>
                            <input type="hidden" name="idTarea" value='<?= $idTarea ?>'>
                </form>
                <form action="" method="post">
                    <button type="submit" name='volver' class="button">Back</button>
                </form>
                </div>
            <?php endif ?>
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
        </article>
        <hr>
        <div style="display: flex; justify-content: space-around;">
            <article>
                <header>
                    <h2>To-do List</h2>
                </header>
                <?php while ($tarea = $todasTareas->fetch()): ?>
                    <div style="display: flex;">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="checkbox">
                                <input type="hidden" name="completada" value="0">
                                <input type="checkbox" name="completada" value="1" onchange="this.form.submit()" <?= $tarea->getCompletada() ? 'checked' : '' ?>>
                                <label style="margin-right: 1vh;"><?= $tarea->getNombre() ?> - <?= $tarea->getDescripcion() ?> - <?= $tarea->getFechaFin() ?></label>
                            </div>
                            <input type="hidden" name="idTarea" value="<?= $tarea->getIdTarea() ?>">
                        </form>
                        <?php if ($usuarioGrupo->getAdmin()): ?>
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <button type="submit" name="eliminar" class="elim"><img src="Css/delete.png"></button>
                                <button type="submit" name="editar" class="button"><img src="Css/edit.png"></button>
                                <input type="hidden" name="idTarea" value='<?= $tarea->getIdTarea() ?>'>
                            </form>
                        <?php endif; ?>
                    </div>
                    <br>
                <?php endwhile; ?>
            </article>
            <article>
                <?php if ($todasTareasUsuario): ?>

                    <header>
                        <h2>To-do List User</h2>
                    </header>
                    <?php while ($tarea = $todasTareasUsuario->fetch()): ?>
                        <div style="display: flex;">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="checkbox">
                                    <input type="hidden" name="completada" value="0">
                                    <input type="checkbox" name="completada" value="1" onchange="this.form.submit()" <?= $tarea->getCompletada() ? 'checked' : '' ?>>
                                    <label style="margin-right: 1vh;"><?= $tarea->getNombre() ?> - <?= $tarea->getDescripcion() ?> - <?= $tarea->getFechaFin() ?></label>
                                </div>
                                <input type="hidden" name="idTarea" value="<?= $tarea->getIdTarea() ?>">
                            </form>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <button type="submit" name="eliminar" class="elim"><img src="Css/delete.png"></button>
                                <button type="submit" name="editar" class="button"><img src="Css/edit.png"></button>
                                <input type="hidden" name="idTarea" value='<?= $tarea->getIdTarea() ?>'>
                            </form>

                        </div>
                        <br>
                    <?php endwhile; ?>

                <?php endif ?>
            </article>
        </div>
    </main>
    <footer></footer>
</body>

</html>