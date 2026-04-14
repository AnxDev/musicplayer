<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

require 'db.php';
$titolo = $_POST['titolo'];
$data_rilascio = $_POST['data_rilascio'];
$id_utente = $_SESSION['id'];

$directory = "../storage_utenti/".$_SESSION['id']."/album/";
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

$cover_path = $directory . basename($_FILES['cover']['name']);
if (move_uploaded_file($_FILES['cover']['tmp_name'], $cover_path)) {
    $cover_path_db = str_replace("../", "", $cover_path);
    $stmt = mysqli_prepare($conn, "INSERT INTO album (titolo, data_rilascio, cover, id_utente) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssi", $titolo, $data_rilascio, $cover_path_db, $id_utente);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: ../pagine/home.php");
    exit();
} else {
    echo "<p id='error'>Errore durante l'upload della cover</p>";
}
?>