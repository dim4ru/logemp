<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd; /* Рамки со всех сторон */
    }

    input:focus {
        border: 2px solid #ff0000;
        outline: none;
    }
    .narrowInput {
        width: 60px;
    }
    .wideInput {
        width: 400px;
    }
</style>

<?php
include "../connection.php";

if (!isset($_POST['id'])) {
    echo "ID посылки не получен";
} else {
    $parcelId = $_POST['id'];
    // Вывод заголовка
    echo "<h2>Изменить сведения о посылке ID $parcelId</h2>";
    // Проверяем подключение к базе данных
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

// Выполняем запрос на получение всех данных из таблицы Parcels
    $sql = "SELECT id, status, weight, volume, sender_id, receiver_id, address_from, address_to, sent, shipped, pickup, delivery, price FROM Parcels WHERE id='$parcelId'";
    $result = $conn->query($sql);

// Если есть результаты, выводим их в виде таблицы
    if ($result->num_rows > 0) {
        echo "<form action='operator_update_parcel_handler.php' method='post'>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Status</th><th>Weight</th><th>Volume</th><th>Sender ID</th><th>Receiver ID</th><th>Address From</th><th>Address To</th><th>Sent</th><th>Shipped</th><th>Pickup</th><th>Delivery</th><th>Price</th></tr>";
        // выводим данные построчно
        while ($row = $result->fetch_assoc()) {
            // Получаем данные о sender_name из запроса к базе данных
            // Нужно обновить sender_name, чтобы получить его из базы данных, например, из таблицы Clients
            $senderQuery = "SELECT name FROM Clients WHERE id = '" . $row["sender_id"] . "'";
            $senderResult = $conn->query($senderQuery);
            $senderRow = $senderResult->fetch_assoc();
            $sender_name = $senderRow["name"];

            // Получаем данные о receiver_name из запроса к базе данных
            // Нужно обновить receiver_name, чтобы получить его из базы данных, например, из таблицы Clients
            $receiverQuery = "SELECT name FROM Clients WHERE id = '" . $row["receiver_id"] . "'";
            $receiverResult = $conn->query($receiverQuery);
            $receiverRow = $receiverResult->fetch_assoc();
            $receiver_name = $receiverRow["name"];

            // выводим данные в форму
            echo "<tr><td><input name='id' type='text' value='" . $row["id"] . "' readonly></td><td><select name='status'><option value='Ожидает курьера' " . (($row["status"] == 'Ожидает курьера') ? 'selected' : '') . ">Ожидает курьера</option><option value='Ожидает прибытия в отделение' " . (($row["status"] == 'Ожидает прибытия в отделение') ? 'selected' : '') . ">Ожидает прибытия в отделение</option><option value='В отделении' " . (($row["status"] == 'В отделении') ? 'selected' : '') . ">В отделении</option><option value='В пути' " . (($row["status"] == 'В пути') ? 'selected' : '') . ">В пути</option><option value='Готова к выдаче' " . (($row["status"] == 'Готова к выдаче') ? 'selected' : '') . ">Готова к выдаче</option><option value='Ожидает доставки на дом' " . (($row["status"] == 'Ожидает доставки на дом') ? 'selected' : '') . ">Ожидает доставки на дом</option><option value='Доставлена' " . (($row["status"] == 'Доставлена') ? 'selected' : '') . ">Доставлена</option><option value='Выдана' " . (($row["status"] == 'Выдана') ? 'selected' : '') . ">Выдана</option></select></td><td><input type='text' name='weight' value='" . $row["weight"] . "' class='narrowInput'></td><td><input type='text' name='volume' value='" . $row["volume"] . "' class='narrowInput'></td><td><input type='text' name='sender_id' value='" . $row["sender_id"] . "'><br>$sender_name</td><td><input type='text' name='receiver_id' value='" . $row["receiver_id"] . "'><br>$receiver_name</td><td><input type='text' name='address_from' value='" . $row["address_from"] . "' class='wideInput'></td><td><input type='text' name='address_to' value='" . $row["address_to"] . "' class='wideInput'></td><td><input type='text' name='sent' value='" . $row["sent"] . "'></td><td><input type='text' name='shipped' value='" . $row["shipped"] . "'></td><td><input type='text' name='pickup' value='" . $row["pickup"] . "' class='narrowInput'></td><td><input type='text' name='delivery' value='" . $row["delivery"] . "' class='narrowInput'></td><td><input type='text' name='price' value='" . $row["price"] . "' class='narrowInput'></td></tr>";
        }
        echo "</table>";
        echo "<input type='submit' value='Обновить'>";
        echo "</form>";
    } else {
        echo "0 результатов";
    }
}

