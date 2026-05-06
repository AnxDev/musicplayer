<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
include 'db.php';
if(isset($_POST['username']) && isset($_POST['bio'] )) {
    $username = $_POST['username'];
    $bio = $_POST['bio'];
    
    $userId = $_SESSION['id'];
    if(!empty($_FILES['pfp']['name'])) {
        $pfp = $_FILES['pfp'];
        $directory = "../storage_utenti/".$userId."/";
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $pfp_path = $directory . basename($pfp['name']);
        if (move_uploaded_file($pfp['tmp_name'], $pfp_path)) {
            $pfp_path_db = str_replace("../", "", $pfp_path);
        } else {
            echo "<p id='error'>Errore durante l'upload della foto del profilo</p>";
            exit();
        }
    } else {
        $pfp_path_db = $_SESSION['pfp'];
    }
    
    
    $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, bio = ?, pfp = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $username, $bio, $pfp_path_db, $userId);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['username'] = $username;
        $_SESSION['bio'] = $bio;
        $_SESSION['pfp'] = $pfp_path_db;
        header("Location: ../pagine/home.php");
        exit();
    } else {
        echo "<p id='error'>Errore durante l'aggiornamento del profilo</p>";
    }
    
    mysqli_stmt_close($stmt);
}
?>
