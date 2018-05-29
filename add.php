<?php
session_start();

require_once('connect_db.php');
require_once('functions.php');
require_once('mysql_helper.php');
require_once('vendor/autoload.php');

$is_auth     =  false;
$title_page  = 'Добавление лота';
$user_name   = '';
$user_avatar = '';
$main_page   = false;
$errors      = [];

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit();
} else {
    $is_auth     = true;
    $user_name   = $_SESSION['user']['name'];
    $user_avatar = $_SESSION['user']['avatar_path'];
}

// Отправьте SQL-запрос для получения списка категорий
$categories = get_id_categories($con);
$categories_name = get_categories($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST;

    $required = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
    $dict     = ['lot-name' => 'Название', 'category' => 'Категория', 'lot-rate' => 'Начальная цена', 'message' => 'Описание', 'lot-step' => 'Шаг ставки', 'lot-date' => 'Дата окончания торгов', 'file' => 'Изображние'];

    if (isset($lot['lot-rate']) && intval($lot['lot-rate']) < 1) {
        $errors['lot-rate'] = 'Поле Ставка должно быть больше ноля';
    }

    if (isset($lot['lot-step']) && intval($lot['lot-step']) < 1) {
        $errors['lot-step'] = 'Поле Шаг должно быть больше ноля';
    }

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (isset($lot['lot-date']) && $lot['lot-date'] && (strtotime($lot['lot-date']) < strtotime('tomorrow') ) ) {
    	$errors['lot-date'] = 'Дата должна быть больше текущей';
    }

    if (isset($_FILES['lot_img']['name']) && $_FILES['lot_img']['name']) {

        $tmp_name = $_FILES['lot_img']['tmp_name'];
        $file_type = mime_content_type($tmp_name);
        if ($file_type == 'image/png') {
            $filename = uniqid() . '.png';
            $lot['lot_img'] = 'img/' . $filename;
        } elseif ($file_type == 'image/jpeg') {
            $filename = uniqid() . '.jpeg';
            $lot['lot_img'] = 'img/' . $filename;
        } elseif ($file_type == 'image/jpg') {
            $filename = uniqid() . '.jpg';
            $lot['lot_img'] = 'img/' . $filename;
        } else {
            $errors['file'] = 'Допустимый формат картинок: jpg jpeg png';
        }

        if (!$errors['file']) {
            move_uploaded_file($tmp_name, $lot['lot_img']);
        }

    } else {

        if (isset($lot['filepath']) && $lot['filepath']) {
            $lot['lot_img'] = $lot['filepath'];
        } else {
            $errors['file'] = 'Вы не загрузили файл';
        }

    }

    if ($errors) {
        $page_content = render_template('templates/add.php', ['categories' => $categories, 'categories_name' => $categories_name, 'errors' => $errors, 'lot' => $lot]);
    } else {

        $sql = "INSERT INTO lots (dt_add, category_id, name, description, url_pictures, price, dt_end, price_step, author_id) VALUES (NOW(),?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = db_get_prepare_stmt($con, $sql, [$lot['category'], $lot['lot-name'], $lot['message'], $lot['lot_img'], intval($lot['lot-rate']), $lot['lot-date'], intval($lot['lot-step']), $_SESSION['user']['id']]);

        $res  = mysqli_stmt_execute($stmt);

        if ($res) {
            $lot_id = mysqli_insert_id($con);
            header("Location: lot.php?id=" . $lot_id);
        } else {
            http_response_code(503);
            print("Ошибка MySQL: ". mysqli_error($con));
        }
    }
} else {
    $page_content = render_template('templates/add.php', ['categories' => $categories,'categories_name' => $categories_name, 'errors' => []]);
}

// окончательный HTML код
$layout_content = render_template('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories_name, 'categories_id' => $categories, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>

