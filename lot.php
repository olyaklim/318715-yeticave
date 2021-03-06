<?php
session_start();

require_once('connect_db.php');
require_once('mysql_helper.php');
require_once('functions.php');
require_once('vendor/autoload.php');

$is_auth     =  false;
$user_name   = '';
$user_avatar = '';
$title_page  = htmlspecialchars($lots['name']);
$main_page   = false;
$errors      = [];
$min_rate    = 0;
$rate_visible = true;
$price_lot = 0;
$min_price = 0;

if (isset($_SESSION['user'])) {
    $is_auth     = true;
    $user_name   = $_SESSION['user']['name'];
    $user_avatar = $_SESSION['user']['avatar_path'];
}

// Отправьте SQL-запрос для получения списка категорий
$categories = get_categories($con);
$categories_id = get_id_categories($con);

$table_rates = [];
$lots = [];
$lot_id = intval($_GET['id']);

// показать лот по его id
$sql_lot = "SELECT l.dt_add, l.name, l.url_pictures, l.price, l.dt_end, l.price_step, l.author_id, l.description, c.name as category FROM lots l "
. " JOIN categories c "
. " ON l.category_id = c.id "
. " WHERE l.id = '" . $lot_id . "' ";

if ($result_lot = mysqli_query($con, $sql_lot)) {

    if (!mysqli_num_rows($result_lot)) {
        http_response_code(404);
        $page_content = "Лот не найден!";
    } else {
        $lots = mysqli_fetch_assoc($result_lot);

        //Блок добавления ставки не показывается если: пользователь не авторизован, срок размещения лота истёк
        //лот создан текущим пользователем, пользователь уже добавлял ставку для этого лота

        if (strtotime($lots['dt_end']) < time()) {
            $rate_visible = false;
        }

        if (isset($_SESSION['user'])) {

            if ($_SESSION['user']['id'] == $lots['author_id'] ) {
                $rate_visible = false;
            }

            if (is_rate_user($con, $_SESSION['user']['id'], $lot_id)) {
                $rate_visible = false;
            }
        }

        // Отправьте SQL-запрос для получения списка ставок
        $table_rates = get_rates($con, $lot_id);

        //Текущая цена рассчитывается как максимальная ставка по этому лоту, либо, если ставок нет, начальная цена лота.
        $max_rate = get_max_rate($con, $lot_id);
        if ($max_rate) {
            $price_lot = intval($max_rate['price_user']);
        } else {
            $price_lot = $lots['price'];
        }

        $min_price =  $price_lot + intval($lots['price_step']);

        // передаем в шаблон результат выполнения HTML код главной страницы
        $page_content = render_template('templates/lot.php', ['lots' => $lots, 'categories' => $categories, 'categories_id' => $categories_id, 'table_rates' => $table_rates, 'rate_visible' => $rate_visible, 'price_lot' => $price_lot, 'min_price' => $min_price]);
    }

} else {
    $error = mysqli_error($con);
    $page_content = "";
    print("Ошибка MySQL: ". $error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $rate = $_POST;

    if (empty($rate['cost'])) {
        $errors['cost'] = 'Ставку надо заполнить!';
    } else {

        // Проверка ставки: Значение поля Ваша сумма должно отвечать следующим требованиям:
        // 1) целое положительное число
        // 2) значение должно быть больше, чем текущая цена лота + минимальный шаг

        if (($rate['cost'] != intval($rate['cost'])) || ($rate['cost'] < 1)) {
            $errors['cost'] = 'Поле Ставка должно быть целым числом больше 0 ';
        } elseif ($rate['cost'] < ($lots['price'] + $lots['price_step'])) {
            $errors['cost'] = 'Поле Ставка должно быть не меньше Мин. ставка!';
        }
    }

    if ($errors) {
        $page_content = render_template('templates/lot.php', ['lots' => $lots, 'rate' => $rate, 'categories' => $categories, 'categories_id' => $categories_id, 'table_rates' => $table_rates, 'rate_visible' => $rate_visible, 'price_lot' => $price_lot, 'min_price' => $min_price, 'errors' => $errors]);

    } else {

        $res = add_rate($con, $rate['cost'], $_SESSION['user']['id'], $lot_id);

        if ($res) {
            header("Location: lot.php?id=" . $lot_id);
        } else {
            http_response_code(503);
            print("Ошибка MySQL: ". mysqli_error($con));
        }

        $page_content = render_template('templates/lot.php', ['lots' => $lots, 'rate' => $rate, 'categories' => $categories, 'categories_id' => $categories_id, 'table_rates' => $table_rates, 'rate_visible' => $rate_visible, 'price_lot' => $price_lot, 'min_price' => $min_price,  'errors' => $errors]);
    }
}

// окончательный HTML код
$layout_content = render_template('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'categories_id' => $categories_id, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>
