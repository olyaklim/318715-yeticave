<?php

// Определение победителя:
// Найти все лоты без победителей
$no_winners_lots = get_no_win_lots($con);

// Записать в лот победителем автора последней ставки
foreach ($no_winners_lots as $key => $value) {

    $res = update_winner($con, $value['user_id'], $value['id']);

    // Отправить победителю на email письмо – поздравление с победой
    if ($res) {

        $user_data = get_user_data($con, $value['user_id']);

        if (!$user_data) {
            continue;
        }

        // Конфигурация транспорта
        $transport = (new Swift_SmtpTransport('phpdemo.ru', 25))
            ->setUsername('keks@phpdemo.ru')
            ->setPassword('htmlacademy');

        // Создание Mailer
        $mailer = new Swift_Mailer($transport);

        // Создание сообщения
        $text_message = render_template('templates/email.php', ['user_name' => $user_data['name'], 'lot_id' => $value['id'], 'lot_name' => $value['name']]);

        $message = (new Swift_Message('Ваша ставка победила'))
            ->setTo([$user_data['email'] => $user_data['name']])
            ->setBody($text_message, 'text/html')
            ->setFrom(['keks@phpdemo.ru' => 'Yeticave']);

        // Отправка сообщения
        $result = $mailer->send($message);

    }

}
