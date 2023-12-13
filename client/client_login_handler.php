<?php
include "../connection.php";

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Начинаем сессию или возобновляем существующую
session_start();

// Проверяем, были ли переданы данные из формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Здесь должна быть логика проверки логина и пароля
    $tel = $_POST['tel'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Clients WHERE phoneNumber = '$tel' AND password = '$password'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $tel, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Проход по результатам выборки
        while ($row = $result->fetch_assoc()) {
            // Здесь вы можете обращаться к данным, например:
            echo "ID: " . $row["id"] . " - Name: " . $row["name"] . "<br>";
        }
        // Предположим, что вы проверили логин и пароль и они совпадают
        // Сохраняем пользователя в сессии
        $_SESSION['tel'] = $tel;
        // Другие действия после успешной авторизации, например, перенаправление на другую страницу
        header("Location: client_cabinet.php");
        exit();
    } else {
        echo "<h1>Неверный логин и/или пароль</h1>";
    }
    // Закрытие соединения с базой данных
    $conn->close();
} else {
    header("Location: client_login.php");
}