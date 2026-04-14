<?php
session_start();

include 'db.php';

if(isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT id, username, email, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);

            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            
            header("Location: ../index.php");
            exit();
        } else {
            echo "<p id='error'>Email o password errati</p>";
        }
    } else {
        echo "<p id='error'>Email o password errati</p>";
    }
    
    mysqli_stmt_close($stmt);
}
?>

<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <?php
        if(isset($_SESSION['id'])) {
            echo "<p>Benvenuto, " . $_SESSION['username'] . "!</p>";
            echo "<a href='logout.php'>Logout</a>";
        } else {
            echo "<p>Non sei loggato.</p>";
        }
    ?>
    <form action="" method="post">
        <h1>Login</h1>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Accedi">
    </form>
</body>
</html>
