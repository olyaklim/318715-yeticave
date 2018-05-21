<?php
require_once('functions.php');

// В сценарии главной страницы выполните подключение к MySQL
$con = mysqli_connect("localhost", "root", "","yeticave");

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка Подключения БД : ". $error);
    return;
}

// Отправьте SQL-запрос для получения списка категорий
$categories = getCategories($con);


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
  }
  else {
    $lots = mysqli_fetch_assoc($result_lot);

    // передаем в шаблон результат выполнения HTML код главной страницы
    $page_content = renderTemplate('templates/lot.php', ['lots' => $lots, 'categories' => $categories]);
  }

} else {
  $error = mysqli_error($con);
  $page_content = "";
  print("Ошибка MySQL: ". $error);
}


$is_auth     = (bool) rand(0, 1);
$title_page  = htmlspecialchars($lots['name']);
$user_name   = 'Константин';
$user_avatar = 'img/user.jpg';
$main_page = false;


// окончательный HTML код
$layout_content = renderTemplate('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>
