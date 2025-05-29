<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Файл для логов
$logFile = __DIR__ . '/register_errors.log';
file_put_contents($logFile, "----- New Request -----\n", FILE_APPEND);

function logError($message) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . "\n", FILE_APPEND);
}
try {
    logError("Starting registration process");
    
    require_once 'config.php';
    logError("Config loaded");
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        logError("Invalid request method");
        http_response_code(405);
        die(json_encode(['success' => false, 'message' => 'Invalid request method']));
    }
    
    // Получаем данные
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    logError("Received data: " . print_r($_POST, true));
    
    // ... остальной код валидации ...
    
    $conn = getDBConnection();
    logError("Database connection established");
    
    // Проверка существования пользователя
    $checkQuery = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    
    if (!$stmt) {
        logError("Prepare failed: " . $conn->error);
        throw new Exception("Database prepare failed");
    }
    
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        logError("User already exists");
        echo json_encode(['success' => false, 'errors' => [
            'username' => 'Имя пользователя или email уже заняты'
        ]]);
        exit;
    }
    
    // Хеширование пароля
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    logError("Password hashed");
    
    // Создание пользователя
    $insertQuery = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    
    if (!$stmt) {
        logError("Prepare failed: " . $conn->error);
        throw new Exception("Database prepare failed");
    }
    
    $stmt->bind_param("sss", $username, $email, $passwordHash);
    $executed = $stmt->execute();
    
    if ($executed) {
        logError("User registered successfully");
        echo json_encode(['success' => true, 'message' => 'Регистрация успешна!']);
    } else {
        logError("Execute failed: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $stmt->error]);
    }
    
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    logError("Stack trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'Внутренняя ошибка сервера: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        closeDBConnection($conn);
        logError("Database connection closed");
    }
    logError("----- End of Request -----\n");
}
?>