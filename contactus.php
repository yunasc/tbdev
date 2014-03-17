<?php

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

require_once('include/bittorrent.php');
dbconn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if ($use_captcha) {
		$b = get_row_count('captcha', 'WHERE imagehash = '.sqlesc($_POST['imagehash']).' AND imagestring = '.sqlesc($_POST['imagestring']));
		sql_query('DELETE FROM captcha WHERE imagehash = '.sqlesc($_POST['imagehash'])) or die(mysql_error());
		if ($b == 0)
			stderr($tracker_lang['error'], 'Вы ввели неправильный код подтверждения.');
	}

	if (!mkglobal('useremail:subject:message'))
		stderr($tracker_lang['error'], 'Вы не заполнили все поля формы! Вернитесь назад и попробуйте еще раз.');

	if (!validemail($useremail))
		stderr($tracker_lang['error'], 'Это не похоже на реальный email адрес.');

$ip = getip();
$username = $CURUSER['username'] ? $CURUSER['username'] : 'unregged';
if ($CURUSER)
	$userid = $CURUSER['id'];
else
	$userid = 0;

$body = <<<EOD
Сообщение через обратную связь на {$website_name}:

--------------------------------

$message

--------------------------------

IP Адрес: {$ip}
Имя пользователя: {$username}
Код пользователя: {$userid}
EOD;

	stdhead();
	if (sent_mail($admin_email, $useremail, $useremail, 'Обратная связь на '.$website_name.' - ' . $subject, $body, false))
		stdmsg('Успешно', 'Ваше сообщение отправлено администрации.', 'success');
	else
		stdmsg('Ошибка', 'Ваше сообщение <b>НЕ</b> было отправлено администрации, из-за непредвиденной ошибки сервера.', 'error');
	stdfoot();

} else {
	stdhead('Связаться с нами');
	if ($use_captcha) {
		include_once("include/captcha.php");
		$hash = create_captcha();
	}
?>
<form method="post" name="contactus" action="contactus.php" onsubmit="document.contactus.cbutton.value='Пожалуйста подождите ...';document.contactus.cbutton.disabled=true">
<input type="hidden" name="do" value="process">
<table class="main" border="1" cellspacing="0" cellpadding="5" width="100%">
	<tr>
		<td align="left" class="colhead" colspan="2">
			Связаться с нами
		</td>
	</tr>
	<tr>
		<td align="right" width="20%" valign="top">
			<b>Ваш Email:</b>
		</td>
		<td align="left" width="80%" valign="top">
			<input type="text" name="useremail" value="" size="30">
		</td>
	</tr>
	<tr>
		<td align="right" width="20%" valign="top">
			<b>Тема:</b>
		</td>
		<td align="left" width="80%" valign="top">
			<input type="text" name="subject" value="" size="30">
		</td>
	</tr>	
	<tr>
		<td align="right" width="20%" valign="top">
			<b>Сообщение:</b>
		</td>
		<td align="left" width="80%" valign="top">
			<textarea name="message" cols="100" rows="10"></textarea>
		</td>
	</tr>
<? if ($use_captcha) { ?>
	<tr>
		<td align="right" width="20%" valign="top">
			<b>Код безопасности:</b>
		</td>
		<td align="left" width="80%" valign="top">
			<input type="text" name="imagestring" value="" size="30">
			<p>Пожалуйста, введите текст изображенный на картинке внизу.<br />Этот процесс предотвращает автоматическую регистрацию.</p>
			<img id="captcha" src="captcha.php?imagehash=<?=$hash; ?>" alt="Captcha" ondblclick="document.getElementById('captcha').src = 'captcha.php?imagehash=<?=$hash; ?>&amp;' + Math.random();" /><br />
			<font color="red">Код чувствителен к регистру</font><br />
			Кликните два раза на картинке, что-бы обновить картинку.
			<input type="hidden" name="imagehash" value="<?=$hash; ?>" />
		</td>
	</tr>
<? } ?>
	<tr>
		<td colspan="2" align="center">
			<input type="submit" value="Отправить" name="cbutton">
			<input type="reset" value="Сбросить">
		</td>
	</tr>
</table>
</form>
<?
	stdfoot();
}

?>