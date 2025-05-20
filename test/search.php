<?php
// search.php
session_start();
require 'config.php';

$searchTerm = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
$articles = [];

if (!empty($searchTerm)) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT id, title, content, image_path, created_at 
        FROM articles 
        WHERE title LIKE ? 
        ORDER BY created_at DESC
    ");
    
    $searchParam = "%" . $searchTerm . "%";
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }
    
    $stmt->close();
    $conn->close();
}


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результаты поиска: <?= htmlspecialchars($searchTerm) ?> | SportNews</title>
    <link rel="stylesheet" href="test-main.css">
</head>
<body>
    <?php include "header.php"?>
    <main class="search-results-page">
        <h1>Результаты поиска: "<?= htmlspecialchars($searchTerm) ?>"</h1>
        
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
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>