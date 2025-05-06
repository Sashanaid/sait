<?php
session_start();
require_once 'config.php'; // Подключение к БД
$conn = getDBConnection();

function hasAccess( $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE id = ? ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

// Проверка
if (!isset($_SESSION['user_id']) || !hasAccess($_SESSION['user_id'])) {
    header('Location: enter.html');
    exit();
}else{
    header('Location: test.php');
    exit();
}
?>