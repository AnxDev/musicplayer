<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

require 'db.php';
$text_content = $_POST['text_content'];
$id_utente = $_SESSION['id'];

if (!check_if_enough_tokens($id_utente, "new_post")) {
    echo "<p id='error'>Non hai abbastanza token per pubblicare un post. Guadagna più token interagendo con i post o completando attività.</p>";
    exit();
}

$directory = "../storage_utenti/".$_SESSION['id']."/posts/";
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

$image_path = $directory . basename($_FILES['image_content']['name']);
if (move_uploaded_file($_FILES['image_content']['tmp_name'], $image_path)) {
    $image_path_db = str_replace("../", "", $image_path);
    $stmt = mysqli_prepare($conn, "INSERT INTO posts (text_content, image_content, id_user) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssi", $text_content, $image_path_db, $id_utente);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    include 'new_transaction.php';
    new_transaction($id_utente, "new_post", "Pubblicazione post");

    header("Location: ../pagine/home.php");
    exit();
} else {
    echo "<p id='error'>Errore durante l'upload della foto</p>";
}
?>