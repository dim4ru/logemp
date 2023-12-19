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
    // Сессия не содержит информацию о пользователе
    header("Location: ../index.php");
}
$name = $_SESSION['user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем выбранный город из формы
    $selectedCity = $_POST['selectedCity'];

    // ... (ваш предыдущий код)

    // Выполняем SQL запрос для получения списка id посылок на отгрузку
    $sqlDispatch = "SELECT parcel_id FROM car_load WHERE car_id = (SELECT car_id FROM Stuff WHERE name = '$name')";
    $resultDispatch = $conn->query($sqlDispatch);

    $toDispatchParcelsId = array(); // Создаем массив для хранения id посылок на отгрузку

    if ($resultDispatch->num_rows > 0) {
        while ($row = $resultDispatch->fetch_assoc()) {
            $toDispatchParcelsId[] = $row["parcel_id"]; // Добавляем id посылок в массив
        }
    }
}

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
        $sql = "SELECT id, weight, volume, address_from, address_to, status FROM Parcels WHERE id IS NOT NULL AND weight IS NOT NULL AND volume IS NOT NULL AND address_from IS NOT NULL AND address_to IS NOT NULL ORDER BY sent ASC;";
        $result = $conn->query($sql);
        // Обрабатываем результаты запроса
        if ($result->num_rows > 0) {
            ?>
            <!-- Первая таблица: откуда -->
            <h3>Посылки на загрузку в городе <?php echo htmlspecialchars($selectedCity); ?> </h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Вес</th>
                    <th>Объем</th>
                    <th>Откуда</th>
                </tr>
                <?php
                $result->data_seek(0); // сбросить указатель результата, чтобы начать сначала
                // Инициализация суммы веса и объема
                $currentWeight = 0;
                $currentCapacity = 0;
                $totalWeight = 0;
                $totalVolume = 0;

                $currentLoad = getCurrentLoad($conn, $name);
                $currentLoadWeight = $currentLoad["currentLoadWeight"];
                $currentLoadVolume = $currentLoad["currentLoadVolume"];

                // Запрос к базе данных
                $Max_sql = "SELECT maxWeight, capacity FROM Cars WHERE id = (SELECT car_id FROM Stuff WHERE name = '$name')";
                $Max_result = $conn->query($Max_sql);

                if ($Max_result->num_rows > 0) {
                    // Вывод данных каждой строки
                    while($row = $Max_result->fetch_assoc()) {
                        $maxWeight = $row["maxWeight"];
                        $maxVolume = $row["capacity"];
                    }
                } else {
                    echo "Нет данных по максимальной грузоподъемности и вместимости";
                }

                // Вывод строк таблицы, пока суммы веса или объема не будут достигнуты
                while($row = $result->fetch_assoc()) {
                        if ((findCityInString($row["address_from"], $cityArray) == $selectedCity) && $row["status"] == 'В отделении') {
                            if (($totalWeight + $row["weight"] + $currentLoadWeight) > $maxWeight || ($totalVolume + $row["volume"] + $currentLoadVolume) > $maxVolume) {
                                continue; // Прерываем цикл, если суммы веса или объема достигли максимальных значений
                            } else {
                                // Обновление сумм веса и объема
                                $totalWeight += $row["weight"];
                                $totalVolume += $row["volume"];
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
                }
                ?>
            </table>

            <?php
            echo "<p>Загружено сейчас: $currentLoadWeight кг $currentLoadVolume м3<br>";
            echo "Доступно для загрузки: <b>" . ($maxWeight - $currentLoadWeight) . " кг " . ($maxVolume - $currentLoadVolume) . " м3</b></p>";
            ?>

            <form action="load_parcels_handler.php" method="post">
                <label for="loadInput">Введите ID посылок для загрузки, разделяя их пробелами:</label><br>
                <input type="text" id="loadInput" name="loadInput" placeholder="Например: 1 5 24 9 533 3 " required>
                <input type="submit" value="Загрузить">
            </form>

            <!-- Вторая таблица: куда -->
            <h3>Посылки на отгрузку в городе <?php echo htmlspecialchars($selectedCity); ?> </h3>
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
                    if ((findCityInString($row["address_to"], $cityArray) == $selectedCity) && (in_array($row["id"],$toDispatchParcelsId))) {
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
            <form action="unload_parcels_handler.php" method="post">
                <label for="unloadInput">Введите ID посылок для отгрузки, разделяя их пробелами:</label><br>
                <input type="text" id="unloadInput" name="unloadInput" placeholder="Например: 1 5 24 9 533 3 " required>
                <input type="submit" value="Отгрузить">
            </form>
            <?php
            // тут форма
        } else {
            echo "0 результатов";
        }
    } else {
        echo "Город не выбран";
    }
}

function getCurrentLoad($conn, $name) {
    // Проверка соединения
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Запрос к базе данных
    $load_sql = "SELECT 
                SUM(weight) AS currentLoadWeight,
                SUM(volume) AS currentLoadVolume
            FROM Parcels
            WHERE id IN (SELECT parcel_id FROM car_load WHERE car_id = (SELECT car_id FROM Stuff WHERE name = '$name'))";

    $load_result = $conn->query($load_sql);

    // Обработка результатов запроса
    if ($load_result->num_rows > 0) {
        // Получение данных из результата запроса
        $row = $load_result->fetch_assoc();
        $currentLoadWeight = $row["currentLoadWeight"];
        $currentLoadVolume = $row["currentLoadVolume"];

        // Возвращение полученных значений
        return array("currentLoadWeight" => $currentLoadWeight, "currentLoadVolume" => $currentLoadVolume);
    } else {

        // Возвращение значений по умолчанию, если ничего не найдено
        return array("currentLoadWeight" => 0, "currentLoadVolume" => 0);
    }
}