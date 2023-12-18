<?php
function extractCities($addresses) {
    $cities = array();
    foreach ($addresses as $address) {
        $parts = explode(',', $address);
        $city = trim($parts[count($parts) - 2]); // Получаем предпоследний элемент после разделения по запятой и убираем пробелы
        $cities[] = $city;
    }
    return $cities;
}

function extractСity($address) {
    $parts = explode(',', $address);
    $city = trim($parts[count($parts) - 2]); // Получаем предпоследний элемент после разделения по запятой и убираем пробелы
    return $city;
}

function findCityInString($address, $cities) {
    foreach ($cities as $city) {
        if (strpos($address, $city) !== false) {
            return $city;
        }
    }
    return "Город не найден";
}