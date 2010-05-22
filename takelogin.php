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

if (!mkglobal("username:password"))
	die();

dbconn();

function bark($text = "Имя пользователя или пароль неверны")
{
  stderr("Ошибка входа", $text);
}

function is_password_correct($password, $secret, $hash) {
	return ($hash == md5($secret . $password . $secret) || $hash == md5($secret . trim($password) . $secret)); // А нахуя вторая часть? Дебилы вводят из писем пароли с пробелом в конце/начале
}

$res = sql_query("SELECT id, passhash, secret, enabled, status FROM users WHERE username = " . sqlesc($username));
$row = mysql_fetch_array($res);

if (!$row)
	bark("Вы не зарегистрированы в системе.");

if ($row["status"] == 'pending')
	bark("Вы еще не активировали свой аккаунт! Активируйте ваш аккаунт и попробуйте снова.");

if (!is_password_correct($password, $row['secret'], $row['passhash']))
	bark();

if ($row["enabled"] == "no")
	bark("Этот аккаунт отключен.");

$peers = sql_query("SELECT COUNT(id) FROM peers WHERE userid = $row[id]");
$num = mysql_fetch_row($peers);
$ip = getip();
if ($num[0] > 0 && $row[ip] != $ip && $row[ip])
	bark("Этот пользователь на данный момент активен с другого IP. Вход невозможен.");

logincookie($row["id"], $row["passhash"]);

if (!empty($_POST["returnto"]))
	header("Location: $DEFAULTBASEURL/$_POST[returnto]");
else
	header("Location: $DEFAULTBASEURL/");

?>