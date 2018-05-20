<?php
require_once('functions.php');

$is_auth     = false; //(bool) rand(0, 1);
$title_page  = 'Вход';
$user_name   = 'Константин';
$user_avatar = 'img/user.jpg';
$main_page = false;

// В сценарии главной страницы выполните подключение к MySQL
$con = mysqli_connect("localhost", "root", "","yeticave");

$categories = getCategories($con);

$page_content = renderTemplate('templates/login.php', ['categories' => $categories]);

// окончательный HTML код
$layout_content = renderTemplate('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>
