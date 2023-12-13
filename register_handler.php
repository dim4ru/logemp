<?php
// Параметры подключения к базе данных
$servername = "127.0.0.1"; // Имя сервера базы данных
$dbusername = "root"; // Имя пользователя базы данных
$dbpassword = ""; // Пароль пользователя базы данных
$dbname = "logemp"; // Имя вашей базы данных

// Создание подключения
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Обработка отправленной формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $tel = $_POST['tel'];
    printf($username);

    // SQL запрос для вставки данных в базу данных
    $sql = "INSERT INTO Clients (id, name, phoneNumber, login, password) VALUES (NULL, '$name', '$tel', '$username', '$password')";

    // Выполнение запроса
    if ($conn->query($sql) === TRUE) {
        echo "Регистрация прошла успешно";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }
}

// Закрытие соединения с базой данных
$conn->close();