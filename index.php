<?php
require_once('functions.php');

// В сценарии главной страницы выполните подключение к MySQL
$con = mysqli_connect("localhost", "root", "","yeticave");

if (!$con) {
    $error = mysqli_connect_error();
    var_dump("Ошибка Подключения БД : ". $error);
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
        var_dump("Ошибка MySQL: ". $error);
    }

// Отправьте SQL-запрос для получения списка категорий
$sql ="SELECT name FROM categories";

$result = mysqli_query($con, $sql);
$categories = [];

if (!$result) {
    $error = mysqli_error($con);
    var_dump("Ошибка MySQL: ". $error);
} else {

    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($rows as $row) {
        $categories[] = $row['name'];
    }
}

$is_auth     = (bool) rand(0, 1);
$title_page  = 'Главная';
$user_name   = 'Константин';
$user_avatar = 'img/user.jpg';


// окончательный HTML код
$layout_content = renderTemplate('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name]);
print($layout_content);

?>
