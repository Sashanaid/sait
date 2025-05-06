<?php
// Конфигурация базы данных
define('DB_HOST', '127.0.0.1');  // Или IP-адрес сервера
define('DB_PORT', '3306');       // Порт MySQL (по умолчанию 3306)
define('DB_USER', 'root');       // Имя пользователя
define('DB_PASS', 'кщще');           // Пароль
define('DB_NAME', 'new_schema'); // Имя базы данных

// Устанавливаем соединение с базой данных с указанием порта
function getDBConnection() {
    // Создаем строку подключения с портом
    $conn = new mysqli(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    return $conn;
}

// Альтернативный вариант подключения (более современный)
function getDBConnectionAlt() {
    // Создаем DSN (Data Source Name) строку
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8';
    
    try {
        $conn = new PDO($dsn, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Закрытие соединения
function closeDBConnection($conn) {
    // Для mysqli
    if ($conn instanceof mysqli) {
        $conn->close();
    }
    // Для PDO (просто обнуляем соединение)
    elseif ($conn instanceof PDO) {
        $conn = null;
    }
}
?>