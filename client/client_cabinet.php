<?php
session_start();
if (!isset($_SESSION['tel'])) {
    // Сессия не содержит информацию о пользователе
    header("Location: client_login.php");
} else {
    $tel = $_SESSION['tel'];
    echo "<h2>Личный кабинет клиента по номеру $tel</h2>";


}