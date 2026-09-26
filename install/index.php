<?php

// ####################### SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

$enable_file = dirname(__FILE__) . '/install.enabled';
$used_file = dirname(__FILE__) . '/install.used';
if (is_file($used_file) || !is_file($enable_file)) {
    header('HTTP/1.1 403 Forbidden');
    exit('Installer is disabled.');
}
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Content-Type: text/html; charset=UTF-8');
    exit('<form method="post"><button type="submit">Run one-time installation</button></form>');
}
if (!@rename($enable_file, $used_file)) {
    header('HTTP/1.1 403 Forbidden');
    exit('Installer is disabled.');
}

require_once('./config.php');

$total = $db->sdImportFromFile('database.sql');

header('Refresh: 3; url=../signup.php');
echo "Установка завершена! Выполнено $total запросов к БД!<br />Теперь <font color=\"red\">Вам надо удалить папку install</font>.<br />Сейчас Вас переадресует на страницу регистрации, где Вы после регистрации будете Директором.<script>alert('Не забудьте удалить папку install после установки!');</script>";

?>