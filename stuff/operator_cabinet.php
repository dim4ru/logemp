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

// Проверяем, активна ли сессия
if (!isset($_SESSION['user'])) {
    // Сессия не содержит информацию о пользователе, перенаправляем на страницу входа
    header("Location: ../index.php");
}

$name = $_SESSION['user'];
echo "<div class='header'>";
echo "<h1>Личный кабинет оператора отделения $name</h1>";
echo "<a href='../logout.php'>Выход</a>";
echo "</div>";

// Проверяем подключение к базе данных
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Выполняем запрос на получение всех данных из таблицы Parcels
$sql = "SELECT id, status, weight, volume, sender_id, receiver_id, address_from, address_to, sent, shipped, pickup, delivery, price FROM Parcels";
$result = $conn->query($sql);

// Если есть результаты, выводим их в виде таблицы
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Status</th><th>Weight</th><th>Volume</th><th>Sender ID</th><th>Receiver ID</th><th>Address From</th><th>Address To</th><th>Sent</th><th>Shipped</th><th>Pickup</th><th>Delivery</th><th>Price</th></tr>";
    // выводим данные построчно
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["status"]."</td><td>".$row["weight"]."</td><td>".$row["volume"]."</td><td>".$row["sender_id"]."</td><td>".$row["receiver_id"]."</td><td>".$row["address_from"]."</td><td>".$row["address_to"]."</td><td>".$row["sent"]."</td><td>".$row["shipped"]."</td><td>".$row["pickup"]."</td><td>".$row["delivery"]."</td><td>".$row["price"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 результатов";
}