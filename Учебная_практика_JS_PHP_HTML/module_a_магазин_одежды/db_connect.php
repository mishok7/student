<?php
$db = mysqli_connect('localhost', 'root', '', 'fashion');

// Проверка подключения
if (!$db) {
    die("Ошибка подключения: " . mysqli_connect_error());
}
?>