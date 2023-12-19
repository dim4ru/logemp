<?php
include "../connection.php";
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "<h3>Новая посылка</h3>";
echo <<<HTML
        <b>Данные отправителя</b><br>
        <form action="../client/client_insert_parcel_handler.php" method="POST">
            <label for="sender_name">ФИО отправителя:</label><br>
            <input type="text" id="sender_name" name="sender_name" required><br>
            <label for="sender_tel">Номер телефона отправителя:</label><br>
            <input type="tel" id="sender_tel" name="sender_tel" maxlength="10" required><br>
            <label for="pickup_address">Адрес, откуда забрать посылку:</label>
            <input type="text" id="pickup_address" name="pickup_address" placeholder="напр. улица 70 лет Октября, 3/1, кв 101, Омск, 644074" required><br>
        
            <br><b>Данные получателя</b>
            <p style="font-size: 14px"><i>Адрес для доставки нужно только указать один:<br>либо адрес доставки на дом (+300р к стоимости), либо выбрать пункт выдачи для самовывоза.<br>В случае заполнения двух полей, будет выбрана опция самовывоза.</i></p>
            <label for="receiver_name">ФИО получателя:</label><br>
            <input type="text" id="receiver_name" name="receiver_name" placeholder="Фамилия Имя Отчество" required><br>
            <label for="receiver_tel">Номер телефона получателя:</label><br>
            <input type="tel" id="receiver_tel" name="receiver_tel" placeholder="Без +7 (напр.9991234566)" maxlength="10" required><br>
        
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
            <input type="number" step="0.001" id="weight" name="weight" max="700"><br>
        
            <label for="volume">Объем, м2:</label><br>
            <input type="number" step="0.001" id="volume" name="volume" max="4"><br>
            
            <br>
            
            <input type="submit" value="Создать заявку">
        </form>
HTML;

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