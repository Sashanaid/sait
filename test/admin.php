<?php
session_start();
require 'config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_id']!=1) {
    header('Location: enter.html');
    exit;
}

// Получаем информацию о единственном авторе
$conn = getDBConnection();
$author = $conn->query("SELECT * FROM authors LIMIT 1")->fetch_assoc();

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_article') {
        // ... существующий код для статей ...
    } 
    elseif ($action === 'update_author') {
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $bio = $conn->real_escape_string($_POST['bio'] ?? '');
        
        // Обработка загрузки аватара
        $avatar_path = $author['avatar'] ?? '';
        if (!empty($_FILES['avatar']['name'])) {
            $upload_dir = 'uploads/authors/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $file_name = 'author_' . time() . '.' . $file_ext;
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
                // Удаляем старый аватар, если он существует
                if (!empty($avatar_path) && file_exists($avatar_path)) {
                    unlink($avatar_path);
                }
                $avatar_path = $target_path;
            }
        }
        
        if ($author) {
            // Обновляем существующего автора
            $query = "UPDATE authors SET 
                      name = '$name', 
                      bio = '$bio', 
                      avatar = '$avatar_path' 
                      WHERE id = {$author['id']}";
        } else {
            // Создаем нового автора (если в базе его еще нет)
            $query = "INSERT INTO authors (name, bio, avatar) 
                      VALUES ('$name', '$bio', '$avatar_path')";
        }
        
        $conn->query($query);
        $_SESSION['message'] = 'Профиль автора успешно обновлен';
        header("Location: admin.php");
        exit();
    }
    elseif ($action === 'delete_article') {
        $article_id = intval($_POST['article_id']);
        $conn->query("DELETE FROM articles WHERE id = $article_id");
        
        $_SESSION['message'] = 'Статья успешно удалена';
        header("Location: admin.php");
        exit();
    }
    
}

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_article') {
        $article_id = intval($_POST['article_id']);
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $category_id = intval($_POST['category_id']);
        
        // Обработка загрузки изображения
        $image_path = $_POST['existing_image'];
        if (!empty($_FILES['image']['name'])) {
            $upload_dir = 'uploads/';
            $file_name = basename($_FILES['image']['name']);
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = $target_path;
            }
        }
        
        $query = "UPDATE articles SET 
                  title = '$title', 
                  content = '$content', 
                  category_id = $category_id, 
                  image_path = '$image_path' 
                  WHERE id = $article_id";
        $conn->query($query);
        
        $_SESSION['message'] = 'Статья успешно обновлена';
        header("Location: admin.php");
        exit();
    }
    elseif ($action === 'delete_article') {
        $article_id = intval($_POST['article_id']);
        $conn->query("DELETE FROM articles WHERE id = $article_id");
        
        $_SESSION['message'] = 'Статья успешно удалена';
        header("Location: admin.php");
        exit();
    }
}

// Получаем список статей для администрирования
$articles_query = "SELECT a.id, a.title, a.content, a.image_path, a.created_at, c.name AS category_name 
                   FROM articles a 
                   LEFT JOIN categories c ON a.category_id = c.id 
                   ORDER BY a.created_at DESC";
$articles_result = $conn->query($articles_query);
$articles = [];

if ($articles_result && $articles_result->num_rows > 0) {
    while($row = $articles_result->fetch_assoc()) {
        $articles[] = $row;
    }
}

// Получаем список категорий для выпадающего списка
$categories_result = $conn->query("SELECT id, name FROM categories");
$categories = [];
while($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора - SportNews</title>
    <link rel="stylesheet" href="test-main.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .admin-title {
            margin: 0;
            color: var(--primary-color);
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            background: #d4edda;
            color: #155724;
        }
        
        .article-list {
            margin-top: 30px;
        }
        
        .article-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        
        .article-actions {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            margin-left: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .edit-btn {
            background: #007bff;
            color: white;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
        }
        
        .edit-form {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .textarea-control {
            min-height: 150px;
        }
        
        .submit-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .author-edit-section {
            margin-bottom: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .author-edit-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .author-edit-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }
        
        .author-edit-form .form-group {
            margin-bottom: 15px;
        }
        
        .author-edit-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .author-edit-form .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .author-edit-form .textarea-control {
            min-height: 100px;
        }
        
        .current-avatar {
            max-width: 200px;
            display: block;
            margin-bottom: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>
    
    <div class="img">
        <div class="admin-container">
            <div class="admin-header">
                <h1 class="admin-title">Панель администратора</h1>
                <a href="add-article-form.php" class="logout-btn">добавить статьи</a>
            </div>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message"><?= $_SESSION['message'] ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <!-- Секция редактирования автора -->
            <div class="author-edit-section">
                <h2>Редактирование профиля автора</h2>
                
                <form method="POST" enctype="multipart/form-data" class="author-edit-form">
                    <input type="hidden" name="action" value="update_author">
                    
                    <div class="form-group">
                        <label for="author-name">Имя автора</label>
                        <input type="text" class="form-control" id="author-name" name="name" 
                               value="<?= htmlspecialchars($author['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="author-bio">Биография</label>
                        <textarea class="form-control textarea-control" id="author-bio" name="bio" 
                                  required><?= htmlspecialchars($author['bio'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Текущий аватар:</label>
                        <?php if (!empty($author['avatar'])): ?>
                            <img src="<?= htmlspecialchars($author['avatar']) ?>" class="current-avatar">
                        <?php else: ?>
                            <p>Аватар не установлен</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="author-avatar">Новый аватар (оставьте пустым, чтобы не изменять)</label>
                        <input type="file" class="form-control" id="author-avatar" name="avatar" accept="image/*">
                    </div>
                    
                    <button type="submit" class="submit-btn">Сохранить изменения</button>
                </form>
            </div>

            <div class="article-list">
                <h2>Управление статьями</h2>
                
                <?php if (!empty($articles)): ?>
                    <?php foreach ($articles as $article): ?>
                        <div class="article-item" id="article-<?= $article['id'] ?>">
                            <div class="article-actions">
                                <button class="edit-btn" onclick="toggleEditForm(<?= $article['id'] ?>)">Редактировать</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_article">
                                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('Вы уверены, что хотите удалить эту статью?')">Удалить</button>
                                </form>
                            </div>
                            
                            <h3><?= htmlspecialchars($article['title']) ?></h3>
                            <p><strong>Категория:</strong> <?= htmlspecialchars($article['category_name']) ?></p>
                            <p><strong>Дата публикации:</strong> <?= date('d.m.Y', strtotime($article['created_at'])) ?></p>
                            
                            <div class="edit-form" id="edit-form-<?= $article['id'] ?>">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="update_article">
                                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                    
                                    <div class="form-group">
                                        <label for="title-<?= $article['id'] ?>">Заголовок</label>
                                        <input type="text" class="form-control" id="title-<?= $article['id'] ?>" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="content-<?= $article['id'] ?>">Содержание</label>
                                        <textarea class="form-control textarea-control" id="content-<?= $article['id'] ?>" name="content" required><?= htmlspecialchars($article['content']) ?></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="category-<?= $article['id'] ?>">Категория</label>
                                        <select class="form-control" id="category-<?= $article['id'] ?>" name="category_id" required>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category['id'] ?>" <?= ($category['name'] == $article['category_name']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($category['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Текущее изображение:</label>
                                        <img src="<?= htmlspecialchars($article['image_path'] ?? 'https://via.placeholder.com/400x200') ?>" style="max-width: 200px; display: block; margin-bottom: 10px;">
                                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($article['image_path'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="image-<?= $article['id'] ?>">Новое изображение (оставьте пустым, чтобы не изменять)</label>
                                        <input type="file" class="form-control" id="image-<?= $article['id'] ?>" name="image">
                                    </div>
                                    
                                    <button type="submit" class="submit-btn">Сохранить изменения</button>
                                    <button type="button" class="edit-btn" onclick="toggleEditForm(<?= $article['id'] ?>)">Отмена</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Нет статей для отображения.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include("footer.php"); ?>
    
    <script>
        function toggleEditForm(articleId) {
            const form = document.getElementById(`edit-form-${articleId}`);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>