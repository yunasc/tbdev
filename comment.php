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

$action = $_GET["action"];

dbconn(false);

loggedinorreturn();
parked();

if ($action == "add")
{
  if ($_SERVER["REQUEST_METHOD"] == "POST")
  {
    $torrentid = intval($_POST["tid"]);
	  if (!is_valid_id($torrentid))
			stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
		$res = sql_query("SELECT name FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__,__LINE__);
		$arr = mysql_fetch_array($res);
		if (!$arr)
		  stderr($tracker_lang['error'], $tracker_lang['no_torrent_with_such_id']);
		$name = $arr[0];
	  $text = trim($_POST["text"]);
	  if (!$text)
			stderr($tracker_lang['error'], $tracker_lang['comment_cant_be_empty']);

	  sql_query("INSERT INTO comments (user, torrent, added, text, ori_text, ip) VALUES (" .
	      $CURUSER["id"] . ",$torrentid, '" . get_date_time() . "', " . sqlesc($text) .
	       "," . sqlesc($text) . "," . sqlesc(getip()) . ")");

	  $newid = mysql_insert_id();

	sql_query('INSERT INTO comments_parsed (cid, text_hash, text_parsed) VALUES ('.implode(', ', array_map('sqlesc', array($newid, md5($text), format_comment($text)))).')') or sqlerr(__FILE__,__LINE__);

	  sql_query("UPDATE torrents SET comments = comments + 1 WHERE id = $torrentid");

	/////////////////СЛЕЖЕНИЕ ЗА КОММЕНТАМИ///////////////// 
    /*$res3 = sql_query("SELECT * FROM checkcomm WHERE checkid = $torrentid AND torrent = 1") or sqlerr(__FILE__,__LINE__);
    $subject = sqlesc("Новый комментарий");
    while ($arr3 = mysql_fetch_array($res3)) {
    	$msg = sqlesc("Для торрента [url=details.php?id=$torrentid&viewcomm=$newid#comm$newid]".$name."[/url] добавился новый комментарий.");
    	if ($CURUSER[id] != $arr3[userid])
     		sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) VALUES (0, $arr3[userid], NOW(), $msg, 0, $subject)") or sqlerr(__FILE__,__LINE__);
    }*/

	/*$subject = "Новый комментарий";
	$msg = "Для торрента [url=details.php?id=$torrentid&viewcomm=$newid#comm$newid]".$name."[/url] добавился новый комментарий.";
	send_pm(0, $userid, get_date_time(), $subject, $msg);*/
	//sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) SELECT 0, userid, NOW(), $msg, 0, $subject FROM checkcomm WHERE checkid = $torrentid AND torrent = 1 AND userid != $CURUSER[id]") or sqlerr(__FILE__,__LINE__);

    /////////////////СЛЕЖЕНИЕ ЗА КОММЕНТАМИ/////////////////

	  header("Refresh: 0; url=details.php?id=$torrentid&viewcomm=$newid#comm$newid");
	  die;
	}

  $torrentid = intval($_GET["tid"]);
  if (!is_valid_id($torrentid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

	$res = sql_query("SELECT name FROM torrents WHERE id = $torrentid") or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_array($res);
	if (!$arr)
	  stderr($tracker_lang['error'], $tracker_lang['no_torrent_with_such_id']);

	stdhead("Добление комментария к \"" . $arr["name"] . "\"");

	print("<p><form name=\"comment\" method=\"post\" action=\"comment.php?action=add\">\n");
	print("<input type=\"hidden\" name=\"tid\" value=\"$torrentid\"/>\n");
?>
	<table class=main border=0 cellspacing=0 cellpadding=3>
	<tr>
	<td class="colhead">
<?
	print("".$tracker_lang['add_comment']." к \"" . htmlspecialchars_uni($arr["name"]) . "\"");
?>
	</td>
	</tr>
	<tr>
	<td>
<?
	textbbcode("comment","text","");
?>
	</td></tr></table>
<?
	//print("<textarea name=\"text\" rows=\"10\" cols=\"60\"></textarea></p>\n");
	print("<p><input type=\"submit\" value=\"Добавить\" /></p></form>\n");

	$res = sql_query("SELECT comments.id, text, comments.ip, comments.added, username, title, class, users.id as user, users.avatar, users.donor, users.enabled, users.warned, users.parked FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = $torrentid ORDER BY comments.id DESC LIMIT 5");

	$allrows = array();
	while ($row = mysql_fetch_array($res))
	  $allrows[] = $row;

	if (count($allrows)) {
	  print("<h2>Последние комментарии, в обратном порядке</h2>\n");
	  commenttable($allrows);
	}

  stdfoot();
	die;
}
elseif ($action == "quote")
{
  $commentid = intval($_GET["cid"]);
  if (!is_valid_id($commentid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

  $res = sql_query("SELECT c.*, t.name, t.id AS tid, u.username FROM comments AS c LEFT JOIN torrents AS t ON c.torrent = t.id JOIN users AS u ON c.user = u.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
  $arr = mysql_fetch_array($res);
  if (!$arr)
  	stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

 	stdhead("Добавления комментария к \"" . $arr["name"] . "\"");

	$text = "[quote=$arr[username]]" . $arr["text"] . "[/quote]\n";

	print("<form method=\"post\" name=\"comment\" action=\"comment.php?action=add\">\n");
	print("<input type=\"hidden\" name=\"tid\" value=\"$arr[tid]\" />\n");
?>

	<table class=main border=0 cellspacing=0 cellpadding=3>
	<tr>
	<td class="colhead">
<?
	print("Добавления комментария к \"" . htmlspecialchars_uni($arr["name"]) . "\"");
?>
	</td>
	</tr>
	<tr>
	<td>
<?
	textbbcode("comment","text",htmlspecialchars_uni($text));
?>
	</td></tr></table>

<?

	print("<p><input type=\"submit\" value=\"Добавить\" /></p></form>\n");

	stdfoot();

}
elseif ($action == "edit")
{
  $commentid = intval($_GET["cid"]);
  if (!is_valid_id($commentid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

  $res = sql_query("SELECT c.*, t.name, t.id AS tid FROM comments AS c LEFT JOIN torrents AS t ON c.torrent = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
  $arr = mysql_fetch_array($res);
  if (!$arr)
  	stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

	if ($arr["user"] != $CURUSER["id"] && get_user_class() < UC_MODERATOR)
		stderr($tracker_lang['error'], $tracker_lang['access_denied']);

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
	  $text = $_POST["text"];
    $returnto = $_POST["returnto"];

	  if ($text == "")
	  	stderr($tracker_lang['error'], $tracker_lang['comment_cant_be_empty']);

	$orig_text = $text;
	  $text = sqlesc($text);

	  $editedat = sqlesc(get_date_time());

	  sql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);

	sql_query('REPLACE INTO comments_parsed (cid, text_hash, text_parsed) VALUES ('.implode(', ', array_map('sqlesc', array($commentid, md5($orig_text), format_comment($orig_text)))).')') or sqlerr(__FILE__,__LINE__);

		if ($returnto)
	  	header("Location: $returnto");
		else
		  header("Location: $DEFAULTBASEURL/");      // change later ----------------------
		die;
	}

 	stdhead("Редактирование комментария к \"" . $arr["name"] . "\"");

	print("<form method=\"post\" name=\"comment\" action=\"comment.php?action=edit&amp;cid=$commentid\">\n");
	print("<input type=\"hidden\" name=\"returnto\" value=\"details.php?id={$arr["tid"]}&amp;viewcomm=$commentid#comm$commentid\" />\n");
	print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" />\n");
?>

	<table class=main border=0 cellspacing=0 cellpadding=3>
	<tr>
	<td class="colhead">
<?
	print("Редактирование комментария к \"" . htmlspecialchars_uni($arr["name"]) . "\"");
?>
	</td>
	</tr>
	<tr>
	<td>
<?
	textbbcode("comment","text",htmlspecialchars_uni($arr["text"]));
?>
	</td></tr></table>

<?

	print("<p><input type=\"submit\" value=\"Отредактировать\" /></p></form>\n");

	stdfoot();
	die;
}
/////////////////СЛЕЖЕНИЕ ЗА КОММЕНТАМИ///////////////// 
elseif ($action == "check" || $action == "checkoff")
{
        $tid = intval($_GET["tid"]);
        if (!is_valid_id($tid))
                stderr($tracker_lang['error'], "Неверный идентификатор $tid.");
        $docheck = mysql_fetch_array(sql_query("SELECT COUNT(*) FROM checkcomm WHERE checkid = " . $tid . " AND userid = " . $CURUSER["id"] . " AND torrent = 1"));
        if ($docheck[0] > 0 && $action=="check")
                stderr($tracker_lang['error'], "<p>Вы уже подписаны на этот торрент.</p><a href=details.php?id=$tid#startcomments>Назад</a>");
        if ($action == "check") {
                sql_query("INSERT INTO checkcomm (checkid, userid, torrent) VALUES ($tid, $CURUSER[id], 1)") or sqlerr(__FILE__,__LINE__);
                stderr($tracker_lang['success'], "<p>Теперь вы следите за комментариями к этому торренту.</p><a href=details.php?id=$tid#startcomments>Назад</a>");
        }
        else {
                sql_query("DELETE FROM checkcomm WHERE checkid = $tid AND userid = $CURUSER[id] AND torrent = 1") or sqlerr(__FILE__,__LINE__);
                stderr($tracker_lang['success'], "<p>Теперь вы не следите за комментариями к этому торренту.</p><a href=details.php?id=$tid#startcomments>Назад</a>");
        }

}
/////////////////СЛЕЖЕНИЕ ЗА КОММЕНТАМИ/////////////////
elseif ($action == "delete")
{
	if (get_user_class() < UC_MODERATOR)
		stderr($tracker_lang['error'], $tracker_lang['access_denied']);

  $commentid = intval($_GET["cid"]);

  if (!is_valid_id($commentid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

  $sure = $_GET["sure"];

  if (!$sure)
  {
		stderr($tracker_lang['delete']." ".$tracker_lang['comment'], sprintf($tracker_lang['you_want_to_delete_x_click_here'],$tracker_lang['comment'],"?action=delete&cid=$commentid&sure=1"));
  }


	$res = sql_query("SELECT torrent FROM comments WHERE id=$commentid")  or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_array($res);
	if ($arr)
		$torrentid = $arr["torrent"];

	sql_query("DELETE FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
	if ($torrentid && mysql_affected_rows() > 0)
		sql_query("UPDATE torrents SET comments = comments - 1 WHERE id = $torrentid");

	list($commentid) = mysql_fetch_row(sql_query("SELECT id FROM comments WHERE torrent = $torrentid ORDER BY added DESC LIMIT 1"));

	$returnto = "details.php?id=$torrentid&viewcomm=$commentid#comm$commentid";

	if ($returnto)
	  header("Location: $returnto");
	else
	  header("Location: $DEFAULTBASEURL/");      // change later ----------------------
	die;
}
elseif ($action == "vieworiginal")
{
	if (get_user_class() < UC_MODERATOR)
		stderr($tracker_lang['error'], $tracker_lang['access_denied']);

  $commentid = intval($_GET["cid"]);

  if (!is_valid_id($commentid))
		stderr($tracker_lang['error'], $tracker_lang['invalid_id']);

  $res = sql_query("SELECT c.*, t.name, t.id AS tid FROM comments AS c LEFT JOIN torrents AS t ON c.torrent = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
  $arr = mysql_fetch_array($res);
  if (!$arr)
  	stderr($tracker_lang['error'], "Неверный идентификатор $commentid.");

  stdhead("Просмотр оригинала");
  print("<h1>Оригинальное содержание комментария №$commentid</h1><p>\n");
	print("<table width=500 border=1 cellspacing=0 cellpadding=5>");
  print("<tr><td class=comment>\n");
	echo htmlspecialchars_uni($arr["ori_text"]);
  print("</td></tr></table>\n");

  $returnto = "details.php?id={$arr["tid"]}&amp;viewcomm=$commentid#comm$commentid";

//	$returnto = "details.php?id=$torrentid&amp;viewcomm=$commentid#$commentid";

	if ($returnto)
 		print("<p><font size=small><a href=$returnto>Назад</a></font></p>\n");

	stdfoot();
	die;
}
else
	stderr($tracker_lang['error'], "Unknown action");

die;
?>