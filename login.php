<?php
require 'functions/securizar.php';
require 'classes/apps/UsuarioDb.php';
require 'functions/verSession.php';
//Inicio de session
session_start();
//Reviso si hay una sessión
if (isset($_SESSION['usuario']) || isset($_COOKIE['recordar'])) {
    header('Location: index.php');
    exit;
}
//Variables
$errores = [];
$usuarioDb = new UsuarioDb();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Variables de los inputs
    $email = securizar($_POST['email'] ?? '');
    $contrasena = securizar($_POST['contrasena'] ?? '');

    if (isset($_POST['entrar'])) {
        //Recogemos el usuario
        $usuario = $usuarioDb->seleccionarUsuario($email);
        //Comprobaciones
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Tienes que introducir un email válido.';
        } elseif (!$contrasena) {
            $errores[] = 'Tienes que introducir una contraseña válida';
        } elseif (strlen($contrasena) < 4 || strlen($contrasena) > 100) {
            $errores[] = 'Tienes que introducir una contraseña de entre 3 y 100 caractéres';
        } elseif (!$usuario) {
            $errores[] = 'Email o contraseña incorrectos';
        } elseif (!password_verify($contrasena, $usuario->getContrasena())) {
            $errores[] = 'Email o contraseña incorrectos';
        }
        //Si no hay errores iniciamos sesión
        if (empty($errores)) {
            if (isset($_POST['recordar'])) {
                $token = bin2hex(random_bytes(32));
                $usuarioDb->editarUsuarioToken($usuario->getEmail(), $token);
                setcookie('recordar', $token, time() + (3600 * 24 * 7));
            }
            $_SESSION['usuario'] = $usuario->getEmail();
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
    <link rel="stylesheet" href="css/estilos.css" type='text/css'>
    <title>Login</title>
</head>

<body>
    <header><h1>Welcome To To-Do List</h1></header>
    <nav></nav>
    <main>
        <fieldset>
            <legend>Login</legend>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label><p><input type="email" name="email" placeholder="Email" required></p></label>
                <label><p><input type="password" name="contrasena" placeholder="Password" required></p></label>
                <p>recordar: <input type="checkbox" name="recordar"></p>
                <input type="submit" value="Sing in" name="entrar" style="margin-right: 2vh;">
                <a href="register.php">Sing up</a>
            </form>
            <?php if (!empty($errores)): ?>
                <?php foreach ($errores as $error): ?>
                    <p class='errores'>*<?= $error ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </fieldset>
    </main>
    <footer></footer>
</body>

</html>