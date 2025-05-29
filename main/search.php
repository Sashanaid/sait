<?php
// search.php
session_start();
require 'config.php';

$searchTerm = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';

// Настройки пагинации
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$articles_per_page = 5;
$offset = ($current_page - 1) * $articles_per_page;

$articles = [];
$total_articles = 0;
$total_pages = 0;

if (!empty($searchTerm)) {
    $conn = getDBConnection();
    
    // Базовый запрос для поиска статей
    $query = "
        SELECT 
            a.id, 
            a.title, 
            a.content, 
            a.image_path, 
            a.created_at,
            a.category_id,
            c.name AS category_name
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.title LIKE ?
        ORDER BY a.created_at DESC
        LIMIT ?, ?
    ";
    
    // Запрос для подсчета общего количества найденных статей
    $count_query = "
        SELECT COUNT(*) as total 
        FROM articles a
        WHERE a.title LIKE ?
    ";
    
    $stmt = $conn->prepare($query);
    $count_stmt = $conn->prepare($count_query);
    
    $searchParam = "%" . $searchTerm . "%";
    
    // Получаем общее количество статей
    $count_stmt->bind_param("s", $searchParam);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_articles = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_articles / $articles_per_page);
    $count_stmt->close();
    
    // Получаем статьи для текущей страницы
    $stmt->bind_param("sii", $searchParam, $offset, $articles_per_page);
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
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin: 30px 0;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .page-link {
            padding: 8px 16px;
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            color: #1e3a8a;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .page-link:hover {
            background-color: #1e3a8a;
            color: white;
            border-color: #1e3a8a;
        }
        
        .page-link.active {
            background-color: #1e3a8a;
            color: white;
            border-color: #1e3a8a;
            font-weight: bold;
        }
        
        .no-articles {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 8px;
            max-width: 800px;
            margin: 30px auto;
        }
        
        .search-results-page {
            padding-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include "header.php"?>
    <main class="search-results-page">
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
            
            <!-- Пагинация -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="?search_query=<?= urlencode($searchTerm) ?>&page=<?= $current_page-1 ?>" class="page-link">← Назад</a>
                    <?php endif; ?>
                    
                    <?php 
                    // Показываем до 5 страниц вокруг текущей
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    if ($start_page > 1) {
                        echo '<a href="?search_query='.urlencode($searchTerm).'&page=1" class="page-link">1</a>';
                        if ($start_page > 2) echo '<span class="page-link">...</span>';
                    }
                    
                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="?search_query=<?= urlencode($searchTerm) ?>&page=<?= $i ?>" 
                           class="page-link <?= $i == $current_page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; 
                    
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) echo '<span class="page-link">...</span>';
                        echo '<a href="?search_query='.urlencode($searchTerm).'&page='.$total_pages.'" class="page-link">'.$total_pages.'</a>';
                    }
                    ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?search_query=<?= urlencode($searchTerm) ?>&page=<?= $current_page+1 ?>" class="page-link">Вперед →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-articles">
                <p><?= empty($searchTerm) ? 'Введите поисковый запрос' : 'Статьи не найдены. Попробуйте изменить поисковый запрос.' ?></p>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>