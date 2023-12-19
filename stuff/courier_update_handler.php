<?php
include "../connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!isset($_POST['parcel_id'])) {
        echo "Посылка не выбрана";
    } else {
        $selectedParcelId = $_POST['parcel_id'];
        updateCourierTask($selectedParcelId);
    }
}

function updateCourierTask($id) {
    global $conn;
    // Выполняем запрос к базе данных для получения статуса
    $sql = "SELECT status FROM Parcels WHERE id = '$id'";
    $oldStatus = $conn->query($sql);

    // Проверка и сохранение результата запроса
    if ($oldStatus->num_rows > 0) {
        // Получение данных
        while($row = $oldStatus->fetch_assoc()) {
            $currentStatus = $row["status"];
        }
    }

    // Используем оператор switch для обновления статуса в соответствии с полученным значением
    switch ($currentStatus) {
        case "Ожидает курьера":
            // Выполняем запрос UPDATE Parcels SET status='Ожидает прибытия в отделение' WHERE id = '$id'
            // Выполнение SQL запроса на обновление
            $sql = "UPDATE Parcels SET status='Ожидает прибытия в отделение', sent=NOW() WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
                echo "Статус для посылки $id успешно обновлен на $currentStatus";
            } else {
                echo "Error updating record: " . $conn->error;
            }
            break;
        case "Ожидает прибытия в отделение":
            // Выполняем запрос UPDATE Parcels SET status='В отделении' WHERE id = '$id'
            // Выполнение SQL запроса на обновление
            $sql = "UPDATE Parcels SET status='В отделении' WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
                echo "Статус для посылки $id успешно обновлен на $currentStatus";
            } else {
                echo "Error updating record: " . $conn->error;
            }
            break;
        case "Ожидает доставки на дом":
            // Выполняем запрос UPDATE Parcels SET status='Доставлена' WHERE id = '$id'
            // Выполнение SQL запроса на обновление
            $sql = "UPDATE Parcels SET status='Доставлена' WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
                echo "Статус для посылки $id успешно обновлен на $currentStatus";
            } else {
                echo "Error updating record: " . $conn->error;
            }
            break;
        default:
            // Логика по умолчанию, если статус не соответствует ни одному из вышеперечисленных
            break;
    }
}
header("Location: courier_cabinet.php");
//UPDATE `Parcels` SET `status`='[value-2]',`sent`='[value-9]' WHERE id = '$id'

//Ожидает прибытия в отделение