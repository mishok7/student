<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root"; 
$password = "";  
$dbname = "db_great_museums_async";

// Создание соединения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    echo json_encode(['error' => 'Ошибка подключения: ' . $conn->connect_error]);
    exit();
}

// Запрос к базе данных для получения всего контента
$sql = "SELECT title, text, image, link FROM publication"; // Измените запрос в зависимости от вашей структуры
$result = $conn->query($sql);

$content = array();
if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $content[] = $row; // Получаем все записи
        }
    }
} else {
    echo json_encode(['error' => 'Ошибка выполнения запроса: ' . $conn->error]);
    exit();
}

header('Content-Type: application/json');
echo json_encode($content);

$conn->close();
?>