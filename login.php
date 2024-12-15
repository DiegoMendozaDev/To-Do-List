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
    <link rel="stylesheet" href="css/login.css" type='text/css'>
    <title>Login</title>
</head>

<body>

    <nav></nav>
    <main>
        <section class="login">
            <header>
                <h2>Welcome To To-Do List</h2>
            </header>
            <fieldset>
                <legend>Login</legend>
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
                    <div class="wave-group">
                        <input type="password" name="contrasena" class='input' required>
                        <span class="bar"></span>
                        <label class="label">
                            <span class="label-char" style="--index: 0">P</span>
                            <span class="label-char" style="--index: 1">a</span>
                            <span class="label-char" style="--index: 2">s</span>
                            <span class="label-char" style="--index: 3">s</span>
                            <span class="label-char" style="--index: 4">w</span>
                            <span class="label-char" style="--index: 5">o</span>
                            <span class="label-char" style="--index: 6">r</span>
                            <span class="label-char" style="--index: 7">d</span>
                        </label>
                    </div>
                    <br>
                    <div>
                        recordar:
                        <label for="cbx" class="cbx">
                            <div class="checkmark">
                                <input type="checkbox" name="recordar" style="display: none;" id="cbx">
                                <div class="flip">
                                    <div class="front"></div>
                                    <div class="back">
                                        <svg viewBox="0 0 16 14" height="14" width="16">
                                            <path d="M2 8.5L6 12.5L14 1.5"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <br>
                    <input type="submit" value="Sing in" name="entrar" button class="button-59" role="button">
                    <a href="register.php" id="style-4">Sing up</a>
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