<?php

// В сценарии главной страницы выполните подключение к MySQL
$con = mysqli_connect("localhost", "root", "","yeticave");

if (!$con) {
    $error = mysqli_connect_error();
    print("Ошибка Подключения БД : ". $error);
    return;
}

?>
