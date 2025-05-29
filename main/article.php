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

// Проверка прав администратора
$is_admin = isset($_SESSION['user_id']) && $_SESSION['user_id'] === 1;

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
$stmt->close();

// Обработка удаления комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment']) && $is_admin) {
    $comment_id = intval($_POST['delete_comment']);
    $delete_stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $delete_stmt->bind_param("i", $comment_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    
    header("Location: article.php?id=$article_id");
    exit;
}

// Обработка отправки комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_content'])) {
    if (!isset($_SESSION['user_id'])) {
        die('Доступ запрещен');
    }
    
    $content = trim($_POST['comment_content']);
    
    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO comments (article_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $article_id, $_SESSION['user_id'], $content);
        $stmt->execute();
        $stmt->close();
        
        header("Location: article.php?id=$article_id");
        exit;
    }
}

// Получение комментариев
$comments_stmt = $conn->prepare("
    SELECT c.*, u.username 
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.article_id = ?
    ORDER BY c.created_at DESC
");
$comments_stmt->bind_param("i", $article_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
$comments = $comments_result->fetch_all(MYSQLI_ASSOC);
$comments_stmt->close();
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

        <section class="comments-section">
            <h2>Комментарии</h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="comment-form">
                    <h3>Оставить комментарий</h3>
                    <form method="POST">
                        <div class="form-group">
                            <textarea name="comment_content" placeholder="Ваш комментарий..." required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Отправить</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="auth-required">
                    <p>Чтобы оставить комментарий, пожалуйста <a href="enter.html">войдите</a>.</p>
                </div>
            <?php endif; ?>
            
            <div class="comments-list">
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-author">
                                <span class="comment-name">
                                    <?= htmlspecialchars($comment['username'] ?? $comment['author_name']) ?>
                                </span>
                                <span class="comment-date">
                                    <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                                </span>
                                <?php if ($is_admin): ?>
                                    <form method="POST" class="delete-comment-form" onsubmit="return confirm('Вы уверены, что хотите удалить этот комментарий?');">
                                        <input type="hidden" name="delete_comment" value="<?= $comment['id'] ?>">
                                        <button type="submit" class="delete-btn">Удалить</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="comment-content">
                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Пока нет комментариев. Будьте первым!</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>