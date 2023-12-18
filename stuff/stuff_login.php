<!DOCTYPE html>
<html>
<head>
    <title>Авторизация сотрудника</title>
</head>
<body>
<a href="../stuff_logout.php" style="position: absolute; top: 10px; right: 10px;">Выход</a>

<form action="stuff_login_handler.php" method="post">
    <div>
        <label for="stuff_user">Выберите пользователя:</label>
        <select id="stuff_user" name="stuff_user">
            <option value="NULL">Не выбрано</option>
            <?php
            include "../connection.php";
            session_destroy();
            // Проверяем соединение
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Выполняем запрос к базе данных, чтобы получить имена сотрудников
            $sql = "SELECT name FROM Stuff ORDER BY name ASC";
            $result = $conn->query($sql);

            // Если есть хотя бы одна строка в результате
            if ($result->num_rows > 0) {
                // Выводим данные каждой строки результата
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=\"" . $row["name"] . "\">" . $row["name"] . "</option>";
                }
            } else {
                echo "Сотрудников не найдено";
            }

            // Закрываем соединение с базой данных
            $conn->close();

            ?>
        </select>
    </div>
    <button type="submit">Войти</button>
</form>

</body>
</html>