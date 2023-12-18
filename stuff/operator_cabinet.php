<?php
session_start();
$name = $_SESSION['user'];
echo "<h1>Личный кабинет оператора отделения $name</h1>";