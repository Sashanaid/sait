<?php
$conn = mysqli_connect("127.0.0.1", "root", "root", "new_schema", 3306);
if (!$conn) {
    die("Ошибка MySQLi: " . mysqli_connect_error());
}
echo "✅ Успешное подключение через MySQLi!";
mysqli_close($conn);