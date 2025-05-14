<?php
// search_ajax.php
require 'config.php';

header('Content-Type: text/html; charset=utf-8');

if (isset($_GET['search_query'])) {
    $searchTerm = trim($_GET['search_query']);
    
    if (!empty($searchTerm)) {
        $conn = getDBConnection();
        
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
        
        while ($row = $result->fetch_assoc()): ?>
            <a href="article.php?id=<?= $row['id'] ?>" class="search-result-item">
                <?php if ($row['image_path']): ?>
                    <img src="<?= htmlspecialchars($row['image_path']) ?>" width="40">
                <?php endif; ?>
                <span><?= htmlspecialchars($row['title']) ?></span>
            </a>
        <?php endwhile;
        
        $stmt->close();
        $conn->close();
    }
}
?>