<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id'])) {
    die(json_encode(['success' => false, 'message' => 'Login richiesto']));
}

$userId = $_SESSION['id'];
$rewardAmount = 100; // Quanti token regalare
$today = date('Y-m-d');

// 1. Controlliamo l'ultima data di riscatto
$query = "SELECT last_daily_bonus FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user['last_daily_bonus'] === $today) {
    echo json_encode(['success' => false, 'message' => 'Hai già riscattato il premio oggi!']);
    exit();
}

// 2. Aggiorniamo token e data
$update = "UPDATE users SET token_balance = token_balance + ?, last_daily_bonus = ? WHERE id = ?";
$upStmt = mysqli_prepare($conn, $update);
mysqli_stmt_bind_param($upStmt, "isi", $rewardAmount, $today, $userId);

if (mysqli_stmt_execute($upStmt)) {
    echo json_encode(['success' => true, 'message' => "Hai ricevuto $rewardAmount token!"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore nel database']);
}
?>