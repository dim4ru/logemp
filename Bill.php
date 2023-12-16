<?php
$routeWest =array(
    "Омск" => 0,
    "Челябинск" => 920,
    "Самара" => 1790,
    "Москва" => 2920,
    "Санкт-Петербург" => 3630,
    "Мурманск" => 4970,
);
class Bill
{
//    public $parcel_id;
    public $weight;
    public $volume;
    public $from_city;
    public $to_city;

    // Конструктор класса для установки начальных значений атрибутов
    public function __construct($weight, $volume, $from_address, $to_address)
    {

        // Если $from_address содержит название города

        global $routeWest;
        foreach ($routeWest as $city => $distance) {
            foreach (explode(' ', $from_address) as $word )
            if (stripos($word, $city) !== false) {
                $this->from_city = $city;
                break;
            }
        }

        foreach ($routeWest as $city => $distance) {
            foreach (explode(' ', $to_address) as $word )
                if (stripos($word, $city) !== false) {
                    $this->to_city = $city;
                    break;
                }
        }
//        $this->parcel_id = $parcel_id;
        $this->weight = $weight;
        $this->volume = $volume;
    }

    // Другие методы класса могут быть добавлены здесь
    public function calculatePrice()
    {
        global $routeWest;
        if ($routeWest[$this->to_city] > $routeWest[$this->from_city]){
            $distance = $routeWest[$this->to_city] - $routeWest[$this->from_city];

        } else {
            $distance = $routeWest[$this->from_city] - $routeWest[$this->to_city];
        }
        $price = $this->weight * $this->volume * $distance * 0.058 + 0;
        return $price;
    }
}