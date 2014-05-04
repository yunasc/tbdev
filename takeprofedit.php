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

function bark($msg) {
	stderr("Произошла ошибка", $msg);
}

dbconn();

loggedinorreturn();

if (!mkglobal("email:oldpassword:chpassword:passagain"))
	bark("missing form data");

// $set = array();

$updateset = array();
$changedemail = 0;

if ($chpassword != "") {
	if (strlen($chpassword) > 40)
		bark("Извините, ваш пароль слишком длинный (максимум 40 символов)");
	if ($chpassword != $passagain)
		bark("Пароли не совпадают. Попробуйте еще раз.");
    if ($CURUSER["passhash"] != md5($CURUSER["secret"] . $oldpassword . $CURUSER["secret"]))
            bark("Вы ввели неправильный старый пароль.");

	$sec = mksecret();
	$passhash = md5($sec . $chpassword . $sec);
	$updateset[] = "secret = " . sqlesc($sec);
	$updateset[] = "passhash = " . sqlesc($passhash);
	logincookie($CURUSER["id"], $passhash);
}

if ($email != $CURUSER["email"]) {
	if (!validemail($email))
		bark("Это не похоже на настоящий E-Mail.");
  $r = sql_query("SELECT id FROM users WHERE email=" . sqlesc($email)) or sqlerr(__FILE__, __LINE__);
	if (mysql_num_rows($r) > 0)
		bark("Этот e-mail адрес уже используется одним из пользователей трекера. (<b>$email</b>)");
	$changedemail = 1;
}

$acceptpms = $_POST["acceptpms"];
$deletepms = ($_POST["deletepms"] != "" ? "yes" : "no");
$savepms = ($_POST["savepms"] != "" ? "yes" : "no");
$pmnotif = $_POST["pmnotif"];
$emailnotif = $_POST["emailnotif"];
$notifs = ($pmnotif == 'yes' ? "[pm]" : "");
$notifs .= ($emailnotif == 'yes' ? "[email]" : "");
$r = sql_query("SELECT id FROM categories") or sqlerr(__FILE__, __LINE__);
$rows = mysql_num_rows($r);
for ($i = 0; $i < $rows; ++$i)
{
	$a = mysql_fetch_assoc($r);
	if ($_POST["cat$a[id]"] == 'yes')
	  $notifs .= "[cat$a[id]]";
}
$avatar = $_POST["avatar"];
// Check remote avatar size
        if ($avatar) {
                if (!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $avatar))
                        stderr($tracker_lang['error'], $tracker_lang['avatar_adress_invalid']);
                if(!(list($width, $height) = getimagesize($avatar)))
                        stderr($tracker_lang['error'], $tracker_lang['avatar_adress_invalid']);
                if ($width > $avatar_max_width || $height > $avatar_max_height)
                        stderr($tracker_lang['error'], sprintf($tracker_lang['avatar_is_too_big'], $avatar_max_width, $avatar_max_height));
        }
// Check remote avatar size
$avatars = ($_POST["avatars"] != "" ? "yes" : "no");
$parked = $_POST["parked"];
$updateset[] = "parked = " . sqlesc($parked);
$gender = $_POST["gender"];
$updateset[] = "gender =  " . sqlesc($gender);

///////////////// BIRTHDAY MOD /////////////////////
$year = $_POST["year"];
$month = $_POST["month"];
$day = $_POST["day"];
$birthday = date("$year.$month.$day");
///////////////// BIRTHDAY MOD /////////////////////
$updateset[] = "birthday = " . sqlesc($birthday);

if ($_POST['resetpasskey'])
	$updateset[] = "passkey=''";

$updateset[] = "passkey_ip = ".($_POST["passkey_ip"] != "" ? sqlesc(getip()) : "''");

// $ircnick = $_POST["ircnick"];
// $ircpass = $_POST["ircpass"];
$info = $_POST["info"];
$theme = $_POST["theme"];
$country = $_POST["country"];
$language = $_POST["language"];
if (!file_exists('./languages/lang_'.$language.'/lang_main.php')) {
    bark('Выбранный язык в системе отсутствует!');
}
$updateset[] = "language = " . sqlesc($language);
//$timezone = 0 + $_POST["timezone"];
//$dst = ($_POST["dst"] != "" ? "yes" : "no");

$icq = (int) unesc($_POST["icq"]);
if (strlen($icq) > 10)
    bark("Жаль, Номер icq слишком длинный  (Макс - 10)");
$updateset[] = "icq = " . sqlesc($icq);

$msn = unesc($_POST["msn"]);
if (strlen($msn) > 30)
    bark("Жаль, Ваш msn слишком длинный  (Макс - 30)");
$updateset[] = "msn = " . sqlesc(htmlspecialchars_uni($msn));

$aim = unesc($_POST["aim"]);
if (strlen($aim) > 30)
    bark("Жаль, Ваш aim слишком длинный  (Макс - 30)");
$updateset[] = "aim = " . sqlesc(htmlspecialchars_uni($aim));

$yahoo = unesc($_POST["yahoo"]);
if (strlen($yahoo) > 30)
    bark("Жаль, Ваш yahoo слишком длинный  (Макс - 30)");
$updateset[] = "yahoo = " . sqlesc(htmlspecialchars_uni($yahoo));

$mirc = unesc($_POST["mirc"]);
if (strlen($mirc) > 30)
    bark("Жаль, Ваш mirc слишком длинный  (Макс - 30)");
$updateset[] = "mirc = " . sqlesc(htmlspecialchars_uni($mirc));

$skype = unesc($_POST["skype"]);
if (strlen($skype) > 20)
    bark("Жаль, Ваш skype слишком длинный  (Макс - 20)");
$updateset[] = "skype = " . sqlesc(htmlspecialchars_uni($skype));

/*
if ($privacy != "normal" && $privacy != "low" && $privacy != "strong")
	bark("whoops");

$updateset[] = "privacy = '$privacy'";
*/

$website = unesc($_POST["website"]);
$updateset[] = "website = " . sqlesc(htmlspecialchars_uni($website));

$updateset[] = "torrentsperpage = " . min(100, intval($_POST["torrentsperpage"]));
$updateset[] = "topicsperpage = " . min(100, intval($_POST["topicsperpage"]));
$updateset[] = "postsperpage = " . min(100, intval($_POST["postsperpage"]));

if (is_theme($theme))
	$updateset[] = "theme = ".sqlesc($theme);
if (is_valid_id($country))
	$updateset[] = "country = $country";

//$updateset[] = "timezone = $timezone";
//$updateset[] = "dst = '$dst'";
$updateset[] = "info = " . sqlesc($info);
$updateset[] = "acceptpms = " . sqlesc($acceptpms);
$updateset[] = "deletepms = '$deletepms'";
$updateset[] = "savepms = '$savepms'";
$updateset[] = "notifs = '$notifs'";
$updateset[] = "avatar = " . sqlesc($avatar);
$updateset[] = "avatars = '$avatars'";

/* ****** */

$urladd = "";

if ($changedemail) {
	$sec = mksecret();
	$hash = md5($sec . $email . $sec);
	$obemail = urlencode($email);
	$updateset[] = "editsecret = " . sqlesc($sec);
	$thishost = $_SERVER["HTTP_HOST"];
	$thisdomain = preg_replace('/^www\./is', "", $thishost);
	$body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on $thisdomain should be updated with this email address ($email) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To complete the update of your user profile, please follow this link:

http://$thishost/confirmemail.php?id={$CURUSER["id"]}&hash=$hash&email=$obemail

If you have AOL browser, please click the following link:
<a href="http://$thishost/confirmemail.php?id={$CURUSER["id"]}&amp;hash=$hash&amp;email=$obemail">http://$thishost/confirmemail.php?id={$CURUSER["id"]}&amp;hash=$hash&amp;email=$obemail</a>

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

	sent_mail($email, $SITENAME, $SITEEMAIL, "Изменение настроек профиля на $thisdomain", $body, false);
//	mail($email, "$thisdomain profile change confirmation", $body, "From: $SITEEMAIL");
	$urladd .= "&mailsent=1";
}

sql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]) or sqlerr(__FILE__,__LINE__);

header("Location: $DEFAULTBASEURL/my.php?edited=1" . $urladd);

?>