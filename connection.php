<?php
// Параметры подключения к базе данных
$servername = "127.0.0.1"; // Имя сервера базы данных
$dbusername = "root"; // Имя пользователя базы данных
$dbpassword = ""; // Пароль пользователя базы данных
$dbname = "logemp"; // Имя вашей базы данных

// Создание подключения
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);