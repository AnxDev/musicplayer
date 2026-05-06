<?php

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}
include_once 'db.php';
function new_transaction($id_utente, $type, $description) {
    global $conn;
    $value = get_value_of_operation($type);

    $stmt = mysqli_prepare($conn, "SELECT token_balance FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_utente);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $token_balance = mysqli_fetch_assoc($result);
    $token_balance = $token_balance['token_balance'];
    $token_balance = $token_balance + $value;
    $stmt = mysqli_prepare($conn, "UPDATE users SET token_balance = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $token_balance, $id_utente);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "INSERT INTO transaction_history (id_user, value, description) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iis", $id_utente, $value, $description);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

}
?>