<?php
session_start();

require_once('connect_db.php');
require_once('functions.php');
require_once 'vendor/autoload.php';

$title_page  = 'Список моих ставок';
$main_page   = false;
$is_auth     = false;
$user_name   = '';
$user_avatar = '';
$table_rates = [];

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit();
} else {
    $is_auth     = true;
    $user_name   = $_SESSION['user']['name'];
    $user_avatar = $_SESSION['user']['avatar_path'];
}

// Отправьте SQL-запрос для получения списка категорий
$categories = get_categories($con);
$categories_id = get_id_categories($con);
// Получить список ставок пользователя
$table_rates = get_user_rates($con, $_SESSION['user']['id']);
// Получить победившие лоты пользователя
$user_win_lots = get_win_lots($con, $_SESSION['user']['id']);

if ($table_rates) {

    // передаем в шаблон результат выполнения HTML код главной страницы
    $page_content = render_template('templates/my_rates.php', ['lots' => $lots, 'categories' => $categories_id, 'table_rates' => $table_rates, 'user_win_lots' => $user_win_lots]);
} else {
    $page_content = "Вы еще не делали ставок";
}

// окончательный HTML код
$layout_content = render_template('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'categories_id' => $categories_id, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>
