<?php
require 'functions/securizar.php';
require 'functions/verSession.php';
require 'classes/apps/UsuarioDb.php';
session_start();
//Declaracion de variables
$session = $token = '';
$errores = [];
$usuarioDb = new UsuarioDb();

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
    $tamano = $_FILES['perfil']['size'] ?? '';
    //Procesar la solicitud de añadir tarea
    if (isset($_POST['actualizar'])) {
        if(!$nombre){
            $errores[] = 'Tienes que introducir un nombre';
        }
        if(empty($errores)){
            $usuarioDb->editarUsuario($usuario->getIdUsuario(), $nombre, $descripcion);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } elseif (isset($_POST['foto'])){
        if($tamano > 256*1024){
            $errores[] = 'Archivo demasiado grande';
        }
        if(empty($errores)){
            $guardar = move_uploaded_file($_FILES['perfil']['tmp_name'], 'fotos/'.$_FILES['perfil']['name']);
            if($guardar){
                $usuarioDb->editarUsuarioFoto($usuario->getIdUsuario(),'fotos/'.$_FILES['perfil']['name']);
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }else{
                $errores[]='Error al subir la imagen';
            }
        }
    }
}

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
        <img src="<?=$usuario->getImagen() ?? 'fotos/avatar.png'?>">
        <a href="index.php" id="style-4">Back</a>
        <a href="logoff.php" id="style-4">logoff</a>
    </header>
    <hr>
    <nav></nav>
    <main>
        <article class="task">
            <header>
                <h2>Profile</h2>
            </header>
            <fieldset>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <div class="coolinput">
                        <label for="input" class="text">Choose your profile picture</label>
                        <input type="file" name="perfil" class="input">
                    </div>
                    <button type="submit" name='foto' class="button" style="margin-bottom: 2vh;">Add</button>
                </form>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="coolinput">
                        <label for="input" class="text">Name:</label>
                        <input type="text" name="nombre" class="input" value="<?= $usuario->getNombre() ?>" required>
                    </div>
                    <div class="coolinput">
                        <label for="input" class="text">Description:</label>
                        <textarea name="descripcion"><?= $usuario->getDescripcion() ?? '' ?></textarea>
                    </div>
                    <button type="submit" name='actualizar' class="button">Update</button>
                </form>
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
    </main>
    <footer></footer>
</body>

</html>