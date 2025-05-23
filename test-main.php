<?php 
session_start();
require 'config.php';

// Получаем последние 5 статей из базы данных
$conn = getDBConnection();
$query = "SELECT 
            a.id, 
            a.title, 
            a.content, 
            a.image_path,
            a.created_at,
            a.category_id,
            c.name AS category_name
          FROM articles a
          LEFT JOIN categories c ON a.category_id = c.id
          ORDER BY a.created_at DESC 
          LIMIT 5";

$result = $conn->query($query);
$articles = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статьи - SportNews</title>
    <link rel="stylesheet" href="test-main.css">
</head>
<body>
    <div class="img">
        <?php include("header.php"); ?>
        
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <div class="article-preview">
                    <img src="<?= htmlspecialchars($article['image_path'] ?? 'https://via.placeholder.com/400x200') ?>" 
                         alt="<?= htmlspecialchars($article['title']) ?>" 
                         class="article-image">
                    <div class="article-content">
                        <span class="article-category">
                            <?= htmlspecialchars($article['category_name'] ?? 'Без категории') ?>
                        </span>
                        <h2 class="article-title"><?= htmlspecialchars($article['title']) ?></h2>
                        <p class="article-excerpt">
                            <?= substr(strip_tags($article['content']), 0, 200) ?>...
                        </p>
                        <div class="article-meta">
                            <span class="article-date">
                                <?= date('d.m.Y', strtotime($article['created_at'])) ?>
                            </span>
                            
                        </div>
                        <a href="article.php?id=<?= $article['id'] ?>" class="read-more">Читать далее →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-articles">
                <p>Статьи не найдены. Попробуйте позже.</p>
            </div>
        <?php endif; ?>
        
        <?php include("footer.php"); ?>
    </div>
</body>
</html>