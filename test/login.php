<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Получаем данные из формы
$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

// Валидация данных
$errors = [];

if (empty($login)) {
    $errors['login'] = 'Логин обязателен';
}

if (empty($password)) {
    $errors['password'] = 'Пароль обязателен';
}

// Если есть ошибки, возвращаем их
if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Проверяем учетные данные
$conn = getDBConnection();

$stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $login, $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $errors['login'] = 'Неверный логин или email';
    echo json_encode(['success' => false, 'errors' => $errors]);
    $stmt->close();
    closeDBConnection($conn);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    $errors['password'] = 'Неверный пароль';
    echo json_encode(['success' => false, 'errors' => $errors]);
    $stmt->close();
    closeDBConnection($conn);
    exit;
}

// Успешный вход - начинаем сессию
session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];

$stmt->close();
closeDBConnection($conn);

echo json_encode(['success' => true, 'message' => 'Вход выполнен успешно', 'redirect' => 'profile.php']);
?>