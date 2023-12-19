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

    tr:hover {
        background-color: #dddddd;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<?php
include "../connection.php";
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php"); // Перенаправляем пользователя на страницу входа, если сессия не существует
    exit();
}

$name = $_SESSION['user'];
echo "<div class='header'>";
echo "<h1>Личный кабинет курьера $name</h1>";
echo "<a href='../logout.php'>Выход</a>";
echo "</div>";
echo "<i>Выберите посылку для доставки. В поле <b>Task</b> вы видите задание, которое необходимо выполнить для продвижения посылки. После его выполнения нажмите кнопку <button>></button> и статус заказа сменится на следующий.</i>"
?>

<form method="post" action="courier_update_handler.php">
    <?php
    $statusArray = array('Ожидает курьера', 'Ожидает прибытия в отделение', 'Ожидает доставки на дом');
    getAvailableParcels($statusArray, $conn);
    ?>
</form>

<?php


function checkNull($value)
{
    return $value === null ? "-" : $value;
}

function getDeliveryIcon($value)
{
    return $value == 1 ? "✔️" : "❌";
}

function getInstructionsText($conn, $row)
{
    if ($row['status'] === 'Ожидает курьера') {
        $senderName = clientNameById($conn, $row['sender_id']);
        return "Забрать у $senderName";
    } elseif ($row['status'] === 'Ожидает прибытия в отделение') {
        return "Доставить в отделение";
    } elseif ($row['status'] === 'Ожидает доставки на дом') {
        $receiverName = clientNameById($conn, $row['receiver_id']);
        return "Доставить $receiverName";
    }
}

function clientNameById($conn, $_client_id)
{
    $sql_sender_name = "SELECT name FROM Clients WHERE id = '$_client_id'";
    $result_sender_name = $conn->query($sql_sender_name);
    if ($result_sender_name->num_rows > 0) {
        $sender_data = $result_sender_name->fetch_assoc();
        return $sender_data["name"];
    }
}

function extractCities($addresses)
{
    $cities = array();
    foreach ($addresses as $address) {
        $parts = explode(',', $address);
        $city = trim($parts[count($parts) - 2]); // Получаем предпоследний элемент после разделения по запятой и убираем пробелы
        $cities[] = $city;
    }
    return $cities;
}

function getAllCities($conn)
{
    // SQL запрос
    $sql = "SELECT address FROM Offices";
    $result = $conn->query($sql);
    $addresses = array();

// Получение данных и сохранение их в массиве
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $addresses[] = $row["address"];
        }
    } else {
        echo "0 results";
    }

// Вызов функции extractCities() с передачей массива адресов
    return extractCities($addresses);
}

function findCityInString($address, $cities)
{
    foreach ($cities as $city) {
        if (strpos($address, $city) !== false) {
            return $city;
        }
    }
    return "Город не найден";
}

function getAvailableParcels($statusArray, $conn)
{
    $status = implode("','", $statusArray);
    $query = "SELECT * FROM Parcels WHERE status IN ('$status')";
    $result = $conn->query($query);
    $name = $_SESSION['user'];
    $allCities = getAllCities($conn);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Sent</th><th>Pickup</th><th>Delivery</th><th>Address From</th><th>Address To</th><th>Weight</th><th>Volume</th><th>Status</th><th>Task</th><th>Done</th></tr>";
        while ($row = $result->fetch_assoc()) {
            if (
                (
                    (getCourierCity($conn, $name) == findCityInString($row["address_from"], $allCities))
                    &&
                    (($row["status"] == "Ожидает курьера") || ($row["status"] == "Ожидает прибытия в отделение"))
                )
                ||
                (
                    (getCourierCity($conn, $name) == findCityInString($row["address_to"], $allCities))
                    &&
                    ($row["status"] == "Ожидает доставки на дом")
                )
            ) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td><td>" . checkNull($row['sent']) . "</td><td>" . getDeliveryIcon($row['pickup']) . "</td><td>" . getDeliveryIcon($row['delivery']) . "</td><td>" . $row['address_from'] . "</td><td>" . $row['address_to'] . "</td><td>" . checkNull($row['weight']) . "</td><td>" . checkNull($row['volume']) . "</td><td>" . $row['status'] . "</td><td>" . getInstructionsText($conn, $row) . "</td>";
                echo "<td><button name='parcel_id' type='submit' value='" . $row['id'] . "'>></button></td>";
                echo "</tr>";
            }
        }
        echo "</table>";

    } else {
        echo "Нет доступных посылок с данным статусом.";
    }
}

function extractCity($address)
{
    $parts = explode(',', $address);
    $city = trim($parts[count($parts) - 2]); // Получаем предпоследний элемент после разделения по запятой и убираем пробелы
    return $city;
}

function getCourierCity($conn, $name)
{
    $query = "SELECT address FROM Offices WHERE id = (SELECT office_id FROM Stuff WHERE name = '$name')";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $officeAddress = $row['address'];
        return extractCity($officeAddress);
    } else {
        // В случае ошибки в запросе или отсутствия данных можно вернуть значение по умолчанию или обработать ошибку по-другому
        return "Город не определен";
    }
}

?>
