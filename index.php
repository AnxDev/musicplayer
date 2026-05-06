<?php
            session_start();
            if (isset($_SESSION['id'])) {
                header("Location: pagine/home.php");
                exit();
            }
        ?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/index.css">
        <title>home</title>
    </head>
    <body>
        
        <h1>Benvenuto</h1>
        <h3>Crea il tuo account</h3>
        <form action="backend/register.php" method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>

            <input type="submit" value="Registrati">
        </form>
        <h3><a href="backend/login.php">Hai già un account?</a></h3>
    </body>
</html>