<?php
require_once('mysql_helper.php');
require_once('functions.php');

$is_auth     = false;
$title_page  = 'Регистрация';
$user_name   = 'Константин';
$user_avatar = 'img/user.jpg';
$main_page = false;

// В сценарии главной страницы выполните подключение к MySQL
$con = mysqli_connect("localhost", "root", "","yeticave");

$categories = get_categories($con);
$categories_id = get_id_categories($con);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST;

    $required = ['email', 'password', 'name', 'message'];
    $dict     = ['email' => 'Email', 'name' => 'Имя', 'password' => 'Пароль', 'message' => 'Контактные данные'];

    if (isset($user['email']) && !filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Это поле заполнено с ошибками';
    }

    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }

    if (isset($_FILES['user_img']['name']) && $_FILES['user_img']['name']) {
        $tmp_name = $_FILES['user_img']['tmp_name'];

        $filename = uniqid() . '.jpg';
        $user['path'] = $filename;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        if ($file_type !== "image/jpeg") {
            $errors['file'] = 'Загрузите картинку в формате JPG';
        } else {
            move_uploaded_file($tmp_name, 'img/' . $filename);
            $user['user_img'] = 'img/' . $filename;
        }
    }

    if ($user['email']) {
        $email = mysqli_real_escape_string($con, $user['email']);
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($con, $sql);

        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }
    }

    if ($errors) {
        $page_content = render_template('templates/sign-up.php', ['categories' => $categories, 'categories_id' => $categories_id, 'errors' => $errors, 'user' => $user]);
    } else {

        $password = password_hash($user['password'], PASSWORD_DEFAULT);

        $sql = 'INSERT INTO users (email, name, password_user, avatar_path, user_contact) VALUES (?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($con, $sql, [$user['email'], $user['name'], $password, $user['user_img'], $user['message']]);
        $res  = mysqli_stmt_execute($stmt);

        // Переадресовать пользователя на страницу входа, если не было ошибок.
        if ($res && empty($errors)) {
            header("Location: /login.php");
            exit();
        } else {
            http_response_code(503);
            print("Ошибка MySQL: ". mysqli_error($con));
        }
    }

} else {
    $page_content = render_template('templates/sign-up.php', ['user' => $user, 'categories' => $categories, 'categories_id' => $categories_id]);
}

// окончательный HTML код
$layout_content = render_template('templates/layout.php', ['main_section' => $page_content, 'categories' => $categories, 'categories_id' => $categories_id, 'is_auth' => $is_auth, 'user_avatar' => $user_avatar, 'title_page' => $title_page, 'user_name' => $user_name, 'main_page' =>$main_page]);
print($layout_content);

?>
