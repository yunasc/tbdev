<?

/*
// +--------------------------------------------------------------------------+
// | Project:    TBDevYSE - TBDev Yuna Scatari Edition                        |
// +--------------------------------------------------------------------------+
// | This file is part of TBDevYSE. TBDevYSE is based on TBDev,               |
// | originally by RedBeard of TorrentBits, extensively modified by           |
// | Gartenzwerg.                                                             |
// |                                                                          |
// | TBDevYSE is free software; you can redistribute it and/or modify         |
// | it under the terms of the GNU General Public License as published by     |
// | the Free Software Foundation; either version 2 of the License, or        |
// | (at your option) any later version.                                      |
// |                                                                          |
// | TBDevYSE is distributed in the hope that it will be useful,              |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with TBDevYSE; if not, write to the Free Software Foundation,      |
// | Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA            |
// +--------------------------------------------------------------------------+
// |                                               Do not remove above lines! |
// +--------------------------------------------------------------------------+
*/

require "include/bittorrent.php";

dbconn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	if ($use_captcha) {
		$b = get_row_count("captcha", "WHERE imagehash = ".sqlesc($_POST["imagehash"], true)." AND imagestring = ".sqlesc($_POST["imagestring"], true));
		sql_query("DELETE FROM captcha WHERE imagehash = ".sqlesc($_POST["imagehash"], true)) or sqlerr(__FILE__,__LINE__);
		if ($b == 0)
			stderr("Ошибка", "Вы ввели неправильный код подтверждения.");
	}

	$email = trim($_POST["email"]);
	if (!$email)
		stderr($tracker_lang['error'], "Вы должны ввести email адрес");
	$res = sql_query("SELECT * FROM users WHERE email = " . sqlesc($email) . " LIMIT 1") or sqlerr(__FILE__, __LINE__);
	$arr = mysql_fetch_assoc($res) or stderr($tracker_lang['error'], "Email адрес не найден в базе данных.\n");

	$sec = mksecret();

	sql_query("UPDATE users SET editsecret = " . sqlesc($sec) . " WHERE id = " . $arr["id"]) or sqlerr(__FILE__, __LINE__);
	if (!mysql_affected_rows())
		stderr($tracker_lang['error'], "Ошибка базы данных. Свяжитесь с администратором относительно этой ошибки.");

	// Защита от козлов что ввели почту с разным регистром т.е не проходило восстановление пароля если у пользователя в базе почта например
	// Kabum@gmail.com
	// тогда как при восстановлении он вводил kabum@gmail.com
	// результатом было невозможность восстановить пароль
	$email = $arr['email'];
	$hash = md5($sec . $email . $arr["passhash"] . $sec);

	$body = <<<EOD
Вы, или кто-то другой, запросили новый пароль к аккаунту связаному с этим адресом ($email).

Запрос был послан человеком с IP адресом {$_SERVER["REMOTE_ADDR"]}.

Если это были не вы, проигнорируйте это письмо. Пожалуста не отвечайте.

Если вы подтверждаете этот запрос, перейдите по следующей ссылке:

$DEFAULTBASEURL/recover.php?id={$arr["id"]}&secret=$hash

После того как вы это сделаете, ваш пароль будет сброшен и новый пароль будет отправлен вам на E-Mail.

--
$SITENAME
EOD;

	sent_mail($arr["email"],$SITENAME,$SITEEMAIL,"Подтверждение восстановления пароля на $SITENAME",$body)
		or stderr($tracker_lang['error'], "Невозможно отправить E-mail. Пожалуста сообщите администрации об ошибке.");
	stderr($tracker_lang['success'], "Подтверждающее письмо было отправлено.\n" .
		" Через несколько минут (обычно сразу) вам прийдет письмо с дальнейшими указаниями.");
} elseif ($_GET) {
	$id = intval($_GET["id"]);
	$md5 = $_GET["secret"];
	if (!$id)
		httperr();
	$res = sql_query("SELECT username, email, passhash, editsecret FROM users WHERE id = $id");
	$arr = mysql_fetch_array($res) or httperr();
	$email = $arr["email"];
	$sec = $arr["editsecret"];
	if (preg_match('/^ *$/s', $sec))
		httperr();
	if ($md5 != md5($sec . $email . $arr["passhash"] . $sec))
		httperr();
	// generate new password;
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$newpassword = "";
	for ($i = 0; $i < 10; $i++)
		$newpassword .= $chars[mt_rand(0, strlen($chars) - 1)];
	$sec = mksecret();
	$newpasshash = md5($sec . $newpassword . $sec);
	sql_query("UPDATE users SET secret = " . sqlesc($sec) . ", editsecret = '', passhash= " . sqlesc($newpasshash) . " WHERE id = $id AND editsecret = " . sqlesc($arr["editsecret"]));

	if (!mysql_affected_rows())
		stderr($tracker_lang['error'], "Невозможно обновить данные пользователя. Пожалуста свяжитесь с администратором относительно этой ошибки.");

	$body = <<<EOD
По вашему запросу на восстановление пароля, вы сгенерировали вам новый пароль.

Вот ваши новые данные для этого аккаунта:

    Пользователь: {$arr["username"]}
    Пароль:       $newpassword

Вы можете войти на сайт тут: $DEFAULTBASEURL/login.php

--
$SITENAME
EOD;

	sent_mail($email,$SITENAME,$SITEEMAIL,"Данные аккаунта на $SITENAME",$body)
		or stderr($tracker_lang['error'], "Невозможно отправить E-mail. Пожалуста сообщите администрации об ошибке.");
	stderr($tracker_lang['success'], "Новые данные по аккаунту отправлены на E-Mail <b>$email</b>.\n" .
		"Через несколько минут (обычно сразу) вы получите ваши новые данные.");
} else {
 	stdhead("Восстановление пароля");
	?>
	<form method="post" action="recover.php">
	<table border="1" cellspacing="0" cellpadding="5">
	<tr><td class="colhead" colspan="2">Восстановление имени пользователя или пароля</td></tr>
	<tr><td colspan="2">Используйте форму ниже для востановления пароля<br /> и ваши данные будут отправлены вам на почту.<br /><br />
	Вы должны будете подтвердить запрос.</td></tr>
	<tr><td class="rowhead">Зарегистрированый email</td>
	<td><input type="text" size="40" name="email"></td></tr>
<?

if ($use_captcha) {
	include_once("include/captcha.php");
	$hash = create_captcha();
	tr("Код подтверждения", "<input type=\"text\" name=\"imagestring\" size=\"20\" value=\"\" />
	<p>Пожалуйста, введите текст изображенный на картинке внизу.<br />Этот процесс предотвращает автоматическую регистрацию.</p>
	<table>
		<tr>
			<td class=\"block\" rowspan=\"2\">
				<img id=\"captcha\" src=\"captcha.php?imagehash=$hash\" alt=\"Captcha\" ondblclick=\"document.getElementById('captcha').src = 'captcha.php?imagehash=$hash&amp;' + Math.random();\" />
			</td>
			<td class=\"block\"><img src=\"themes/$ss_uri/images/reload.gif\" style=\"cursor: pointer;\" onclick=\"document.getElementById('captcha').src = 'captcha.php?imagehash=$hash&amp;' + Math.random();\" /></td>
		</tr>
		<tr>
			<td class=\"block\"><a href=\"captcha_mp3.php?imagehash=$hash\"><img src=\"themes/$ss_uri/images/listen.gif\" style=\"cursor: pointer;\" border=\"0\" /></a></td>
		</tr>
	</table>
	<font color=\"red\">Код чувствителен к регистру</font><br />Кликните два раза на картинке, что-бы обновить картинку.<input type=\"hidden\" name=\"imagehash\" value=\"$hash\" />", 1);
}

?>
	<tr><td colspan="2" align="center"><input type="submit" value="Восстановить"></td></tr>
	</table>
	<?
	stdfoot();
}

?>