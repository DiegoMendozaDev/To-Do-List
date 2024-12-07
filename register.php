<?php
    require 'functions/securizar.php';
    require 'classes/apps/UsuarioDb.php';

    //Inicio de session
    session_start();
    //Reviso si hay una sessión
    if(isset($_SESSION['usuario']) || isset($_COOKIE['recordar'])){
        header('Location: index.php');
        exit;
    }
    //Variables
    $errores = [];
    $usuarioDb = new UsuarioDb();

    //Procesamos las solicitudes en metodo post
    if($_SERVER['REQUEST_METHOD']=='POST'){
        //Variables de los inputs
        $email=securizar($_POST['email']??'');
        $contrasena= securizar($_POST['contrasena']??'');
        $contrasenaRepetida=securizar($_POST['contrasenaRepetida']??'');
        //Procesar la solicitud de registrar a alguin
        if(isset($_POST['registrar'])){
            //Vemos si hay algun usuario con este email
            $usuario = $usuarioDb->comprobarUsuario($email);
            //Validaciones de los campos
            if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errores[]='Tienes que introducir un email válido';
            }elseif(!$contrasena){
                $errores[]='Tienes que introducir una contraseña';
            }elseif(strlen($contrasena)<4 || strlen($contrasena)>100){
                $errores[]='Tienes que introducir una contraseña de entre 3 y 100 caractéres';
            }elseif($contrasena!=$contrasenaRepetida){
                $errores[]='Las contraseñas tienen que coincidir';
            }elseif($usuario){
                $errores[]='Ya hay un usuario con ese email registrado';
            }
            //Si no hay errores se registra
            if(empty($errores)){
                $contrasenaHash = password_hash($contrasena,PASSWORD_DEFAULT);
                $usuarioDb->anadirUsuario($email,$contrasenaHash);
                $_SESSION['usuario']=$email;
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
    <title>Register</title>
</head>
<body>
    <head></head>
    <nav></nav>
    <main>
        <fieldset>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label><input type="email" name="email" placeholder="Email" required></label>
                <label><input type="password" name="contrasena" placeholder="Password" required></label>
                <label><input type="password" name="contrasenaRepetida" placeholder="Repeat password"></label>
                <input type="submit" value="Sing up" name="registrar">
            </form>
            <a href="login.php">Go back</a>
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