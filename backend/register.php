<?php
    include 'db.php';
    if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password = password_hash($password, PASSWORD_BCRYPT); // Hash della password

        // Check if username already exists
        $sql_check_username = "SELECT id FROM users WHERE username = '$username'";
        $result_username = mysqli_query($conn, $sql_check_username);
        if (mysqli_num_rows($result_username) > 0) {
            echo "Username already exists.";
            exit;
        }

        // Check if email already exists
        $sql_check_email = "SELECT id FROM users WHERE email = '$email'";
        $result_email = mysqli_query($conn, $sql_check_email);
        if (mysqli_num_rows($result_email) > 0) {
            echo "Email already exists.";
            exit;
        }

        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if (mysqli_query($conn, $sql)) {
            echo "Registrazione avvenuta con successo!";
            header("Location: login.php");
        } else {
            echo "Errore: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
?>