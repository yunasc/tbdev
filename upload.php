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

dbconn(false);

loggedinorreturn();
parked();

stdhead($tracker_lang['upload_torrent']);

if (get_user_class() < UC_UPLOADER)
{
  stdmsg($tracker_lang['error'], $tracker_lang['access_denied']);
  stdfoot();
  exit;
}

if (strlen($CURUSER['passkey']) != 32) {
$CURUSER['passkey'] = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
sql_query("UPDATE users SET passkey='$CURUSER[passkey]' WHERE id=$CURUSER[id]");
}

?>
<div align="center">
<p><span style="color: green; font-weight: bold;">После загрузки торрента, вам нужно будет скачать торрент и поставить качаться в папку где лежат оригиналы файлов.</span></p>
<form name="upload" enctype="multipart/form-data" action="takeupload.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_torrent_size?>" />
<table border="1" cellspacing="0" cellpadding="5">
<tr><td class="colhead" colspan="2"><?=$tracker_lang['upload_torrent'];?></td></tr>
<?
//tr($tracker_lang['announce_url'], $announce_urls[0], 1);
tr($tracker_lang['torrent_file'], "<input type=file name=tfile size=80>\n", 1);
tr($tracker_lang['torrent_name'], "<input type=\"text\" name=\"name\" size=\"80\" /><br />(".$tracker_lang['taken_from_torrent'].")\n", 1);
tr($tracker_lang['img_poster'], $tracker_lang['max_file_size'].": 500kb<br />".$tracker_lang['avialable_formats'].": .gif .jpg .png<br /><input type=file name=image0 size=80>\n", 1);
tr($tracker_lang['images'], $tracker_lang['max_file_size'].": 500kb<br />".$tracker_lang['avialable_formats'].": .gif .jpg .png<br />".
		"<b>".$tracker_lang['image']." №1:</b>&nbsp&nbsp<input type=file name=image1 size=80><br />".
		"<b>".$tracker_lang['image']." №2:</b>&nbsp&nbsp<input type=file name=image2 size=80><br />".
		"<b>".$tracker_lang['image']." №3:</b>&nbsp&nbsp<input type=file name=image3 size=80><br />".
		"<b>".$tracker_lang['image']." №4:</b>&nbsp&nbsp<input type=file name=image4 size=80>", 1);
print("<tr><td class=rowhead style='padding: 3px'>".$tracker_lang['description']."</td><td>");
textbbcode("upload","descr");
print("</td></tr>\n");

$s = "<select name=\"type\">\n<option value=\"0\">(".$tracker_lang['choose'].")</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars_uni($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr($tracker_lang['type'], $s, 1);

tr('Мультитрекер', '<input type="checkbox" value="yes" id="multi" name="multi" /><label for="multi">Мультитрекерный торрент</label>
	<br /><font class="small">Включение этой опции отключает установку private-флага и удаление других аннонсеров из файла</font>', 1);

tr('Keywords', '<input type="text" name="keywords" size="80" />', 1);
tr('Description', '<input type="text" name="description" size="80" />', 1);

if(get_user_class() >= UC_ADMINISTRATOR)
    tr("Тип раздачи",
    "<input type=\"radio\" name=\"free\" id=\"gold\" value=\"yes\" /><label for=\"gold\">Золотая раздача (считаеться только раздача, скачка не учитиваеться)</label><br />".
    "<input type=\"radio\" name=\"free\" id=\"silver\" value=\"silver\" /><label for=\"silver\">Серебряная раздача (скачка не учитиваеться только на 50%)</label><br />".
    "<input type=\"radio\" name=\"free\" id=\"no\" value=\"no\" checked /><label for=\"no\">Обычная раздача (скачка и раздача учитиваеться как обычно)</label><br />"
    , 1);

if (get_user_class() >= UC_ADMINISTRATOR)
    tr("Важный", "<input type=\"checkbox\" name=\"not_sticky\" value=\"no\">Прикрепить этот торрент (всегда наверху)", 1);

?>
<tr><td align="center" colspan="2"><input type="submit" class=btn value="<?=$tracker_lang['upload'];?>" /></td></tr>
</table>
</form>
</div>
<?

stdfoot();

?>