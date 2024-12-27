<?php
$db = mysqli_connect('localhost', 'root', '', 'db_great_museums_async');

// Проверка подключения
if (!$db) {
    die("Ошибка подключения: " . mysqli_connect_error());
}
?>