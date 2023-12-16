<?php
include "../connection.php";
include "../Bill.php";

// Подключение к базе данных - $conn необходимо определить заранее

// Обработка формы при отправке
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_name = $_POST['sender_name'];
    $sender_tel = $_POST['sender_tel'];
    $pickup_address = $_POST['pickup_address'];
    $receiver_name = $_POST['receiver_name'];
    $receiver_tel = $_POST['receiver_tel'];
    $delivery_address = $_POST['delivery_address'];
    $destination_office = $_POST['destination_office'];
    $weight = $_POST['weight'];
    $volume = $_POST['volume'];
    $courier = ($destination_office !== "Не выбрано") ? 1 : 2;
    // Вставка или обновление данных в таблице Clients, игнорируя дубликаты
    $sql = "INSERT IGNORE INTO Clients (name, phoneNumber) VALUES ('$sender_name', '$sender_tel')";
    mysqli_query($conn, $sql);

    $sql = "INSERT IGNORE INTO Clients (name, phoneNumber) VALUES ('$receiver_name', '$receiver_tel')";
    mysqli_query($conn, $sql);

// Вставка данных в таблицу Parcels
    if ($weight != NULL || $volume != NULL) {
        $bill = ($destination_office == "Не выбрано") ? new Bill($weight, $volume, $pickup_address, $delivery_address, $courier) : new Bill($weight, $volume, $pickup_address, $destination_office, $courier);
        $price = $bill->calculatePrice();
        $finalMessage = "<h2>Зарегистрирована посылка ". $bill->from_city. " > ". $bill->to_city .". Сумма к оплате: $price</h2>";
    }
    else {
        $price = 'NULL';
        $weight = 'NULL';
        $volume = 'NULL';
        $finalMessage = "<h2>Посылка зарегистрирована. Сумма к оплате будет расчитана после регистрации посылки в отделении</h2>";
    }
    $destination_address = ($destination_office !== "Не выбрано") ? $destination_office : $delivery_address;
    $sql = "INSERT INTO Parcels (status, weight, volume, sender_id, receiver_id, address_from, address_to, sent, shipped, pickup, delivery, price) 
        SELECT 'Ожидает курьера', $weight, $volume, 
            (SELECT id FROM Clients WHERE name = '$sender_name' LIMIT 1), 
            (SELECT id FROM Clients WHERE name = '$receiver_name' LIMIT 1), 
            '$pickup_address', '$destination_address',NULL, NULL, 1, 0, $price";
    $result = mysqli_query($conn, $sql);
    var_dump($sql);

    if (!$result) {
        echo "<h2>Ошибка при выполнении запроса: " . mysqli_error($conn) . "</h2>";
    } else {
        echo $finalMessage;
    }
}