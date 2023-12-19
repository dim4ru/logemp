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
    $input = $_POST['unloadInput'];

    $updateIds = explode(" ", $input);
    // Заменяем пробелы на запятые
    $deleteIds = str_replace(' ', ',', $input);

    // Далее для каждого значения в массиве выполняем запрос INSERT INTO
    foreach ($updateIds as $parcelId) {
        $newStatus = isDeliveryRequired($conn, $parcelId) ? 'Ожидает доставки на дом' : 'Готова к выдаче';
        // Выполняем SQL запрос для вставки
        $insertSql = "DELETE FROM car_load WHERE parcel_id IN($deleteIds)";

        // Выполняем запрос вставки
        if ($conn->query($insertSql) === TRUE) {
            // Запрос INSERT выполнен успешно, теперь выполним запрос UPDATE
            $updateSql = "UPDATE Parcels SET status='$newStatus' WHERE id = '$parcelId'";

            // Выполняем запрос UPDATE
            if ($conn->query($updateSql) === TRUE) {
                echo "Посылка ID $parcelId зарегистрирована как отгруженная.";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            echo "Error inserting record: " . $conn->error;
        }
    }
}

function isDeliveryRequired($conn, $parcelId) {
    // Формируем SQL запрос для получения значения delivery по заданному parcelId
    $sql = "SELECT delivery FROM Parcels WHERE id = $parcelId";

    // Выполняем SQL запрос для получения значения delivery
    $result = $conn->query($sql);

    // Проверяем, были ли получены результаты
    if ($result->num_rows > 0) {
        // Возвращаем значение полученное из запроса
        $row = $result->fetch_assoc();
        return $row["delivery"];
    } else {
        // Возвращаем null, если ничего не было найдено
        return null;
    }
}
// Готова к выдаче','Ожидает доставки на дом'
?>