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

// сколько часов и минут осталось до конца лота
function getLotTimeEnd($dt_end) {


    $ts_midnight      = strtotime($dt_end); ;
    $secs_to_midnight = $ts_midnight - time();

    $hours            = floor($secs_to_midnight / 3600);
    $minutes          = floor(($secs_to_midnight % 3600) / 60);

    return $hours . ':' . $minutes;;

}

//время в человеческом формате (5 минут назад, час назад и т.д.)
function getNumEnding($number, $endingArray)
{
    $number = $number % 100;
    if ($number>=11 && $number<=19) {
        $ending=$endingArray[2];
    }
    else {
        $i = $number % 10;
        switch ($i)
        {
            case (1): $ending = $endingArray[0]; break;
            case (2):
            case (3):
            case (4): $ending = $endingArray[1]; break;
            default: $ending=$endingArray[2];
        }
    }
    return $ending;
}


// время в списке ставок
function getRateTime($rate_date) {

    $rate_time    = strtotime($rate_date);
    $secs_to_time = time() - $rate_time;
    $hours        = floor($secs_to_time / 3600);
    $minutes      = floor(($secs_to_time % 3600) / 60);

    if ($hours >= 24) {

        $end_time_format = date('d.m.y \в H:m', $rate_time);

    } elseif (($hours < 24) && ($hours >= 1)) {

        $end_time_format = $hours .' ' . getNumEnding($hours, array('час', 'часа', 'часов')) . ' назад';

    } elseif ($minutes > 0) {

        $end_time_format = $minutes .' ' . getNumEnding($minutes, array('минута', 'минуты', 'минут')) . ' назад';
    } else {
        $end_time_format = 'только что';
    }

    return $end_time_format;

}


function getRates($con, $lot_id) {

    $sql ="SELECT  r.dt_registration , r.price_user, r.user_id, u.name as name_user FROM rates r "
    . " JOIN users u "
    . " ON r.user_id = u.id "
    . " WHERE r.lot_id = '" . $lot_id . "'  ORDER BY r.dt_registration DESC ";

    $result = mysqli_query($con, $sql);
    $rates = [];

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: ". $error);
    } else {
        $rates = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $rates;

}

function getMaxRate($con, $lot_id) {

    $sql ="SELECT r.lot_id, MAX(r.price_user) as price_user FROM rates r "
        . " WHERE r.lot_id = '" . $lot_id . "' GROUP BY r.lot_id ";

    $result = mysqli_query($con, $sql);
    $rates = [];

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: ". $error);
    } else {
        $rates = mysqli_fetch_assoc($result);
    }

    return $rates;
}

function isRateUser($con, $user_id, $lot_id) {
    $sql ="SELECT  r.dt_registration , r.price_user, r.user_id, u.name as name_user FROM rates r "
    . " JOIN users u "
    . " ON r.user_id = u.id "
    . " WHERE r.lot_id = '" . $lot_id . "'  AND r.user_id = '" . $user_id . "'";

    $result = mysqli_query($con, $sql);
    $rates = false;

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: ". $error);
    } else {
        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $rates = (bool) $result;
    }

    return $rates;
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

function addRate ($con, $cost, $user_id, $lot_id) {

    $res = [];
    $sql = 'INSERT INTO rates (dt_registration, price_user, user_id, lot_id) VALUES (NOW(),?, ?, ?)';

    $stmt = db_get_prepare_stmt($con, $sql, [$cost, $user_id, $lot_id]);

    $res  = mysqli_stmt_execute($stmt);

    return $res;

}

?>
