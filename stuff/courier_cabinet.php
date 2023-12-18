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
</style>

<?php
include "../connection.php";
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php"); // Перенаправляем пользователя на страницу входа, если сессия не существует
    exit();
}

$name = $_SESSION['user'];
echo "<h1>Личный кабинет курьера $name</h1>";

// Вызов функции getAvailableParcels
$statusArray = array('Ожидает курьера','Ожидает прибытия в отделение','Ожидает доставки на дом');
getAvailableParcels($statusArray, $conn);

function checkNull($value) {
    return $value === null ? "-" : $value;
}

function getDeliveryIcon($value) {
    return $value == 1 ? "✔️" : "❌";
}

function getInstructionsText($conn, $row) {
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

function clientNameById($conn, $_client_id) {
    $sql_sender_name = "SELECT name FROM Clients WHERE id = '$_client_id'";
    $result_sender_name = $conn->query($sql_sender_name);
    if ($result_sender_name->num_rows > 0) {
        $sender_data = $result_sender_name->fetch_assoc();
        return $sender_data["name"];
    }
}

function getAvailableParcels($statusArray, $conn) {
    $status = implode("','", $statusArray);
    $query = "SELECT * FROM Parcels WHERE status IN ('$status')";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Sent</th><th>Pickup</th><th>Delivery</th><th>Address From</th><th>Address To</th><th>Weight</th><th>Volume</th><th>Status</th><th>To-do</th><th>Done</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td><td>" . checkNull($row['sent']) . "</td><td>" . getDeliveryIcon($row['pickup']) . "</td><td>" . getDeliveryIcon($row['delivery']) . "</td><td>" . $row['address_from'] . "</td><td>" . $row['address_to'] . "</td><td>" . checkNull($row['weight']) . "</td><td>" . checkNull($row['volume']) . "</td><td>" . $row['status'] . "</td><td>" . getInstructionsText($conn, $row) . "</td>";
            echo "<td><button>></button></td>";
            echo "</tr>";
        }
        echo "</table>";

    } else {
        echo "Нет доступных посылок с данным статусом.";
    }
}
?>
