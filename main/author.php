<?php
session_start();
require 'config.php';


// Получаем информацию об авторе
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM authors LIMIT 1");

$stmt->execute();
$author_result = $stmt->get_result();
$author = $author_result->fetch_assoc();






$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($author['name']) ? htmlspecialchars($author['name']) : 'Автор' ?> - Автор - SportNews</title>
    <link rel="stylesheet" href="test-main.css">
    <style>
        .author-profile {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .author-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .author-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 30px;
        }
        
        .author-info h1 {
            margin: 0 0 10px 0;
            color: var(--primary-color);
        }
        
        .author-bio {
            line-height: 1.6;
            color: #555;
        }
        
        .author-articles {
            margin-top: 40px;
        }
        
        .author-articles h2 {
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>
    
    <div class="img">
        <div class="author-profile">
            <div class="author-header">
                <img src="<?= isset($author['avatar']) ? htmlspecialchars($author['avatar']) : 'https://via.placeholder.com/150' ?>" 
                     alt="<?= isset($author['name']) ? htmlspecialchars($author['name']) : 'Аватар автора' ?>" 
                     class="author-avatar">
                <div class="author-info">
                    <h1><?= isset($author['name']) ? htmlspecialchars($author['name']) : 'Неизвестный автор' ?></h1>
                    <div class="author-bio"><?= isset($author['bio']) ? nl2br(htmlspecialchars($author['bio'])) : 'Автор пока не добавил информацию о себе.' ?></div>
                </div>
            </div>
            
            
        </div>
    </div>
    
    <?php include("footer.php"); ?>
</body>
</html>