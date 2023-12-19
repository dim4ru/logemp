<?php
include "../connection.php";

// Стартуем сессию
session_start();

// Получаем значение переменной $name из сессии
if(isset($_SESSION['user'])) {
    $name = $_SESSION['user']; // $name содержит имя пользователя
} else {
    // Обработка случая, если переменная $name не была установлена
    // Может потребоваться перенаправить пользователя обратно на страницу входа
}
// Проверяем, был ли отправлен POST запрос
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем введенные пользователем ID посылок
    $input = $_POST['loadInput'];

    // Разделяем строку по пробелам, чтобы получить массив значений
    $parcelIds = explode(" ", $input);

    // Далее для каждого значения в массиве выполняем запрос INSERT INTO
    foreach ($parcelIds as $parcelId) {
        // Выполняем SQL запрос для вставки
        $insertSql = "INSERT INTO car_load(car_id, parcel_id) VALUES ((SELECT car_id FROM Stuff WHERE name = '$name'), '$parcelId')";

        // Выполняем запрос вставки
        if ($conn->query($insertSql) === TRUE) {
            // Запрос INSERT выполнен успешно, теперь выполним запрос UPDATE
            $updateSql = "UPDATE Parcels SET status='В пути' WHERE id = '$parcelId'";

            // Выполняем запрос UPDATE
            if ($conn->query($updateSql) === TRUE) {
                echo "Посылка ID $parcelId зарегистрирована как загруженная.";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            echo "Error inserting record: " . $conn->error;
        }
    }
}
?>