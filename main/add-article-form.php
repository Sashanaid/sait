<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id']!=1) {
    header('Location: enter.html');
    exit;
}

require 'config.php';

// Получаем соединение с базой данных
// Добавьте проверку подключения
$conn = getDBConnection();


// Получаем список категорий
$categories = [];
$query = "SELECT id, name FROM categories";
$result = $conn->query($query);

if ($result === false) {
    die("Ошибка при выполнении запроса: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить статью | SportNews</title>
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #dc2626;
            --text-color: #1f2937;
            --light-color: #f8fafc;
            --gray-color: #e5e7eb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f3f4f6;
            padding: 20px;
            position: relative;
        }
        
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--gray-color);
            border-radius: 6px;
            font-size: 16px;
        }
        
        textarea {
            min-height: 200px;
            resize: vertical;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 300px;
            margin-top: 10px;
            display: none;
        }
        
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #1a2f6b;
        }
        
        .error {
            color: var(--secondary-color);
            margin-top: 5px;
        }

        .back-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
            font-size: 16px;
        }

        .back-btn:hover {
            background-color: #b91c1c;
        }

        @media (max-width: 768px) {
            .back-btn {
                padding: 8px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="main_href">
        <a href="test-main.php" class="back-btn">Назад</a>
    </div>
    
    <div class="form-container">
        <h1>Добавить новую статью</h1>
        
        <form id="articleForm" action="add-article.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Заголовок статьи</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">Категория</label>
                <select id="category_id" name="category_id" required>
                    <option value="">-- Выберите категорию --</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="content">Текст статьи</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Изображение статьи</label>
                <input type="file" id="image" name="image" accept="image/*">
                <img id="imagePreview" class="image-preview" src="#" alt="Предпросмотр изображения">
            </div>
            
            <button type="submit" class="btn">Опубликовать статью</button>
        </form>
    </div>

    <script>
        // Предпросмотр изображения перед загрузкой
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
        
        // Валидация формы
        document.getElementById('articleForm').addEventListener('submit', function(e) {
            const title = document.getElementById('title').value.trim();
            const category = document.getElementById('category_id').value;
            const content = document.getElementById('content').value.trim();
            
            if (!title || !category || !content) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
            }
        });
    </script>
</body>
</html>