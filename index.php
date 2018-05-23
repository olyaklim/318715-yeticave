<?php
session_start();

require_once('functions.php');

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

// В сценарии главной страницы выполните подключение к MySQL
$con = mysqli_connect("localhost", "root", "","yeticave");

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка Подключения БД : ". $error);
    return;
}

// Отправьте SQL-запрос для получения всей информации по новым лотам
$sql_lot = "SELECT l.id, l.name, l.price, l.url_pictures, c.name as category FROM lots l "
   . " JOIN categories c "
   . " ON l.category_id = c.id "
   . " WHERE NOW() < l.dt_end "
   . " ORDER BY l.dt_add DESC Limit 6";

   if ($result_lot = mysqli_query($con, $sql_lot)) {
        $lots = mysqli_fetch_all($result_lot, MYSQLI_ASSOC);

        // передаем в шаблон результат выполнения HTML код главной страницы
        $page_content = renderTemplate('templates/index.php', ['lots' => $lots]);
    } else {
        $error = mysqli_error($con);
        $page_content = "";
        print("Ошибка MySQL: ". $error);
    }

// Отправьте SQL-запрос для получения списка категорий
$categories = getCategories($con);


// окончательный HTML код
$layout_content = renderTemplate('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>
