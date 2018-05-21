<?php

// Установите часовую зону в московское время
date_default_timezone_set('Europe/Moscow');


// Функция должна подключить файл шаблона и использовать буферизацию вывода для захвата его содержимого
function renderTemplate($path, $data = []) {

    if (!file_exists($path)) {
        return "";
    }

    ob_start();
    extract($data);
    require($path);

    $html = ob_get_clean();

    return $html;

}

// Напишите функцию для форматирования суммы и добавления к ней знака рубля
function format_price($price) {

    // Округлить число до целого
    $price = ceil($price);

     // отделить пробелом три последних цифры
    if ($price >= 1000) {
        $price = number_format($price, 0, '.', ' ');
    }

    // Добавить к получившейся строке пробел и знак рубля - ₽
    $price .= "&nbsp;&#8381";

    return $price;
}

// сколько часов и минут осталось до новых суток
function getLotTime() {

    $ts_midnight      = strtotime('tomorrow');
    $secs_to_midnight = $ts_midnight - time();

    $hours            = floor($secs_to_midnight / 3600);
    $minutes          = floor(($secs_to_midnight % 3600) / 60);

    $end_time = '00.00.0000 ' . $hours . ':' . $minutes. ':00';

    return strftime("%R", strtotime($end_time));

}

function getCategories($con) {

    $sql ="SELECT name FROM categories";

    $result = mysqli_query($con, $sql);
    $categories = [];

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: ". $error);
    } else {

        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        foreach ($rows as $row) {
            $categories[] = $row['name'];
        }
    }

    return $categories;

}

function getIdCategories ($con) {
    $sql ="SELECT name, id FROM categories";

    $result = mysqli_query($con, $sql);
    $categories = [];

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: ". $error);
    } else {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $categories;

}

?>
