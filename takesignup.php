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

require_once("include/bittorrent.php");

dbconn();

if ($deny_signup && !$allow_invite_signup)
	stderr($tracker_lang['error'], $tracker_lang['signup_disabled']);

if ($CURUSER)
	stderr($tracker_lang['error'], sprintf($tracker_lang['signup_already_registered'], $SITENAME));

$users = get_row_count("users");
if ($users >= $maxusers)
	stderr($tracker_lang['error'], sprintf($tracker_lang['signup_users_limit'], number_format($maxusers)));

if (!mkglobal("wantusername:wantpassword:passagain:email"))
	stderr($tracker_lang['error'], $tracker_lang['dad']);

if ($deny_signup && $allow_invite_signup) {
	if (empty($_POST["invite"]))
		stderr($tracker_lang['error'], "Для регистрации вам нужно ввести код приглашения!");
	if (strlen($_POST["invite"]) != 32)
		stderr($tracker_lang['error'], "Вы ввели не правильный код приглашения");
	list($inviter) = mysql_fetch_row(sql_query("SELECT inviter FROM invites WHERE invite = ".sqlesc($_POST["invite"])));
	if (!$inviter)
		stderr($tracker_lang['error'], "Код приглашения введенный вами не рабочий");
	list($invitedroot) = mysql_fetch_row(sql_query("SELECT invitedroot FROM users WHERE id = $inviter"));
}

function bark($msg) {
	global $tracker_lang;
	stdhead();
	stdmsg($tracker_lang['error'], $msg, 'error');
	stdfoot();
	exit;
}

function validusername($username)
{
	if ($username == "")
	  return false;

	// The following characters are allowed in user names
	$allowedchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_".
		"абвгдеёжзиклмнопрстуфхшщэюяьъАБВГДЕЁЖЗИКЛМНОПРСТУФХШЩЭЮЯЬЪ";

	for ($i = 0; $i < strlen($username); ++$i)
	  if (strpos($allowedchars, $username[$i]) === false)
	    return false;

	return true;
}

$gender = $_POST["gender"];
$website = htmlspecialchars_uni($_POST["website"]);
$country = $_POST["country"];
$year = $_POST["year"];
$month = $_POST["month"];
$day = $_POST["day"];

$icq = unesc($_POST["icq"]);
if (strlen($icq) > 10)
    bark("Жаль, Номер icq слишком длинный (Макс - 10)");

$msn = unesc($_POST["msn"]);
if (strlen($msn) > 30)
    bark("Жаль, Ваш msn слишком длинный (Макс - 30)");

$aim = unesc($_POST["aim"]);
if (strlen($aim) > 30)
    bark("Жаль, Ваш aim слишком длинный (Макс - 30)");

$yahoo = unesc($_POST["yahoo"]);
if (strlen($yahoo) > 30)
    bark("Жаль, Ваш yahoo слишком длинный (Макс - 30)");

$mirc = unesc($_POST["mirc"]);
if (strlen($mirc) > 30)
    bark("Жаль, Ваш mirc слишком длинный (Макс - 30)");

$skype = unesc($_POST["skype"]);
if (strlen($skype) > 20)
    bark("Жаль, Ваш skype слишком длинный (Макс - 20)");

$email = trim(strtolower($email));

if (empty($wantusername) || empty($wantpassword) || empty($email) || empty($gender) || empty($country))
	bark("Все поля обязательны для заполнения.");

if (strlen($wantusername) > 12)
	bark("Извините, имя пользователя слишком длинное (максимум 12 символов)");

if ($wantpassword != $passagain)
	bark("Пароли не совпадают! Похоже вы ошиблись. Попробуйте еще.");

if (strlen($wantpassword) < 6)
	bark("Извините, пароль слишком коротки (минимум 6 символов)");

if (strlen($wantpassword) > 40)
	bark("Извините, пароль слишком длинный (максимум 40 символов)");

if ($wantpassword == $wantusername)
	bark("Извините, пароль не может быть такой-же как имя пользователя.");

if (!validemail($email))
	bark("Это не похоже на реальный email адрес.");

list(, $domain) = explode('@', $email);
if (!mail_possible($email))
        bark('Почты в таком домене быть не может ('.htmlspecialchars_uni($domain).')');

if ($check_for_working_mta) {
	// Some fucking pre PHP 5.3 WINDOWS installations...
	if (function_exists('getmxrr')) {
		getmxrr($domain, $mxs);
		foreach ($mxs as $mx) {
			if (check_port($mx, 25, 1, true)) {
				$is_good_smtp = true;
				break;
			}
		}

		if (!$is_good_smtp)
			bark("На вашей почтовой службе не работает почтовый сервер (MTA).");
	}
}

if (!validusername($wantusername))
	bark("Неверное имя пользователя.");

if ($year == '0000' || $month == '00' || $day == '00')
        stderr($tracker_lang['error'], "Похоже вы указали неверную дату рождения");
	$birthday = date("$year.$month.$day");

// make sure user agrees to everything...
if ($_POST["rulesverify"] != "yes" || $_POST["faqverify"] != "yes" || $_POST["ageverify"] != "yes")
	stderr($tracker_lang['error'], "Извините, вы не подходите для того что-бы стать членом этого сайта.");

// check if email addy is already in use
/*$a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*) FROM users WHERE email=".sqlesc($email)))) or die(mysql_error());
if ($a[0] != 0)
	bark("E-mail адрес ".htmlspecialchars($email)." уже зарегистрирован в системе.");*/

$a = get_row_count('users', 'WHERE email = '.sqlesc($email));
if ($a != 0)
	bark("E-mail адрес ".htmlspecialchars($email)." уже зарегистрирован в системе.");

if ($use_captcha && $users) {
	if (!$_POST['imagestring'])
		bark("Вы должны ввести код подтверждения.");
	$b = get_row_count("captcha", "WHERE imagehash = ".sqlesc($_POST["imagehash"])." AND imagestring = ".sqlesc($_POST["imagestring"]));
	sql_query("DELETE FROM captcha WHERE imagehash = ".sqlesc($_POST["imagehash"])) or die(mysql_error());
	if ($b == 0)
		bark("Вы ввели неправильный код подтверждения.");
}

$ip = getip();

if (isset($_COOKIE[COOKIE_UID]) && is_numeric($_COOKIE[COOKIE_UID]) && $users && $enable_adv_antidreg) {
    $cid = intval($_COOKIE[COOKIE_UID]);
    $c = sql_query("SELECT enabled FROM users WHERE id = $cid ORDER BY id DESC LIMIT 1");
    $co = @mysql_fetch_row($c);
    if ($co[0] == 'no') {
		sql_query("UPDATE users SET ip = '$ip', last_access = NOW() WHERE id = $cid");
		bark("Ваш IP забанен на этом трекере. Регистрация невозможна.");
    } else
		bark("Регистрация невозможна!");
} else {
    $b = (@mysql_fetch_row(@sql_query("SELECT enabled, id FROM users WHERE ip = '$ip' ORDER BY last_access DESC LIMIT 1")));
    if ($b[0] == 'no') {
		$banned_id = $b[1];
        setcookie(COOKIE_UID, $banned_id, "0x7fffffff", "/");
		bark("Ваш IP забанен на этом трекере. Регистрация невозможна.");
    }
}

$secret = mksecret();
$wantpasshash = md5($secret . $wantpassword . $secret);
$editsecret = (!$users?"":mksecret());

if ((!$users) || (!$use_email_act == true))
	$status = 'confirmed';
else
	$status = 'pending';

$ret = sql_query("INSERT INTO users (username, passhash, secret, editsecret, gender, country, icq, msn, aim, yahoo, skype, mirc, website, email, status, ". (!$users?"class, ":"") ."added, birthday, invitedby, invitedroot, theme) VALUES (" .
		implode(",", array_map("sqlesc", array($wantusername, $wantpasshash, $secret, $editsecret, $gender, $country, $icq, $msn, $aim, $yahoo, $skype, $mirc, $website, $email, $status))).
		", ". (!$users?UC_SYSOP.", ":""). "'". get_date_time() ."', '$birthday', '$inviter', '$invitedroot', '".select_theme()."')");// or sqlerr(__FILE__, __LINE__);

if (!$ret) {
	if (mysql_errno() == 1062)
		bark("Пользователь $wantusername уже зарегистрирован!");
	bark("Неизвестная ошибка. Ответ от сервера mySQL: ".htmlspecialchars(mysql_error()));
}

$id = mysql_insert_id();

sql_query("DELETE FROM invites WHERE invite = ".sqlesc($_POST["invite"]));

write_log("Зарегистрирован новый пользователь $wantusername","FFFFFF","tracker");

$psecret = md5($editsecret);

$body = <<<EOD
Вы зарегистрировались на $SITENAME и указали этот адрес как обратный ($email).

Если это были не вы, пожалуйста проигнорируйте это письмо. Персона которая ввела ваш E-Mail адресс имеет IP адрес {$_SERVER["REMOTE_ADDR"]}. Пожалуйста, не отвечайте.

Для подтверждения вашей регистрации, вам нужно пройти по следующей ссылке:

$DEFAULTBASEURL/confirm.php?id=$id&secret=$psecret

После того как вы это сделаете, вы сможете использовать ваш аккаунт. Если вы этого не сделаете,
 ваш новый аккаунт будет удален через пару дней. Мы рекомендуем вам прочитать правила
и ЧаВо прежде чем вы начнете использовать $SITENAME.
EOD;

if($use_email_act && $users) {
	if (!sent_mail($email, $SITENAME, $SITEEMAIL, "Подтверждение регистрации на $SITENAME", $body, false)) {
		//stderr($tracker_lang['error'], "Невозможно отправить E-Mail. Попробуйте позже");
		write_log("Проблема с отправкой письма для активации на адрес $email","FF0000","errors");
		logincookie($id, $wantpasshash);
		sql_query('UPDATE users SET status = "confirmed" WHERE id = '.$id) or sqlerr();
		header("Location: ok.php?type=confirm");
		die;
	}
} else {
	logincookie($id, $wantpasshash);
}

header("Refresh: 0; url=ok.php?type=". (!$users?"sysop":("signup&email=" . urlencode($email))));

?>