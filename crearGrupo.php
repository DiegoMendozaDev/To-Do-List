<?php

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
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <label><input type="text" name="nombre" placeholder="Group name"></label>
                    <input type="submit" value="Crear">
                </form>
            </fieldset>
        </article>           
    </main>
    <footer></footer>
</body>
</html>