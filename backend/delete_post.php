<?php
include 'db.php';
session_start();
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$postId = $_GET['id'];
$userId = $_SESSION['id'];

$query = "DELETE FROM posts WHERE id = ? AND id_user = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $postId, $userId);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
//delete file associated with the post
$query = "SELECT image_content FROM posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
if ($row) {
    $file_path = "../storage_utenti/". $userId . "/posts/" . $row['image_content'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}
header("Location: ../pagine/home.php");
exit();
?>

