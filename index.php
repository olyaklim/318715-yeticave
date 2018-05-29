<?php
session_start();

require_once('mysql_helper.php');
require_once('connect_db.php');
require_once('functions.php');
require_once('vendor/autoload.php');
require_once('getwinner.php');

$title_page  = 'Главная';
$main_page   = true;
$is_auth     = false;
$user_name   = '';
$user_avatar = '';

if (isset($_SESSION['user'])) {
    $is_auth     = true;
    $user_name   = $_SESSION['user']['name'];
    $user_avatar = $_SESSION['user']['avatar_path'];
}

// Отправьте SQL-запрос для получения списка категорий
$categories    = get_categories($con);
$categories_id = get_id_categories($con);

// Отправьте SQL-запрос для получения всей информации по новым лотам
$sql_lot = "SELECT l.id, l.name, l.price, l.url_pictures,l.dt_end, c.name as category FROM lots l "
   . " JOIN categories c "
   . " ON l.category_id = c.id "
   . " WHERE NOW() < l.dt_end "
   . " ORDER BY l.dt_add DESC Limit 6";

if ($result_lot = mysqli_query($con, $sql_lot)) {
    $lots = mysqli_fetch_all($result_lot, MYSQLI_ASSOC);

    // передаем в шаблон результат выполнения HTML код главной страницы
    $page_content = render_template('templates/index.php', ['lots' => $lots, 'categories_id' => $categories_id]);
} else {
    $error = mysqli_error($con);
    $page_content = "";
    print("Ошибка MySQL: ". $error);
}

// окончательный HTML код
$layout_content = render_template('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'categories_id' => $categories_id, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>
