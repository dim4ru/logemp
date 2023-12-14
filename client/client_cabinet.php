<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    input[type=text] {
        width: 500px;
    }
</style>

<?php
include "../connection.php";
session_start();
if (!isset($_SESSION['tel'])) {
    // Сессия не содержит информацию о пользователе
    header("Location: client_login.php");
} else {
    $tel = $_SESSION['tel'];
    echo "<h2>Личный кабинет клиента по номеру $tel</h2>";

    // Проверяем соединение
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

// Выполняем запрос, чтобы получить id клиента по номеру телефона из сессии
    $sql = "SELECT id FROM Clients WHERE phoneNumber = $tel";
    $result = $conn->query($sql);

// Проверяем, были ли найдены результаты
    if ($result->num_rows > 0) {
        // Получаем id клиента из результата запроса
        $row = $result->fetch_assoc();
        $clientId = $row["id"];

        // Выполняем запрос на получение данных посылок отправленных или полученных данным клиентом
        $sql_parcels = "SELECT * FROM Parcels WHERE sender_id = '$clientId' OR receiver_id = '$clientId'";
        $result_parcels = $conn->query($sql_parcels);

        echo "<h3>Ваши посылки</h3>";
        // Проверяем, были ли найдены посылки
        if ($result_parcels->num_rows > 0) {
            // Выводим таблицу с полями id, status, weight, volume, sender_id, receiver_id, sent, shipped, pickup, delivery, price
            echo "<table><tr><th>ID посылки</th><th>Статус</th><th>Вес, кг</th><th>Объем, кг</th><th>Отправитель</th><th>Получатель</th><th>Адрес отправления</th><th>Адрес вручения</th><th>Передано в доставку</th><th>Вручено</th><th>Забрать</th><th>Доставить</th><th>Стоимость, RUB</th></tr>";
            while($row_parcels = $result_parcels->fetch_assoc()) {
                $pickup_symbol = ($row_parcels["pickup"] == 1) ? "✔️" : "❌";
                $delivery_symbol = ($row_parcels["delivery"] == 1) ? "✔️" : "❌";
                $shipped = ($row_parcels["shipped"] != NULL) ? $row_parcels["shipped"] : "-";
                echo "<tr><td>" . $row_parcels["id"] . "</td><td>" . $row_parcels["status"] . "</td><td>" . $row_parcels["weight"] . "</td><td>" . $row_parcels["volume"] . "</td><td>" . senderNameById($conn, $row_parcels["sender_id"]) . "</td><td>" . receiverNameById($conn, $row_parcels["receiver_id"]) . "</td><td>" . $row_parcels["address_from"] . "</td><td>" . $row_parcels["address_to"] . "</td><td>" . $row_parcels["sent"] . "</td><td>" . $shipped . "</td><td>" . $pickup_symbol . "</td><td>" . $delivery_symbol . "</td><td>" . $row_parcels["price"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "У вас нет входящих или исходящих посылок.";
        }

        echo "<h3>Новая посылка</h3>";
        echo <<<HTML
        <form action="client_insert_parcel_handler.php" method="POST">
            <label for="sender_name">Отправитель:</label><br>
            <input type="text" id="sender" name="sender"><br>
        
            <label for="receiver_name">Получатель:</label><br>
            <input type="text" id="recipient" name="recipient"><br>
        
            <label for="departure_address">Адрес отправления:</label><br>
            <input type="text" id="departure_address" name="departure_address" class="wide-input"><br>
        
            <label for="delivery_address">Адрес вручения:</label><br>
            <input type="text" id="delivery_address" name="delivery_address" class="wide-input"><br>
        
            <input type="checkbox" id="pickup" name="pickup" value="yes">
            <label for="pickup">Забрать из дома (+ 300 р.)</label><br>
        
            <input type="checkbox" id="delivery" name="delivery" value="yes">
            <label for="delivery">Доставить на дом (+ 300 р.)</label><br><br>
        
            <input type="submit" value="Отправить">
        </form>
HTML;

    } else {
        echo "Клиент не найден";
    }

// Закрываем соединение с базой данных
    $conn->close();
}

function senderNameById ($conn, $sender_id)
{
    $sql_sender_name = "SELECT name FROM Clients WHERE id = '$sender_id'";
    $result_sender_name = $conn->query($sql_sender_name);
    // Предполагается, что $result_sender_name содержит результат вашего запроса
    if (mysqli_num_rows($result_sender_name) > 0) {
        $sender_data = mysqli_fetch_assoc($result_sender_name);
        return $sender_name = $sender_data["name"];
        // Теперь $sender_name содержит имя отправителя
    }
}

function receiverNameById ($conn, $receiver_id)
{
    $sql_receiver_name = "SELECT name FROM Clients WHERE id = '$receiver_id'";
    $result_receiver_name = $conn->query($sql_receiver_name);
    // Предполагается, что $result_sender_name содержит результат вашего запроса
    if (mysqli_num_rows($result_receiver_name) > 0) {
        $receiver_data = mysqli_fetch_assoc($result_receiver_name);
        return $receiver_name = $receiver_data["name"];
    }
}