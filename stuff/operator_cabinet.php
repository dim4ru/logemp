<?php
session_start();
// Проверяем, активна ли сессия
if (!isset($_SESSION['user'])) {
    // Сессия не содержит информацию о пользователе
    header("Location: ../index.php");
}
$name = $_SESSION['user'];
echo "<h1>Личный кабинет оператора отделения $name</h1>";