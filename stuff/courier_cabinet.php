<?php
session_start();
$name = $_SESSION['user'];
echo "<h1>Личный кабинет курьера $name</h1>";