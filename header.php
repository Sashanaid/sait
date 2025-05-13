
<header>
        <div class="header-container">
            <div class="first-panel">
                <nav>
                    <ul>
                        <li><a href="test-main.php">Главная</a></li>
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
                if (isset($_SESSION['user_id'])) {
                    echo '<button class="subscribe-btn" onclick="document.location=\'profile.php\'">Личный кабинет</button>';
                } else {
                    echo '<button class="subscribe-btn" onclick="document.location=\'enter.html\'">Войти</button>';
                }
                ?>
            </div>
            
            
            
            
        </div>
    </header>
