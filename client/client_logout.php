<?php
// Инициализация сессии
session_start();

// Удаление всех переменных сессии
session_unset();

// Завершение сессии
session_destroy();

// Перенаправление пользователя на другую страницу или вывод сообщения о успешном выходе
// Например:
header("Location: ../index.php");
exit;