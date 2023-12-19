<?php
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
        session_start();
        $selected_user = $conn->real_escape_string($_POST["stuff_user"]);
        $entered_password = $conn->real_escape_string($_POST["password"]);

        // Выполнение запроса к базе данных для получения пароля выбранного пользователя
        $password_query = "SELECT password, jobTitle FROM Stuff WHERE name = '$selected_user' LIMIT 1";
        $password_result = $conn->query($password_query);

        if ($password_result->num_rows == 1) {
            $row = $password_result->fetch_assoc();
            $stored_password = $row["password"];
            echo $stored_password;
            echo $entered_password;
            echo password_verify($entered_password, $stored_password);
            $jobTitle = $row["jobTitle"];

            // Проверка введенного пароля
            if (hash_equals($entered_password, $stored_password)) {
                // Освобождение результата запроса и закрытие соединения с базой данных
                $password_result->free();
                $conn->close();

                // Начало сессии и сохранение имени сотрудника
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
                echo "Неверный пароль";
            }
        } else {
            echo "Пользователь не найден";
        }
    }
}
?>
