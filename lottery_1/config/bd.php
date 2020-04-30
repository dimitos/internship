<?php
$host = '127.0.0.1';         // адрес сервера
$database = '';              // имя базы данных
$user = 'root';              // имя пользователя
$password = '';              // пароль
$link = new mysqli($host, $user, $password, $database)
or die("Ошибка " . mysqli_error($link));
//echo "Соединение с MySQL установлено!" . '<br>';
//echo "Информация о сервере: " . mysqli_get_host_info($link) . '<br>';