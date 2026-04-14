<?php
session_start();
    require 'db.php';
    if (!isset($_SESSION['id'])) {
        header("Location: ../index.php");
        exit();
    }
    $id_album = $_GET['id_album'];
    $directory = "../storage_utenti/ ".$_SESSION['id']."/brani/";
    $durata = $_POST['durata'];
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    $titolo = $_POST['titolo'];
    $file_path = $directory . basename($_FILES['brano']['name']);
    if (move_uploaded_file($_FILES['brano']['tmp_name'], $file_path)) {
        $file_path_db = str_replace("../", "", $file_path);
        $stmt = mysqli_prepare($conn, "INSERT INTO brani (titolo, path, durata, id_album) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssi", $titolo, $file_path_db, $durata, $id_album);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: album.php?album_id=".$_GET['id_album']);
        exit();
    } else {
        echo "<p id='error'>Errore durante l'upload del brano</p>";
    }
?>

