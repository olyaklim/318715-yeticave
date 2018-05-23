<?php
session_start();

require_once('functions.php');
require_once('mysql_helper.php');

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

// В сценарии главной страницы выполните подключение к MySQL
$con = mysqli_connect("localhost", "root", "","yeticave");

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка Подключения БД : ". $error);
    return;
}

// Отправьте SQL-запрос для получения списка категорий
$categories = getIdCategories($con);
$categories_name = getCategories($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lot = $_POST;

    $required = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
    $dict     = ['lot-name' => 'Название', 'category' => 'Категория', 'lot-rate' => 'Начальная цена', 'message' => 'Описание', 'lot-step' => 'Шаг ставки', 'lot-date' => 'Дата окончания торгов', 'file' => 'Изображние'];

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (isset($_FILES['lot_img']['name']) && $_FILES['lot_img']['name']) {
        $tmp_name = $_FILES['lot_img']['tmp_name'];

        $filename = uniqid() . '.jpg';
        $lot['path'] = $filename;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type !== "image/jpeg") {
            $errors['file'] = 'Загрузите картинку в формате JPG';
        }
        else {
            move_uploaded_file($tmp_name, 'img/' . $filename);
            $lot['lot_img'] = 'img/' . $filename;
        }
    }
    else {
        $errors['file'] = 'Вы не загрузили файл';
    }

    if ($errors) {
        $page_content = renderTemplate('templates/add.php', ['categories' => $categories, 'categories_name' => $categories_name, 'errors' => $errors, 'lot' => $lot]);
    } else {

        $sql = 'INSERT INTO lots (dt_add, category_id, name, description, url_pictures, price, dt_end, price_step, author_id) VALUES (NOW(),?, ?, ?, ?, ?, ?, ?, ?)';


        $stmt = db_get_prepare_stmt($con, $sql, [$lot['category'], $lot['lot-name'], $lot['message'], $lot['lot_img'], $lot['lot-rate'], $lot['lot-date'], $lot['lot-step'], $_SESSION['user']['id']]);

        $res  = mysqli_stmt_execute($stmt);

        if ($res) {
            $lot_id = mysqli_insert_id($con);
            header("Location: lot.php?id=" . $lot_id);
        }
        else {
            http_response_code(503);
            print("Ошибка MySQL: ". mysqli_error($con));
        }
    }

}
else {
    $page_content = renderTemplate('templates/add.php', ['categories' => $categories,'categories_name' => $categories_name, 'errors' => []]);
}


// окончательный HTML код
$layout_content = renderTemplate('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories_name, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>

