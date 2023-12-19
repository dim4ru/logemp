<?php
include "../connection.php";

if(isset($_POST['status']) && isset($_POST['weight']) && isset($_POST['volume']) && isset($_POST['sender_id']) && isset($_POST['receiver_id']) && isset($_POST['address_from']) && isset($_POST['address_to']) && isset($_POST['sent']) && isset($_POST['shipped']) && isset($_POST['pickup']) && isset($_POST['delivery']) && isset($_POST['price'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $weight = $_POST['weight'];
    $volume = $_POST['volume'];
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $address_from = $_POST['address_from'];
    $address_to = $_POST['address_to'];
    $sent = $_POST['sent'];
    $shipped = $_POST['shipped'];
    $pickup = $_POST['pickup'];
    $delivery = $_POST['delivery'];
    $price = $_POST['price'];

    $sql = "UPDATE Parcels SET status='$status', weight='$weight', volume='$volume', sender_id='$sender_id', receiver_id='$receiver_id', address_from='$address_from', address_to='$address_to', sent='$sent', shipped='$shipped', pickup='$pickup', delivery='$delivery', price='$price' WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Сведение о посылке обновлены успешно";
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    echo "All fields are required";
}