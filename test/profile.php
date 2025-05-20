<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: enter.html');
    exit;
}

require_once 'config.php';
$conn = getDBConnection();

// Получаем данные пользователя
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль | SportNews</title>
    <style>
        @import "header.css";
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f3f4f6;
        }
        .profile-container {
            max-width: 800px;
            margin: 10px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #1e3a8a;
            margin-bottom: 20px;
        }
        .profile-info {
            margin-bottom: 20px;
        }
        .profile-info p {
            margin: 10px 0;
        }
        .logout-btn, .admin-btn {
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .logout-btn {
            background-color: #dc2626;
        }
        .logout-btn:hover {
            background-color: #b91c1c;
        }
        .admin-btn {
            background-color: #1e3a8a;
        }
        .admin-btn:hover {
            background-color: #1e40af;
        }
    </style>
</head>
<body>
    <?php include ("header.php");?>
    <div class="profile-container">
        <h1>Ваш профиль</h1>
        
        <div class="profile-info">
            <p><strong>Имя пользователя:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <div class="button-group">
            <button class="logout-btn" onclick="location.href='logout.php'">Выйти</button>
            
            <?php if ($_SESSION['user_id'] == 1): ?>
                <button class="admin-btn" onclick="location.href='admin.php'">Админ-панель</button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>