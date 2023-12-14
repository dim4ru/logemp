<?php
include "../connection.php";

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
    // Вставка или обновление данных в таблице Clients, игнорируя дубликаты
    $sql = "INSERT IGNORE INTO Clients (name, phoneNumber) VALUES ('$sender_name', '$sender_tel')";
    mysqli_query($conn, $sql);

    $sql = "INSERT IGNORE INTO Clients (name, phoneNumber) VALUES ('$receiver_name', '$receiver_tel')";
    mysqli_query($conn, $sql);

    $currentDate = date("Y-m-d");
    // Вставка данных в таблицу Parcels
    $sql = "INSERT INTO Parcels (status, weight, volume, sender_id, receiver_id, address_from, address_to, sent, shipped, pickup, delivery, price) 
            SELECT 'Ожидает курьера', NULL, NULL, 
                (SELECT id FROM Clients WHERE name = '$sender_name'), 
                (SELECT id FROM Clients WHERE name = '$receiver_name'), 
                '$pickup_address', '$delivery_address','$currentDate' , NULL, 1, 0, 666";
    mysqli_query($conn, $sql);
    var_dump($sql);
}
