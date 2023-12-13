<?php
include "../connection.php";

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Обработка отправленной формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $password = $_POST['password'];
    $name = $_POST['name'];
    $tel = $_POST['tel'];

    // SQL запрос для вставки данных в базу данных
    $sql = "INSERT INTO Clients (id, name, phoneNumber, password) VALUES (NULL, '$name', '$tel', '$password')";

    // Выполнение запроса
    if ($conn->query($sql) === TRUE) {
        echo "Регистрация прошла успешно. Теперь вы можете авторизоваться.";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }
}

// Закрытие соединения с базой данных
$conn->close();