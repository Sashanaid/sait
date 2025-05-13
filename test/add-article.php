<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: enter.html');
    exit;
}
require 'config.php';

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $content = $conn->real_escape_string($_POST['content'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    
    // Обработка загрузки изображения
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/articles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $image_path = $conn->real_escape_string($destination);
        }
    }
    
    // Сохранение статьи в БД с автоматической датой создания
    $query = "INSERT INTO articles (title, content, image_path, category_id, created_at) 
              VALUES ('$title', '$content', " . ($image_path ? "'$image_path'" : "NULL") . ", $category_id, NOW())";
    
    if ($conn->query($query)) {
        $conn->close();
        header('Location: /article-added-success.html');
        exit;
    } else {
        $error = "Ошибка при сохранении статьи: " . $conn->error;
    }
}

$conn->close();
?>