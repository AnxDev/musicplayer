<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

include_once 'db.php';
include_once 'new_transaction.php';
$postId = $_POST['post_id'];
$text_content = $_POST['text_content'];
$userId = $_SESSION['id'];
if(isset( $_POST['id_parent'])) {
    $id_parent = $_POST['id_parent'];
} else {
    $id_parent = null;
}
if (!check_if_enough_tokens($userId, "new_comment")) {
    echo "<p id='error'>Non hai abbastanza token per pubblicare un commento. Guadagna più token interagendo con i post o completando attività.</p>";
    exit();
}

$query = "INSERT INTO comments (id_post, id_user, text_content, id_parent) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iisi", $postId, $userId, $text_content, $id_parent);
mysqli_stmt_execute($stmt);
new_transaction($userId, "new_comment", "Pubblicazione commento");
mysqli_stmt_close($stmt);

header("Location: ../pagine/post_details.php?id=" . $postId);
exit();
?>
