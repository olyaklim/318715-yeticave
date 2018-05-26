<?php
session_start();
require_once('mysql_helper.php');
require_once('functions.php');

$title_page  = 'Все лоты';
$main_page   = false;
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

$active_category = intval($_GET['id_categ']) ?? '';
$categories_id = get_id_categories($con);

// Пагинация
$cur_page = $_GET['page'] ?? 1;
$page_items = 9;

$sql = "SELECT COUNT(*) as cnt FROM lots l "
  . " JOIN categories c "
  . " ON l.category_id = c.id "
  . " WHERE NOW() < l.dt_end AND l.category_id = '" . $active_category . "' "
  . " ORDER BY l.dt_add DESC";

$result = mysqli_query($con, $sql);

$items_count = mysqli_fetch_assoc($result)['cnt'];
$pages_count = ceil($items_count / $page_items);
$offset = ($cur_page - 1) * $page_items;
$pages = range(1, $pages_count);

// Отправьте SQL-запрос для получения всей информации по новым лотам категории
$sql_lot = "SELECT l.id, l.name, l.price, l.url_pictures,l.dt_end, c.name as category FROM lots l "
  . " JOIN categories c "
  . " ON l.category_id = c.id "
  . " WHERE NOW() < l.dt_end AND l.category_id = '" . $active_category . "' "
  . " ORDER BY l.dt_add DESC Limit 9 OFFSET " . $offset ."";

if ($result_lot = mysqli_query($con, $sql_lot)) {
  $lots = mysqli_fetch_all($result_lot, MYSQLI_ASSOC);

  if ($lots) {
    $tpl_data = [
      'pages' => $pages,
      'pages_count' => $pages_count,
      'cur_page' => intval($cur_page)
    ];

    // передаем в шаблон результат выполнения HTML код главной страницы
    $page_content = render_template('templates/all-lots.php', ['lots' => $lots, 'tpl_data' => $tpl_data, 'categories_id' => $categories_id, 'active_category' => $active_category]);
  } else {
    $page_content = "Лоты по категории не найдены";
  }

} else {
  $error = mysqli_error($con);
  $page_content = "";
  print("Ошибка MySQL: ". $error);
}

// Отправьте SQL-запрос для получения списка категорий
$categories = get_categories($con);

// окончательный HTML код
$layout_content = render_template('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'categories_id' => $categories_id, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>
