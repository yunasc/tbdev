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

require_once("include/bittorrent.php");
dbconn(true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $choice = (int) $_POST["choice"];  
  if ($CURUSER && $choice >= 0 && $choice < 256) {
    $res = sql_query("SELECT * FROM polls ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res) or die("Ќет опроса");
    $pollid = $arr["id"];
    $userid = $CURUSER["id"];
    $res = sql_query("SELECT * FROM pollanswers WHERE pollid=$pollid && userid=$userid") or sqlerr(__FILE__, __LINE__);
    $arr = mysql_fetch_assoc($res);
    if ($arr) die("ƒвойной голос");
    sql_query("INSERT INTO pollanswers VALUES(0, $pollid, $userid, $choice)") or sqlerr(__FILE__, __LINE__);
    if (mysql_affected_rows() != 1)
      stderr($tracker_lang['error'], "ѕроизошла ошибка. ¬аш голос не был прин€т.");
    header("Location: $DEFAULTBASEURL/");
    die;
  } else
    stderr($tracker_lang['error'], "ѕожалуйста, выберите вариант ответа.");
}

stdhead($tracker_lang['homepage']);

//print("<table width=\"100%\" class=\"main\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"embedded\">");

?>

<div align="center"><font class="small"><img src="./themes/<?=$ss_uri;?>/images/ru.gif" width="20" height="15"></font></div>
<p align="justify"><font class="small" style="font-weight: normal;">ѕредупреждение! »нформаци€, расположенна€ на данном сервере, предназначена исключительно дл€ частного использовани€ в образовательных цел€х и не может быть загружена/перенесена на другой компьютер. Ќи владелец сайта, ни хостинг-провайдер, ни любые другие физические или юридические лица не могут нести никакой отвественности за любое использование материалов данного сайта. ¬ход€ на сайт, ¬ы, как пользователь, тем самым подтверждаете полное и безоговорочное согласие со всеми услови€ми использовани€. јвторы проекта относ€тс€ особо негативно к нелегальному использованию информации, полученной на сайте.</font></p>
<div align="center"><font class="small"><img src="./themes/<?=$ss_uri;?>/images/en.gif" width="20" height="15"></font></div>
<p align="justify"><font class="small" style="font-weight: normal;">No files you see here are hosted on the server. Links available are provided by site users and administation is not responsible for them. It is strictly prohibited to upload any copyrighted material without explicit permission from copyright holders. If you find that some content is abusing you feel free to contact administation.</font></p>

<!--</td></tr></table>-->

<?php
stdfoot();
?>