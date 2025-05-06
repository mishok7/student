<?php
include 'db_connect.php'; // Подключение к базе данных

session_start(); // Начало сессии

// Инициализация переменных
$username = ""; // Имя пользователя
$email    = ""; // Электронная почта
$errors = array(); // Массив для хранения ошибок

// Подключение к базе данных
$db = mysqli_connect('localhost', 'root', '', 'db_great_museums_async');

// РЕГИСТРАЦИЯ ПОЛЬЗОВАТЕЛЯ
if (isset($_POST['reg_user'])) {
  // Получение всех значений ввода из формы
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // Валидация формы: убедитесь, что форма заполнена правильно ...
  // добавляя (array_push()) соответствующие ошибки в массив $errors
  if (empty($username)) { array_push($errors, "Имя пользователя обязательно"); }
  if (empty($email)) { array_push($errors, "Электронная почта обязательна"); }
  if (empty($password_1)) { array_push($errors, "Пароль обязателен"); }
  if ($password_1 != $password_2) {
        array_push($errors, "Пароли не совпадают");
  }

  // Сначала проверяем базу данных, чтобы убедиться, что 
  // пользователь с таким же именем пользователя и/или электронной почтой не существует
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // если пользователь существует
    if ($user['username'] === $username) {
      array_push($errors, "Имя пользователя уже существует");
    }

    if ($user['email'] === $email) {
      array_push($errors, "Электронная почта уже существует");
    }
  }

  // Наконец, регистрируем пользователя, если в форме нет ошибок
  if (count($errors) == 0) {
        $password = md5($password_1); // Шифруем пароль перед сохранением в базе данных

        $query = "INSERT INTO users (username, email, password) 
                          VALUES('$username', '$email', '$password')";
        mysqli_query($db, $query);
        $_SESSION['username'] = $username;
        $_SESSION['success'] = "Вы успешно вошли";
        header('location: index.php'); // Перенаправление на главную страницу
  }
}

// ... 

// ВХОД ПОЛЬЗОВАТЕЛЯ
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
  
    if (empty($username)) {
          array_push($errors, "Имя пользователя обязательно");
    }
    if (empty($password)) {
          array_push($errors, "Пароль обязателен");
    }
  
    if (count($errors) == 0) {
          $password = md5($password); // Шифруем пароль
          $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
          $results = mysqli_query($db, $query);
          if (mysqli_num_rows($results) == 1) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = "Вы успешно вошли";
            header('location: index.php'); // Перенаправление на главную страницу
          } else {
                  array_push($errors, "Неверное имя пользователя/пароль");
          }
    }
}
?>