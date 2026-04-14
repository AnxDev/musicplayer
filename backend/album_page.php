<?php

require 'db.php';
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
$id_album = $_GET['album_id'];
$id_utente = $_SESSION['id'];


$query = "SELECT * FROM album WHERE id = ? AND id_utente = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id_album, $id_utente);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$is_owner = false;
if(mysqli_num_rows($result) > 0) {
    $is_owner = true;
}
?>