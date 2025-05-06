<?php
require 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    
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
            $image_path = $destination;
        }
    }
    
    // Сохранение статьи в БД
    $stmt = $pdo->prepare("INSERT INTO articles (title, content, image_path, category_id) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$title, $content, $image_path, $category_id])) {
        header('Location: /article-added-success.html');
        exit;
    } else {
        $error = "Ошибка при сохранении статьи";
    }
}
?>