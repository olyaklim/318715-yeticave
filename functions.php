<?php

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

?>
