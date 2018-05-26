<?php

/**
 * Устанавливает часовую зону в московское время
*/
date_default_timezone_set('Europe/Moscow');


/**
 * Функция подключает файл шаблона
 * и использует буферизацию вывода для захвата его содержимого
 *
 * @param string $path путь к шаблону
 * @param array $data массив переменных
 * @return string Готовый html
 */
function render_template($path, $data = []) {

    if (!file_exists($path)) {
        return "";
    }

    ob_start();
    extract($data);
    require($path);

    $html = ob_get_clean();

    return $html;

}


/**
 * Напишите функцию для форматирования суммы
 * и добавления к ней знака рубля
 *
 * @param integer $price сумма
 * @return string отформатированная сумма
 */
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


/**
 * сколько часов и минут осталось до новых суток
 *
 * @return string отформатированное время
 */
function get_lot_time() {

    $ts_midnight      = strtotime('tomorrow');
    $secs_to_midnight = $ts_midnight - time();

    $hours            = floor($secs_to_midnight / 3600);
    $minutes          = floor(($secs_to_midnight % 3600) / 60);

    $end_time = '00.00.0000 ' . $hours . ':' . $minutes. ':00';

    return strftime("%R", strtotime($end_time));

}


/**
 * сколько часов и минут осталось до конца лота
 *
 * @param data $dt_end Дата закрытия лота
 * @return string отформатированное время
 */
function get_lot_time_end($dt_end) {

    $ts_midnight      = strtotime($dt_end); ;
    $secs_to_midnight = $ts_midnight - time();

    $hours            = floor($secs_to_midnight / 3600);
    $minutes          = floor(($secs_to_midnight % 3600) / 60);

    return $hours . ':' . $minutes;;

}


/**
 * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
 *
 * @param  integer $number  Число на основе которого нужно сформировать окончание
 * @param  array $endingsArray   Массив слов или окончаний для чисел (1, 4, 5),
 *         например array('яблоко', 'яблока', 'яблок')
 * @return string
 */
function get_num_ending($number, $endingArray) {

    $number = $number % 100;
    if ($number >= 11 && $number <= 19) {
        $ending = $endingArray[2];
    } else {
        $i = $number % 10;
        switch ($i)
        {
            case (1): $ending = $endingArray[0]; break;
            case (2):
            case (3):
            case (4): $ending = $endingArray[1]; break;
            default: $ending = $endingArray[2];
        }
    }

    return $ending;

}


/**
 * Время в человеческом формате (5 минут назад, час назад и т.д.)
 *
 * @param data $rate_date Дата создания ставки
 * @return string Отформатированное время
 */
function get_rate_time($rate_date) {

    $rate_time    = strtotime($rate_date);
    $secs_to_time = time() - $rate_time;
    $hours        = floor($secs_to_time / 3600);
    $minutes      = floor(($secs_to_time % 3600) / 60);

    if ($hours >= 24) {
        $end_time_format = date('d.m.y \в H:m', $rate_time);

    } elseif (($hours < 24) && ($hours >= 1)) {

        $end_time_format = $hours .' ' . get_num_ending($hours, array('час', 'часа', 'часов')) . ' назад';

    } elseif ($minutes > 0) {

        $end_time_format = $minutes .' ' . get_num_ending($minutes, array('минута', 'минуты', 'минут')) . ' назад';
    } else {
        $end_time_format = 'только что';
    }

    return $end_time_format;

}


/**
 * Получает ставки по указанному лоту
 *
 * @param $con Ссылка на базу данных
 * @param string $lot_id ИД лота
 * @return array Массив лотов
 */
function get_rates($con, $lot_id) {

    $sql = "SELECT  r.dt_registration , r.price_user, r.user_id, u.name as name_user FROM rates r "
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


/**
 * Получает максимальную ставку по лоту
 *
 * @param $con Ссылка на базу данных
 * @param string $lot_id ИД лота
 * @return array Массив лотов
 */
function get_max_rate($con, $lot_id) {

    $sql = "SELECT r.lot_id, MAX(r.price_user) as price_user FROM rates r "
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


/**
 * Определяет сделал ли пользователь ставку по лоту
 *
 * @param $con Ссылка на базу данных
 * @param string $user_id ИД пользователя
 * @param string $lot_id ИД лота
 * @return bool есть стака
 */
function is_rate_user($con, $user_id, $lot_id) {

    $sql = "SELECT  r.dt_registration , r.price_user, r.user_id, u.name as name_user FROM rates r "
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

/**
 * Получает названия категорий
 *
 * @param $con Ссылка на базу данных
 * @return array Массив категорий
 */
function get_categories($con) {

    $sql = "SELECT name FROM categories";

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


/**
 * Получает ИД категорий
 *
 * @param $con Ссылка на базу данных
 * @return array Массив категорий
 */
function get_id_categories($con) {

    $sql = "SELECT name, id FROM categories";

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


/**
 * Добавляет ставку
 *
 * @param $con Ссылка на базу данных
 * @param integer $cost цена
 * @param string $user_id ИД пользователя
 * @param string $lot_id ИД лота
 * @return bool результат выполнения запроса
 */
function add_rate ($con, $cost, $user_id, $lot_id) {

    $res = [];
    $sql = "INSERT INTO rates (dt_registration, price_user, user_id, lot_id) VALUES (NOW(),?, ?, ?)";

    $stmt = db_get_prepare_stmt($con, $sql, [$cost, $user_id, $lot_id]);
    $res  = mysqli_stmt_execute($stmt);

    return $res;

}


/**
 * Поиск лотов
 *
 * @param $con Ссылка на базу данных
 * @param string $search искомая строка
 * @return array Массив лотов
 */
function get_search_lots($con, $search, $offset = 0) {

    $sql = "SELECT l.id, l.name, l.price, l.url_pictures,l.dt_end, l.description, c.name as category FROM lots l "
        . " JOIN categories c "
        . " ON l.category_id = c.id "
        . " WHERE NOW() < l.dt_end AND MATCH (l.name, l.description) AGAINST ('" . $search . "') "
        . " ORDER BY l.dt_add DESC Limit 9  OFFSET " . $offset ."";

    $result = mysqli_query($con, $sql);
    $lots = [];

    if (!$result) {
        $error = mysqli_error($con);
        print("Ошибка MySQL: ". $error);
    } else {
        $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return $lots;

}

?>
