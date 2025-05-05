<?php
session_start();
if (isset($_SESSION['user_id'])) {
    echo '<button class="subscribe-btn" onclick="document.location=\'profile.php\'">Личный кабинет</button>';
} else {
    echo '<button class="subscribe-btn" onclick="document.location=\'enter.html\'">Войти</button>';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статья - SportNews</title>
    <link rel = "stylesheet" href="test-main.css">
</head>
<div class="img">
<body>
    <header>
        <div class="header-container">
            <div class="first-panel">
                <nav>
                    <ul>
                        <li><a href="#">Главная</a></li>
                        <li><a href="#">Футбол</a></li>
                        <li><a href="#">Хоккей</a></li>
                        <li><a href="#">Баскетбол</a></li>
                        <li><a href="#">Теннис</a></li>
                        <li><a href="#">Другие виды</a></li>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" class="search-input" placeholder="Поиск статей">
                        <button class="search-button">🔍</button>
                    </div>
                    
                </div>
                <button class="mobile-menu-btn">☰</button>
            </div>
            <div class="logo">
                <img src="https://via.placeholder.com/40x40" alt="SportNews Logo">
                <div class="logo-text">Sport<span>News</span></div>
                <?php
                session_start();
                if (isset($_SESSION['user_id'])) {
                    echo '<button class="subscribe-btn" onclick="document.location=\'profile.php\'">Личный кабинет</button>';
                } else {
                    echo '<button class="subscribe-btn" onclick="document.location=\'enter.html\'">Войти</button>';
                }
                ?>
            </div>
            
            
            
            
        </div>
    </header>

    

        <div class="article-preview">
            <img src="https://via.placeholder.com/800x400" alt="Футбольный матч" class="article-image">
            <div class="article-content">
                <span class="article-category">Футбол</span>
                <h2 class="article-title">Решающий матч чемпионата: обзор противостояния</h2>
                <p class="article-excerpt">
                    Вчера состоялся матч, который определил чемпиона этого сезона. В напряженной борьбе 
                    команды показали все свое мастерство. Первый тайм прошел с преимуществом гостей, 
                    но после перерыва хозяева смогли переломить ход игры. Особенно отличился нападающий...
                </p>
                <div class="article-meta">
                    <span class="article-date">15 апреля 2023</span>
                    <span class="article-author">Иван Спортивный</span>
                </div>
                <a href="#" class="read-more">Читать далее →</a>
            </div>
        </div>

        <div class="article-preview">
            <img src="https://via.placeholder.com/800x400" alt="Футбольный матч" class="article-image">
            <div class="article-content">
                <span class="article-category">Футбол</span>
                <h2 class="article-title">Решающий матч чемпионата: обзор противостояния</h2>
                <p class="article-excerpt">
                    Вчера состоялся матч, который определил чемпиона этого сезона. В напряженной борьбе 
                    команды показали все свое мастерство. Первый тайм прошел с преимуществом гостей, 
                    но после перерыва хозяева смогли переломить ход игры. Особенно отличился нападающий...
                </p>
                <div class="article-meta">
                    <span class="article-date">15 апреля 2023</span>
                    <span class="article-author">Иван Спортивный</span>
                </div>
                <a href="#" class="read-more">Читать далее →</a>
            </div>
        </div>

        <div class="article-preview">
            <img src="https://via.placeholder.com/800x400" alt="Футбольный матч" class="article-image">
            <div class="article-content">
                <span class="article-category">Футбол</span>
                <h2 class="article-title">Решающий матч чемпионата: обзор противостояния</h2>
                <p class="article-excerpt">
                    Вчера состоялся матч, который определил чемпиона этого сезона. В напряженной борьбе 
                    команды показали все свое мастерство. Первый тайм прошел с преимуществом гостей, 
                    но после перерыва хозяева смогли переломить ход игры. Особенно отличился нападающий...
                </p>
                <div class="article-meta">
                    <span class="article-date">15 апреля 2023</span>
                    <span class="article-author">Иван Спортивный</span>
                </div>
                <a href="#" class="read-more">Читать далее →</a>
            </div>
        </div>

        <div class="article-preview">
            <img src="https://via.placeholder.com/800x400" alt="Футбольный матч" class="article-image">
            <div class="article-content">
                <span class="article-category">Футбол</span>
                <h2 class="article-title">Решающий матч чемпионата: обзор противостояния</h2>
                <p class="article-excerpt">
                    Вчера состоялся матч, который определил чемпиона этого сезона. В напряженной борьбе 
                    команды показали все свое мастерство. Первый тайм прошел с преимуществом гостей, 
                    но после перерыва хозяева смогли переломить ход игры. Особенно отличился нападающий...
                </p>
                <div class="article-meta">
                    <span class="article-date">15 апреля 2023</span>
                    <span class="article-author">Иван Спортивный</span>
                </div>
                <a href="#" class="read-more">Читать далее →</a>
            </div>
        </div>
    
    <footer>
        <div class="footer-container">
            <div class="footer-about">
                <div class="footer-logo">
                    <img src="https://via.placeholder.com/40x40" alt="SportNews Logo">
                    <div class="footer-logo-text">Sport<span>News</span></div>
                </div>
                <p class="footer-description">
                    Самые свежие и актуальные новости спортивного мира. Эксклюзивные интервью, аналитика и прямые трансляции.
                </p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook">📘</a>
                    <a href="#" aria-label="Twitter">🐦</a>
                    <a href="#" aria-label="Instagram">📷</a>
                    <a href="#" aria-label="YouTube">▶️</a>
                </div>
            </div>
            
            <div class="footer-column">
                <h3>Разделы</h3>
                <ul class="footer-links">
                    <li><a href="#">Футбол</a></li>
                    <li><a href="#">Хоккей</a></li>
                    <li><a href="#">Баскетбол</a></li>
                    <li><a href="#">Теннис</a></li>
                    <li><a href="#">Олимпийские виды</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Компания</h3>
                <ul class="footer-links">
                    <li><a href="#">О нас</a></li>
                    <li><a href="#">Контакты</a></li>
                    <li><a href="#">Вакансии</a></li>
                    <li><a href="#">Реклама</a></li>
                    <li><a href="#">Партнеры</a></li>
                </ul>
            </div>
            

        
        <div class="footer-bottom">
            <p>&copy; 2023 SportNews. Все права защищены. | <a href="#" style="color: var(--gray-color);">Политика конфиденциальности</a> | <a href="#" style="color: var(--gray-color);">Условия использования</a></p>
        </div>
    </footer>

</body>
</div>
</html>