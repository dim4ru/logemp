<?php
session_start();
if (isset($_SESSION['tel'])) {
    $tel = $_SESSION['tel'];
    echo "<h2>Личный кабинет клиента по номеру $tel</h2>";
} else {
    // Сессия не содержит информацию о пользователе
    // Можно выполнить другие действия, например, перенаправить пользователя на страницу входа
    header("Location: client_login.php");
}