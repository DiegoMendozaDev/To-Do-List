<?php
    require 'functions/securizar.php';
    require 'classes/apps/UsuarioDb.php';
    require 'functions/verSession.php';
    //Inicio de session
    session_start();
    //Reviso si hay una sessión
    if(isset($_SESSION['usuario'])||isset($_COOKIE['recordar'])){
        header('Location: index.php');
        exit;
    }
    //Variables
    $errores = [];
    $usuarioDb = new UsuarioDb();

    if($_SERVER['REQUEST_METHOD']=='POST'){
        //Variables de los inputs
        $email=$contrasena='';
        if(isset($_POST['email']) && isset($_POST['contrasena'])){
            $email = securizar($_POST['email']);
            $contrasena = securizar($_POST['contrasena']);
        }else{
            $errores[]= 'No puedes hacer eso';
        }

        if(isset($_POST['entrar']) && empty($errores)){
            //Recogemos el usuario
            $usuario = $usuarioDb->comprobarUsuario($email);
            //Comprobaciones
            if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errores[]='Tienes que introducir un email válido.';
            }elseif(!$contrasena){
                $errores[]='Tienes que introducir una contraseña válida';
            }elseif(strlen($contrasena)<4 || strlen($contrasena)>100){
                $errores[]='Tienes que introducir una contraseña de entre 3 y 100 caractéres';
            }elseif(!$usuario){
                $errores[]='Email o contraseña incorrectos';
            }elseif(!password_verify($contrasena,$usuario->getContrasena())){
                $errores[]='Email o contraseña incorrectos';
            }
            //Si no hay errores iniciamos sesión
            if(empty($errores)){
                if(isset($_POST['recordar'])){
                    $token = bin2hex(random_bytes(32));
                    $usuarioDb->editarUsuarioToken($usuario->getEmail(),$token);
                    setcookie('recordar',$token,time()+(3600*24*7));
                }
                $_SESSION['usuario']=$usuario->getEmail();
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
    <title>Login</title>
</head>
<body>
    <head></head>
    <nav></nav>
    <main>
        <fieldset>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label><input type="email" name="email" placeholder="Email" required></label>
                <label><input type="password" name="contrasena" placeholder="Password" required></label>
                recordar: <input type="checkbox" name="recordar">
                <input type="submit" value="Sing in" name="entrar">
            </form>
            <a href="register.php">Sing up</a>
            <?php if(!empty($errores)):?>
                <?php foreach($errores as $error):?>
                    <p class='errores'>*<?=$error?></p>
                <?php endforeach;?>
            <?php endif;?>
        </fieldset>
    </main>
    <footer></footer>
</body>
</html>