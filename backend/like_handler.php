<?php
session_start();
// Riattiva gli errori per il debug, ma mantieni il Content-Type JSON
error_reporting(E_ALL); 
ini_set('display_errors', 1); 
header('Content-Type: application/json');

if (!isset($_SESSION['id']) || !isset($_GET['id']) || !isset($_GET['element'])) {
    echo json_encode(['error' => 'invalid request']);
    exit();
}

include_once 'db.php';
include_once 'new_transaction.php';
$elementId = (int) $_GET['id'];
$element   = $_GET['element'] === 'comment' ? 'comment' : 'post';
$userId    = $_SESSION['id'];

// 1. Controllo se il like esiste già
$check = mysqli_prepare($conn, "SELECT id FROM interactions WHERE id_user=? AND id_element=? AND element=? AND type='like'");
mysqli_stmt_bind_param($check, "iis", $userId, $elementId, $element);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
$already_liked = mysqli_stmt_num_rows($check);
mysqli_stmt_close($check); // LIBERA IL RISULTATO PRIMA DI PROCEDERE

$useroflikedelement = null;
if ($element == 'comment') {
    $useroflikedelement = mysqli_prepare($conn, "SELECT id_user FROM comments WHERE id=?");
} else {
    $useroflikedelement = mysqli_prepare($conn, "SELECT id_user FROM posts WHERE id=?");
}
mysqli_stmt_bind_param($useroflikedelement, "i", $elementId);
mysqli_stmt_execute($useroflikedelement);
$result = mysqli_stmt_get_result($useroflikedelement);
$useroflikedelement = mysqli_fetch_assoc($result);
$useroflikedelement = $useroflikedelement['id_user'];
if ($already_liked > 0) {
    // 2. Rimuovo il like
    if ($useroflikedelement != $userId) {
        new_transaction($useroflikedelement, "unliked", "Rimosso like");
    }
    $del = mysqli_prepare($conn, "DELETE FROM interactions WHERE id_user=? AND id_element=? AND element=? AND type='like'");
    mysqli_stmt_bind_param($del, "iis", $userId, $elementId, $element);
    mysqli_stmt_execute($del);
    mysqli_stmt_close($del);
    
    echo json_encode(['liked' => false]);
} else {
    // 3. Aggiungo il like
    if ($useroflikedelement != $userId) {
        new_transaction($useroflikedelement, "liked", "Ricevuto like");
    }
    
    $ins = mysqli_prepare($conn, "INSERT INTO interactions (id_user, id_element, element, type) VALUES (?,?,?,'like')");
    mysqli_stmt_bind_param($ins, "iis", $userId, $elementId, $element);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
    
    echo json_encode(['liked' => true]);
}
exit();