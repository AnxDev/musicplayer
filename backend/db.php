<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "economy_social";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function get_user_id_of_post($post_id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT id_user FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_id = mysqli_fetch_assoc($result);
    return $user_id['id_user'];
}

function get_token_balance($user_id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT token_balance FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $token_balance = mysqli_fetch_assoc($result);
    return $token_balance['token_balance'];
}

function get_value_of_operation($operation) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT value FROM economy WHERE type = ?");
    mysqli_stmt_bind_param($stmt, "s", $operation);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $value = mysqli_fetch_assoc($result);
    return $value['value'];
}

function check_if_enough_tokens($user_id, $operation) {
    $token_balance = get_token_balance($user_id);
    $required_tokens = get_value_of_operation($operation);
    return $token_balance >= -$required_tokens;
}


?>
