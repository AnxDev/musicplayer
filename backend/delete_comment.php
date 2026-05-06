<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
include 'db.php';

$query = "SELECT id_user FROM comments WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
mysqli_stmt_execute($stmt);
$commentUserId = mysqli_stmt_get_result($stmt);
$commentUserId = mysqli_fetch_assoc($commentUserId);
$commentUserId = $commentUserId['id_user'];


if($_SESSION['id'] != $commentUserId ) {
    echo "<p id='error'>Non hai i permessi per eliminare questo commento</p>";
    exit();
}
if(isset($_GET['id']) ) {
    $commentId = $_GET['id'];
    $stmt = mysqli_prepare($conn, "DELETE FROM comments WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $commentId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Elimina anche i commenti figli
    $stmt = mysqli_prepare($conn, "DELETE FROM comments WHERE id_parent = ?");
    mysqli_stmt_bind_param($stmt, "i", $commentId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect alla pagina precedente
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    echo "<p id='error'>ID del commento non specificato</p>";
}
?>