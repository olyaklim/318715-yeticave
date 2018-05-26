<?php

session_start();

require_once('functions.php');

$is_auth     = false;
$title_page  = 'Вход';
$user_name   = 'Константин';
$user_avatar = 'img/user.jpg';
$main_page   = false;
$errors      = [];

// В сценарии главной страницы выполните подключение к MySQL
$con = mysqli_connect("localhost", "root", "", "yeticave");
$categories = get_categories($con);
$categories_id = get_id_categories($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;

    $required = ['email', 'password'];

    if (isset($form['email']) && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Это поле заполнено с ошибками';
    }

    foreach ($required as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }

    $email = mysqli_real_escape_string($con, $form['email']);

    $sql = "SELECT * FROM users WHERE email = '" .$email. "' ";
    $res = mysqli_query($con, $sql);
    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (!count($errors) and $user) {
        if (password_verify($form['password'], $user['password_user'])) {
            $_SESSION['user'] = $user;
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    } else {
        if (!isset($errors['email'])) {
            $errors['email'] = 'Такой пользователь не найден';
        }
    }

    if (count($errors)) {
        $page_content = render_template('templates/login.php', ['form' => $form, 'errors' => $errors, 'categories' => $categories, 'categories_id' => $categories_id]);
    } else {
        header("Location: /index.php");
        exit();
    }
} else {

    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    } else {
       $page_content = render_template('templates/login.php', ['categories' => $categories, 'categories_id' => $categories_id]);
    }
}

// окончательный HTML код
$layout_content = render_template('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'categories_id' => $categories_id, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>
