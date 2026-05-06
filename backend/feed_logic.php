<?php
    include_once '../backend/db.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['id'])) {
        header("Location: ../index.php");
        exit();
    }

    $user_id = $_SESSION['id'];

    $query = "SELECT posts.*, users.username, users.id AS user_id_record, 
              (SELECT COUNT(*) FROM comments WHERE id_post = posts.id) AS comment_count, 
              (SELECT COUNT(*) FROM interactions WHERE id_element = posts.id AND element = 'post' AND type = 'like') AS numero_like,
              (SELECT COUNT(*) FROM interactions WHERE id_element = posts.id AND id_user = ? AND element = 'post' AND type = 'like') AS gia_messo_like
              FROM posts 
              JOIN users ON posts.id_user = users.id 
              ORDER BY posts.id DESC";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
?>