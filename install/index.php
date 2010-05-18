<?php

// ####################### SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE);

require_once('./config.php');

$total = $db->sdImportFromFile('database.sql');

header('Refresh: 3; url=../signup.php');
echo "Установка завершена! Выполнено $total запросов к БД!<br />Теперь <font color=\"red\">Вам надо удалить папку install</font>.<br />Сейчас Вас переадресует на страницу регистрации, где Вы после регистрации будете Директором.<script>alert('Не забудьте удалить папку install после установки!');</script>";

?>