<?php
include 'db_connect.php'; // Подключение к базе данных

$errors = array(); 

if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
  
    if (empty($username)) {
        array_push($errors, "Логие не введен");
    }
    if (empty($password)) {
        array_push($errors, "Пароль не введен");
    }
  
    if (count($errors) == 0) {
        $salt = '2189507634'; // Используем соль для хеширования
        $password = md5($salt . $password); // Хешируем пароль с солью
        $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $results = mysqli_query($db, $query);
        if (mysqli_num_rows($results) == 1) {
            $_SESSION['username'] = $username;
            $_SESSION['success'] = "Вы в аккакунте";
            header('location: index.php');
        } else {
            array_push($errors, "Неверный логин или пароль");
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Вход</title>
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
  margin: 21vh auto 0px;
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
  width: 60px;
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
        <h2>Войти</h2>
  </div>
         
  <form method="post" action="login.php">
        <?php include('errors.php'); ?>
        <div class="input-group">
                <label>Логин</label>
                <input type="text" name="username" >
        </div>
        <div class="input-group">
                <label>Пароль</label>
                <input type="password" name="password">
        </div>
        <div class="input-group">
                <button type="submit" class="btn" name="login_user">Войти</button>
        </div>
        <p>
                Нет аккаунта? <a href="register.php">Зарегистрируйтесь!</a>
        </p>
  </form>
</body>
</html>