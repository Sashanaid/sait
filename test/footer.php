<style>
    footer {
    background-color: var(--dark-color);
    color: white;
    padding: 60px 0 30px;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.footer-logo img {
    height: 40px;
}

.footer-logo-text {
    font-size: 24px;
    font-weight: 700;
    color: white;
}

.footer-logo-text span {
    color: var(--secondary-color);
}

.footer-description {
    color: var(--gray-color);
    margin-bottom: 20px;
    line-height: 1.6;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-links a {
    color: white;
    font-size: 20px;
    transition: color 0.3s;
}

.social-links a:hover {
    color: var(--secondary-color);
}

.footer-column h3 {
    font-size: 18px;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 10px;
}

.footer-column h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 2px;
    background-color: var(--secondary-color);
}

.footer-links {
    list-style: none;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: var(--gray-color);
    text-decoration: none;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: white;
}

.footer-newsletter input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: none;
    border-radius: 4px;
    background-color: #1f2937;
    color: white;
}

.footer-newsletter button {
    background-color: var(--secondary-color);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
    width: 100%;
}

.footer-newsletter button:hover {
    background-color: #b91c1c;
}

.footer-bottom {
    max-width: 1200px;
    margin: 50px auto 0;
    padding: 0 20px;
    text-align: center;
    color: var(--gray-color);
    font-size: 14px;
    border-top: 1px solid #374151;
    padding-top: 20px;
}

@media (max-width: 768px) {
    .footer-container {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .footer-column h3 {
        margin-bottom: 15px;
    }
}
</style>
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
                <a href="#" aria-label="YouTube">▶️</a>
            </div>
        </div>
        
        <div class="footer-column">
            <h3>Разделы</h3>
            <ul class="footer-links">
                <li><a href="#">Футбол</a></li>
                <li><a href="#">Хоккей</a></li>
                <li><a href="#">Теннис</a></li>
            </ul>
        </div>
        
        <div class="footer-column">
            <h3>Компания</h3>
            <ul class="footer-links">
                <li><a href="#">О нас</a></li>
                <li><a href="#">Контакты</a></li>
                <li><a href="#">Реклама</a></li>
                <li><a href="#">Партнеры</a></li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> SportNews. Все права защищены. | 
           <a href="#" style="color: var(--gray-color);">Политика конфиденциальности</a> | 
           <a href="#" style="color: var(--gray-color);">Условия использования</a></p>
    </div>
</footer>