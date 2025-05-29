<?php 
session_start();
require 'config.php';

// Получаем ID категории из URL, если есть
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;

// Настройки пагинации
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$articles_per_page = 5;
$offset = ($current_page - 1) * $articles_per_page;

// Базовый запрос для статей
$query = "SELECT 
            a.id, 
            a.title, 
            a.content, 
            a.image_path,
            a.created_at,
            a.category_id,
            c.name AS category_name
          FROM articles a
          LEFT JOIN categories c ON a.category_id = c.id";

// Запрос для подсчета общего количества статей
$count_query = "SELECT COUNT(*) as total FROM articles a";

// Добавляем условие фильтрации, если выбрана категория
if ($category_id) {
    $query .= " WHERE a.category_id = " . $category_id;
    $count_query .= " WHERE a.category_id = " . $category_id;
}

// Получаем общее количество статей
$conn = getDBConnection();
$count_result = $conn->query($count_query);
$total_articles = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_articles / $articles_per_page);

// Добавляем сортировку и пагинацию к основному запросу
$query .= " ORDER BY a.created_at DESC LIMIT $offset, $articles_per_page";

// Получаем статьи для текущей страницы
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
    <title>
        <?= $category_id && !empty($articles) ? htmlspecialchars($articles[0]['category_name']).' - ' : '' ?>Статьи - SportNews
    </title>
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
    </style>
</head>
<body>
    <div class="img">
        <?php include("header.php"); ?>
        
        <div class="content-wrapper">
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
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?= $current_page-1 ?><?= $category_id ? '&category='.$category_id : '' ?>" class="page-link">← Назад</a>
                    <?php endif; ?>
                    
                    <?php 
                    // Показываем до 5 страниц вокруг текущей
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    if ($start_page > 1) {
                        echo '<a href="?page=1'.($category_id ? '&category='.$category_id : '').'" class="page-link">1</a>';
                        if ($start_page > 2) echo '<span class="page-link">...</span>';
                    }
                    
                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="?page=<?= $i ?><?= $category_id ? '&category='.$category_id : '' ?>" 
                           class="page-link <?= $i == $current_page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; 
                    
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) echo '<span class="page-link">...</span>';
                        echo '<a href="?page='.$total_pages.($category_id ? '&category='.$category_id : '').'" class="page-link">'.$total_pages.'</a>';
                    }
                    ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?= $current_page+1 ?><?= $category_id ? '&category='.$category_id : '' ?>" class="page-link">Вперед →</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-articles">
                    <p>Статьи не найдены. Попробуйте позже.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include("footer.php"); ?>
</body>
</html>