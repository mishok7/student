<?php
include 'db_connect.php'; // Подключение к базе данных

$username = "";
$email = "";
$errors = array(); 

if (isset($_POST['reg_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

    if (empty($username)) { array_push($errors, "Логин не введен"); }
    if (empty($email)) { array_push($errors, "Почта не введена"); }
    if (empty($password_1)) { array_push($errors, "Пароль не введен"); }
    if ($password_1 != $password_2) {
        array_push($errors, "Пароли не совпадают");
    }

    $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) {
        if ($user['username'] === $username) {
            array_push($errors, "Логин уже занят");
        }
        if ($user['email'] === $email) {
            array_push($errors, "Почта уже занята");
        }
    }

    if (count($errors) == 0) {
      $salt = '2189507634'; // Используем соль для хеширования
      $password = md5($salt . $password_1); // Хешируем пароль
      $query = "INSERT INTO users (username, password, role, salt, email) VALUES('$username', '$password', 'Пользователь', '$salt', '$email')";
      mysqli_query($db, $query);
      $_SESSION['username'] = $username; // Устанавливаем сессионную переменную
      $_SESSION['success'] = "You are now logged in";
      header('location: index.php');
      exit(); // Добавление exit() после header для предотвращения дальнейшего выполнения скрипта
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Регистрация</title>
  <style>
      * {
  margin: 0px;
  padding: 0px;
  color: black;
}

body {
  font-size: 120%;
  background-size: 100%;
}

.header {
  width: 30%;
  margin: 14vh auto 0px;
  background: #C0C0C0;
  text-align: center;
  border: 2px solid #C0C0C0;
  border-bottom: none;
  border-radius: 10px 10px 0px 0px;
  padding: 20px;
}

form,
.content {
  width: 30%;
  margin: 0px auto;
  padding: 20px;
  border: 2px solid #C0C0C0;
  background: white;
  border-radius: 0px 0px 10px 10px;
}

.input-group {
  margin: 10px 0px 10px 0px;
}

.input-group label {
  display: block;
  text-align: left;
  margin: 3px;
}

.input-group input {
  height: 30px;
  width: 93%;
  padding: 5px 10px;
  font-size: 16px;
  border-radius: 5px;
  border: 1px solid gray;
}

.btn {
  height: 30px;
  width: 150px;
  border: 1px solid #c0c0c0;
  
}

.error {
  width: 92%;
  margin: 0px auto;
  padding: 10px;
  border: 1px solid #a94442;
  color: #a94442;
  background: #f2dede;
  border-radius: 5px;
  text-align: left;
}

.success {
  color: #3c763d;
  background: #dff0d8;
  border: 1px solid #3c763d;
  margin-bottom: 20px;
}
  </style>
</head>
<body>
  <div class="header">
        <h2>Регистрация</h2>
  </div>
         
  <form method="post" action="register.php">
        <?php include('errors.php'); ?>
        <div class="input-group">
                <label>Логин</label>
                <input type="text" name="username" >
        </div>
        <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" >
        </div>
        <div class="input-group">
                <label>Пароль</label>
                <input type="password" name="password_1">
        </div>
        <div class="input-group">
                <label>Подтверждение пароля</label>
                <input type="password" name="password_2">
        </div>
        <div class="input-group">
                <button type="submit" class="btn" name="reg_user">Зарегистрироваться</button>
        </div>
        <p>
                Уже есть аккаунт? <a href="login.php">Войдите!</a>
        </p>
  </form>
</body>
</html>