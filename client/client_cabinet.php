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
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logout {
        margin-left: auto;
    }
</style>
<!DOCTYPE html>
<html>
<head>
    <title>Личный кабинет</title>
</head>
<?php
include "../connection.php";
include "../Bill.php";
session_start();
if (!isset($_SESSION['tel'])) {
    // Сессия не содержит информацию о пользователе
    header("Location: client_login.php");
} else {
    $tel = $_SESSION['tel'];
    echo "<div class='header'><h2>Личный кабинет клиента по номеру $tel</h2><a class='logout' href='../logout.php'>Выход</a></div>";

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
                $sent = ($row_parcels["sent"] != NULL) ? $row_parcels["sent"] : "-";
                echo "<tr><td>" . $row_parcels["id"] . "</td><td>" . $row_parcels["status"] . "</td><td>" . $row_parcels["weight"] . "</td><td>" . $row_parcels["volume"] . "</td><td>" . clientNameById($conn, $row_parcels["sender_id"]) . "</td><td>" . clientNameById($conn, $row_parcels["receiver_id"]) . "</td><td>" . $row_parcels["address_from"] . "</td><td>" . $row_parcels["address_to"] . "</td><td>" . $sent . "</td><td>" . $shipped . "</td><td>" . $pickup_symbol . "</td><td>" . $delivery_symbol . "</td><td>" . $row_parcels["price"] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "У вас нет входящих или исходящих посылок.";
        }

        $bill = new Bill(1, 1, "Омск", "Москва", 1);
        $price = $bill->calculatePrice();
        echo $price;

        echo "<h3>Новая посылка</h3>";
        echo <<<HTML
        <b>Данные отправителя</b><br>
        <form action="client_insert_parcel_handler.php" method="POST">
            <label for="sender_name">ФИО отправителя:</label><br>
            <input type="text" id="sender_name" name="sender_name" value="
HTML;
        echo clientNameById($conn, $clientId);
        echo <<<HTML
"readonly required><br>
            <label for="sender_tel">Номер телефона отправителя:</label><br>
            <input type="tel" id="sender_tel" name="sender_tel" maxlength="10" value="
HTML;
        echo $_SESSION['tel'];
        echo <<<HTML
" readonly required><br>
            <label for="pickup_address">Адрес, откуда забрать посылку:</label>
            <input type="text" id="pickup_address" name="pickup_address" placeholder="напр. улица 70 лет Октября, 3/1, кв 101, Омск, 644074"><br>
        
            <br><b>Данные получателя</b>
            <p style="font-size: 14px"><i>Адрес для доставки нужно только указать один:<br>либо адрес доставки на дом (+300р к стоимости), либо выбрать пункт выдачи для самовывоза.<br>В случае заполнения двух полей, будет выбрана опция самовывоза.</i></p>
            <label for="receiver_name">ФИО получателя:</label><br>
            <input type="text" id="receiver_name" name="receiver_name" placeholder="Фамилия Имя Отчество" required><br>
            <label for="receiver_tel">Номер телефона получателя:</label><br>
            <input type="tel" id="receiver_tel" name="receiver_tel" placeholder="Без +7 (напр.9991234566)" maxlength="10"><br>
        
            <label for="delivery_address">Адрес вручения для доставки на дом (+ 300 р):</label>
            <input type="text" id="delivery_address" name="delivery_address" placeholder="напр. улица 70 лет Октября, 3/1, кв 101, Омск, 644074"><br>
            <label for="destination_office"><b>или</b><br>Выбрать отделение для самовывоза (бесплатно):</label>
            <select id="destination_office" name="destination_office">
HTML;

        foreach (getOfficesList($conn) as $address) {
            echo "<option value=\"$address\">$address</option>";
        }

        echo <<<HTML
            </select>
            <br><br>
            
            <b>Параметры посылки</b><br>
            <p style="font-size: 14px"><i>Оставьте оба поля пустыми, если не знаете параметров.<br>Тогда стоимость посылки будет расчитана после ее регистрации в отделении</i></p>
            <label for="weight">Вес, кг:</label><br>
            <input type="number" step="0.001" id="weight" name="weight" ><br>
        
            <label for="volume">Объем, м2:</label><br>
            <input type="number" step="0.001" id="volume" name="volume"><br>
            
            <br>
            
            <input type="submit" value="Создать заявку">
        </form>
HTML;


    } else {
        echo "Клиент не найден";
    }

// Закрываем соединение с базой данных
    $conn->close();
}

function clientNameById ($conn, $_client_id)
{
    $sql_sender_name = "SELECT name FROM Clients WHERE id = '$_client_id'";
    $result_sender_name = $conn->query($sql_sender_name);
    // Предполагается, что $result_sender_name содержит результат вашего запроса
    if (mysqli_num_rows($result_sender_name) > 0) {
        $sender_data = mysqli_fetch_assoc($result_sender_name);
        return $sender_data["name"];
        // Теперь $sender_name содержит имя отправителя
    }
}
function getOfficesList($conn)
{
    // Формируем запрос к базе данных
    $sql = "SELECT address FROM Offices WHERE 1";
    $result = $conn->query($sql);

    // Проверяем, есть ли результаты
    if ($result->num_rows > 0) {
        // Инициализируем переменную для хранения списка адресов
        $addresses = array();

        // Получаем каждую строку результата и помещаем адрес в массив
        while($row = $result->fetch_assoc()) {
            $addresses[] = $row["address"];
        }

        // Выводим список адресов для проверки
        array_unshift($addresses, "Не выбрано");
        return $addresses;

        // Теперь $addresses содержит список адресов из базы данных
    } else {
        echo "0 результатов";
    }
}
?>
</body>
</html>