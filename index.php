<?php

$icon_path = "image/ico.png";


function format_device($params) {
    global $so_pin;
    $so_pin = "87654321";
    [$new_so_pin, $new_user_pin, $rutoken_label, $min_so_pin, $min_user_pin, $max_so_pin_retry_count, $max_user_pin_retry_count] = explode('|', $params);

    $command = "sudo ./kernel/rtadmin format -p \"$so_pin\" --new-user-pin \"$new_user_pin\" --new-so-pin \"$new_so_pin\" -l \"$rutoken_label\" --min-so-pin \"$min_so_pin\" --min-user-pin \"$min_user_pin\" --max-so-pin-retry-count \"$max_so_pin_retry_count\" --max-user-pin-retry-count \"$max_user_pin_retry_count\" --pin-change-policy so";

    if (exec($command, $output, $return_var) && $return_var === 0) {
        return "Форматирование прошло успешно.";
    } else {
        return "Ошибка при форматировании.";
    }
}

function set_user_pin($so_pin, $user_pin) {
    $command = "sudo ./kernel/rtadmin set-user-pin -p \"$so_pin\" -n \"$user_pin\" --auth-as so";

    if (exec($command, $output, $return_var) && $return_var === 0) {
        return "PIN пользователя установлен успешно.";
    } else {
        return "Ошибка при установке PIN пользователя.";
    }
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set_user_pin'])) {
        $so_pin = $_POST['so_pin'];
        $user_pin = $_POST['user_pin'];
        $message = set_user_pin($so_pin, $user_pin);
    } elseif (isset($_POST['format_device'])) {
        $params = $_POST['params'];
        $message = format_device($params);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ Рутокен</title>
    <link rel="icon" href="<?php echo $icon_path; ?>">
</head>
<body>
    <h1>Админ Рутокен</h1>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <h2>Установить PIN пользователя</h2>
    <form method="post">
        <label for="so_pin">PIN админа:</label>
        <input type="password" id="so_pin" name="so_pin" required><br>
        <label for="user_pin">PIN пользователя:</label>
        <input type="password" id="user_pin" name="user_pin" required><br>
        <button type="submit" name="set_user_pin">Установить PIN</button>
    </form>

    <h2>Форматирование устройства</h2>
    <form method="post">
        <label for="params">Введите параметры (разделенные '|'):</label><br>
        <textarea id="params" name="params" rows="5" required></textarea><br>
        <button type="submit" name="format_device">Форматировать</button>
    </form>
</body>
</html>
