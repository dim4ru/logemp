<?php
// Подключение к базе данных
include "../connection.php";
if ($conn->connect_error) {
    die("Ошибка соединения: " . $conn->connect_error);
}

if (!$_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Неверный метод отправки формы";
} else {
    if (!isset($_POST["stuff_user"]) && $_POST["stuff_user"] != "NULL") {
        echo "Вы не выбрали пользователя";
    } else {
        $selected_user = $conn->real_escape_string($_POST["stuff_user"]);

        // Выполнение запроса к базе данных
        $query = "SELECT jobTitle FROM Stuff WHERE name = '$selected_user' LIMIT 1";
        $result = $conn->query($query);

        if ($result) {
            // Обработка результата запроса
            $row = $result->fetch_assoc();
            $jobTitle = $row["jobTitle"];

            // Освобождение результата запроса и закрытие соединения с базой данных
            $result->free();
            $conn->close();

            // Начало сессии и сохранение имени сотрудника
            session_start();
            $_SESSION["user"] = $selected_user;

            // Перенаправление в зависимости от должности
            if ($jobTitle === "Курьер") {
                header("Location: courier_cabinet.php");
                exit;
            } elseif ($jobTitle === "Оператор отделения") {
                header("Location: operator_cabinet.php");
                exit;
            } elseif ($jobTitle === "Экспедитор") {
                header("Location: driver_cabinet.php");
                exit;
            } else {
                echo "Должность не определена";
            }
        } else {
            echo "Ошибка выполнения запроса: " . $conn->error;
        }
    }
}
?>
