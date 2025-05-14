<?php
require 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

if (!isset($_GET['id'])) {
    header('Location: 404.php');
    exit;
}

$conn = getDBConnection();
$article_id = intval($_GET['id']);

// Получаем статью и увеличиваем счетчик просмотров
$stmt = $conn->prepare("
    SELECT 
        a.*,
        c.name AS category_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    WHERE a.id = ?
");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: 404.php');
    exit;
}

$article = $result->fetch_assoc();


$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> | SportNews</title>
    <link rel="stylesheet" href="article.css">
    <meta name="description" content="<?= htmlspecialchars(substr(strip_tags($article['content']), 0, 160)) ?>">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="article-container">
        <article class="article-full">
            <?php if ($article['image_path']): ?>
                <img src="<?= htmlspecialchars($article['image_path']) ?>" 
                     alt="<?= htmlspecialchars($article['title']) ?>" 
                     class="article-image">
            <?php endif; ?>

            <div class="article-meta">
                <span class="article-category">
                    <?= htmlspecialchars($article['category_name'] ?? 'Без категории') ?>
                </span>
                <span class="article-date">
                    <?= date('d.m.Y H:i', strtotime($article['created_at'])) ?>
                </span>
            </div>

            <h1><?= htmlspecialchars($article['title']) ?></h1>

            <div class="article-content">
                <?= $article['content'] ?>
            </div>
        </article>

        <section class="related-articles">
            <h2>Похожие статьи</h2>
            <?php
            $conn = getDBConnection();
            $related = $conn->query("
                SELECT id, title, image_path 
                FROM articles 
                WHERE category_id = {$article['category_id']} AND id != {$article['id']}
                ORDER BY created_at DESC 
                LIMIT 3
            ");
            
            if ($related->num_rows > 0): ?>
                <div class="related-grid">
                    <?php while($rel_article = $related->fetch_assoc()): ?>
                        <a href="article.php?id=<?= $rel_article['id'] ?>" class="related-item">
                            <?php if ($rel_article['image_path']): ?>
                                <img src="<?= htmlspecialchars($rel_article['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($rel_article['title']) ?>">
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($rel_article['title']) ?></h3>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Нет похожих статей</p>
            <?php endif; 
            $conn->close();
            ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>