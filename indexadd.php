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
loggedinorreturn();
if (get_user_class() < UC_MODERATOR)
	stderr($tracker_lang["error"], $tracker_lang["access_denied"]);

$types = array(
	"notemplate" => array("type" => "notemplate", "name" => "Без шаблона"),
	"video" => array("type" => "video", "name" => "Видео"),
	"games" => array("type" => "games", "name" => "Игры"),
	"music" => array("type" => "music", "name" => "Музыка"),
	"soft" => array("type" => "soft", "name" => "Программы"),
);

$templates = array(
	"notemplate" => array("toptemplate" => "", "centertemplate" => "", "bottomtemplate" => ""),
	"video" => array("toptemplate" => "[b]Жанр:[/b] \n[b]Режиссер:[/b] \n[b]В ролях:[/b] ", "centertemplate" => "[b]О фильме:[/b] ", "bottomtemplate" => "[b]Качество:[/b] \n[b]Видео:[/b] \n[b]Аудио:[/b] \n[b]Продолжительность:[/b] \n[b]Язык:[/b] \n[b]Перевод:[/b] "),
	"music" => array("toptemplate" => "[b]Исполнитель:[/b] \n[b]Альбом:[/b] \n[b]Год выпуска:[/b] \n[b]Стиль:[/b] ", "centertemplate" => "[b]Треклист:[/b] ", "bottomtemplate" => "[b]Звук:[/b] \n[b]Продолжительность:[/b] "),
	"games" => array("toptemplate" => "[b]Название:[/b] \n[b]Производитель:[/b] \n[b]Жанр:[/b] \n[b]Год выпуска:[/b] ", "centertemplate" => "[b]Описание:[/b] ", "bottomtemplate" => "[b]Системные требования:[/b] \n[b]Скрины:[/b] "),
	"soft" => array("toptemplate" => "[b]Название:[/b] \n[b]Производитель:[/b] \n[b]Год выпуска:[/b] ", "centertemplate" => "[b]Описание:[/b] ", "bottomtemplate" => "[b]Системные требования:[/b] "),
);

if (empty($_GET["type"])) {
stdhead("Выберите тип релиза");
?>
<form action="indexadd.php" method="get">
	<table border="1" cellspacing="0" cellpadding="3" width="20%">
	<tr><td class="heading" align="right">Тип</td><td>
	<select name="type">
<?
	foreach ($types as $type)
		print("<option value=\"" . $type["type"] . "\">" . $type["name"] . "</option>");
?>
	</select>
	</td></tr>
	<tr><td align="center" colspan="2"><input type="submit" class=btn value="Дальше"></td></tr>
	</table>
</form>
<?
stdfoot();
die;
} else
	$type = $_GET["type"];

stdhead("Добавить релиз - ".$types[$type]["name"]);

$cats = genrelist();
$categories = "<select name=\"cat\"><option selected>Выберите категорию</option>";
foreach ($cats as $cat) {
	$cat_id = $cat["id"];
	$cat_name = $cat["name"];
	$categories .= "<option value=\"$cat_id\">$cat_name</option>";
}
$categories .= "</select>";

?>

<form name="index" action="takeindex.php" method="post">
<table border="0" cellspacing="0" cellpadding="5">
<tr><td class="colhead" colspan="2">Выбранный шаблон: <?=$types[$type]["name"];?></td></tr>
<?
tr("Название релиза", "<input type=\"text\" name=\"name\" size=\"80\" /><br />Пример: Смерть Президента (2006) DVDRip\n", 1);
tr("Постер", "<input type=\"text\" name=\"poster\" size=\"80\" /><br />Залить картинку на <a href=\"http://www.imageshack.us\">ImageShack</a>", 1);
?>
<tr><td width="" class="heading" valign="top" align="right">Верхний шаблон</td><td valign="top" align="left"><?=textbbcode("index", "top", $templates[$type]["toptemplate"]);?></td></tr>
<tr><td width="" class="heading" valign="top" align="right">Средний шаблон</td><td valign="top" align="left"><?=textbbcode("index", "center", $templates[$type]["centertemplate"]);?></td></tr>
<tr><td width="" class="heading" valign="top" align="right">Нижний шаблон</td><td valign="top" align="left"><?=textbbcode("index", "bottom", $templates[$type]["bottomtemplate"]);?></td></tr>
<?
tr("Номер торрента", "<input type=\"text\" name=\"torrentid\" size=\"60\" /><br />Пример: $DEFAULTBASEURL/details.php?id=<b>6764</b><br />Выделенное жирным - и есть номер торрента\n", 1);
tr("URL IMDB", "<input type=\"text\" name=\"imdb\" size=\"60\" /><br />Пример: http://www.imdb.com/title/tt0408306/\n", 1);
tr("Категория", $categories, 1);
?>
<tr><td align="center" colspan="2"><input type="submit" value="Добавить" /></td></tr>
</table>
</form>

<?
stdfoot();
?>