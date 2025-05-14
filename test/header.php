<?php
// header.php



// Обработка поискового запроса
$searchResults = [];
if (isset($_GET['search_query'])) {
    $searchTerm = trim($_GET['search_query']);
    
    if (!empty($searchTerm)) {
        $conn = getDBConnection();
        
        // Подготовленный запрос для защиты от SQL-инъекций
        $stmt = $conn->prepare("
            SELECT id, title, image_path 
            FROM articles 
            WHERE title LIKE ? 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        
        $searchParam = "%" . $searchTerm . "%";
        $stmt->bind_param("s", $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<header>
    <div class="header-container">
        <div class="first-panel">
            <nav>
                <ul>
                    <li><a href="test-main.php">Главная</a></li>
                    <li><a href="test-main.php?category=1">Футбол</a></li>
                    <li><a href="test-main.php?category=2">Хоккей</a></li>
                    <li><a href="test-main.php?category=3">Теннис</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <form method="GET" action="search.php" class="search-form">
                    <div class="search-bar">
                        <input type="text" 
                               name="search_query" 
                               class="search-input" 
                               placeholder="Поиск статей"
                               value="<?= isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : '' ?>">
                        <button type="submit" class="search-button">🔍</button>
                    </div>
                    
                    <!-- Выпадающие результаты поиска (AJAX-версия) -->
                    <div class="search-results" id="searchResults">
                        <?php if (!empty($searchResults)): ?>
                            <?php foreach ($searchResults as $article): ?>
                                <a href="article.php?id=<?= $article['id'] ?>" class="search-result-item">
                                    <?php if ($article['image_path']): ?>
                                        <img src="<?= htmlspecialchars($article['image_path']) ?>" width="40">
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($article['title']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <button class="mobile-menu-btn">☰</button>
        </div>
        
        <div class="logo">
            <img src="https://via.placeholder.com/40x40" alt="SportNews Logo">
            <div class="logo-text">Sport<span>News</span></div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="subscribe-btn" onclick="document.location='profile.php'">Личный кабинет</button>
            <?php else: ?>
                <button class="subscribe-btn" onclick="document.location='enter.html'">Войти</button>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Подключение JavaScript для AJAX-поиска -->
<script>
document.querySelector('.search-input').addEventListener('input', function(e) {
    const query = e.target.value.trim();
    const resultsContainer = document.getElementById('searchResults');
    
    if (query.length < 2) {
        resultsContainer.style.display = 'none';
        return;
    }
    
    fetch(`search_ajax.php?search_query=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(html => {
            resultsContainer.innerHTML = html;
            resultsContainer.style.display = html.trim() ? 'block' : 'none';
        });
});
</script>