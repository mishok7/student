<?php
   include 'db_connect.php'; // Подключение к базе данных

   session_start();
   ?>

   <!DOCTYPE html>
   <html lang="ru">
   <head>
       <meta charset="UTF-8">
       <title>Главная страница</title>
       <link rel="stylesheet" href="style.css">
   </head>
   
   
   
   <body>
   <header class="header">
        <div class="header__inner">
            <div><img width="120" src="logo.png" alt=""></div>
            <nav class="header__nav nav">
                <ul class="first_menu">
                    <li><a class="btn" href="http://localhost/practica/login.php"><b>Выйти</b></a></li>
                    

                </ul>
            </nav>
        </div>
        <div class="header__intro">
            <h1>Добро пожаловать!</h1>
        </div>
    </header>

	


   </body>
   </html>