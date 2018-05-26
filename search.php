<?php
session_start();

require_once('mysql_helper.php');
require_once('functions.php');

$title_page  = 'Результаты поиска';
$main_page   = false;
$is_auth     = false;
$user_name   = '';
$user_avatar = '';

if (isset($_SESSION['user'])) {
  $is_auth     = true;
  $user_name   = $_SESSION['user']['name'];
  $user_avatar = $_SESSION['user']['avatar_path'];
}

// подключение к MySQL
$con = mysqli_connect("localhost", "root", "","yeticave");

if (!$con) {
  $error = mysqli_connect_error();
  print("Ошибка Подключения БД : ". $error);
  return;
}

$categories = get_categories($con);
$categories_id = get_id_categories($con);
$search     = htmlspecialchars(trim($_GET['search'])) ?? '';

// Пагинация
$cur_page = $_GET['page'] ?? 1;
$page_items = 9;

$sql = "SELECT COUNT(*) as cnt FROM lots l "
        . " JOIN categories c "
        . " ON l.category_id = c.id "
        . " WHERE NOW() < l.dt_end AND MATCH (l.name, l.description) AGAINST ('" . $search . "') "
        . " ORDER BY l.dt_add DESC Limit 6";

$result = mysqli_query($con, $sql);

$items_count = mysqli_fetch_assoc($result)['cnt'];
$pages_count = ceil($items_count / $page_items);
$offset = ($cur_page - 1) * $page_items;
$pages = range(1, $pages_count);

$lots = get_search_lots($con, $search, $offset);

if ($lots) {

    $tpl_data = [
      'pages' => $pages,
      'pages_count' => $pages_count,
      'cur_page' => intval($cur_page)
    ];

 // передаем в шаблон результат выполнения HTML код
 $page_content = render_template('templates/search.php', ['lots' => $lots, 'tpl_data' => $tpl_data, 'search'=> $search, 'categories' => $categories, 'categories_id' => $categories_id]);
} else {
  $page_content = "Ничего не найдено по вашему запросу";
}

// окончательный HTML код
$layout_content = render_template('templates/layout.php', ['main_section' => $page_content, 'search'=> $search, 'categories' => $categories, 'categories_id' => $categories_id, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' => $main_page]);
print($layout_content);

?>
