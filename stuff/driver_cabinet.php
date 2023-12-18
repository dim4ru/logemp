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
$name = $_SESSION['user'];
echo "<div class='header'>";
echo "<h1>Личный кабинет экспедитора $name</h1>";
echo "<a href='../logout.php'>Выход</a>";
echo "</div>";

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL запрос
$sql = "SELECT address FROM Offices";
$result = $conn->query($sql);
$addresses = array();

// Получение данных и сохранение их в массиве
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $addresses[] = $row["address"];
    }
} else {
    echo "0 results";
}
$conn->close();

// Функция для извлечения городов из массива строк адресов
include "../functions/extractCities.php";

// Вызов функции extractCities() с передачей массива адресов
$cityArray = extractCities($addresses);


?>
<form action="driver_cabinet.php" method="post">
    <label for="cities">Выберите город прибытия:</label>
    <select name="selectedCity" id="cities">
        <option>Не выбрано</option>
        <?php
        foreach ($cityArray as $city) {
            echo "<option value=\"$city\">$city</option>";
        }
        ?>
    </select>
    <input type="submit" value="Подтвердить">
</form>
<?php
include "../connection.php";
// Проверяем, был ли отправлен POST запрос
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем выбранный город из формы
    $selectedCity = $_POST['selectedCity'];

    // Проверяем, чтобы выбранный город был выбран (не "Не выбрано")
    if ($selectedCity != "Не выбрано") {
        // Выполняем SQL запрос, используя выбранный город в качестве фильтра
        $sql = "SELECT id, weight, volume, address_from, address_to FROM Parcels WHERE id IS NOT NULL AND weight IS NOT NULL AND volume IS NOT NULL AND address_from IS NOT NULL AND address_to IS NOT NULL ORDER BY sent ASC;";
        $result = $conn->query($sql);
        // Обрабатываем результаты запроса
        if ($result->num_rows > 0) {
            ?>
            <!-- Первая таблица: откуда -->
            <table>
                <tr>
                    <th>ID</th>
                    <th>Вес</th>
                    <th>Объем</th>
                    <th>Откуда</th>
                </tr>
                <?php
                $result->data_seek(0); // сбросить указатель результата, чтобы начать сначала
                while($row = $result->fetch_assoc()) {
                    if (findCityInString($row["address_from"], $cityArray) == $selectedCity) {
                        ?>
                        <tr>
                            <td><?php echo $row["id"]; ?></td>
                            <td><?php echo $row["weight"]; ?></td>
                            <td><?php echo $row["volume"]; ?></td>
                            <td><?php echo findCityInString($row["address_from"], $cityArray); ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>

            <?php

            ?>

            <!-- Вторая таблица: куда -->
            <table>
                <tr>
                    <th>ID</th>
                    <th>Вес</th>
                    <th>Объем</th>
                    <th>Куда</th>
                </tr>
                <?php
                $result->data_seek(0); // сбросить указатель результата, чтобы начать сначала
                while($row = $result->fetch_assoc()) {
                    if (findCityInString($row["address_to"], $cityArray) == $selectedCity) {
                        ?>
                        <tr>
                            <td><?php echo $row["id"]; ?></td>
                            <td><?php echo $row["weight"]; ?></td>
                            <td><?php echo $row["volume"]; ?></td>
                            <td><?php echo findCityInString($row["address_to"], $cityArray); ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
            <?php
        } else {
            echo "0 результатов";
        }
    } else {
        echo "Город не выбран";
    }
}