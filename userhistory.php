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

gzip();

dbconn(false);

loggedinorreturn();

$userid = (int)$_GET["id"];

if (!is_valid_id($userid)) stderr($tracker_lang['error'], "Invalid ID");

if (get_user_class()< UC_POWER_USER || ($CURUSER["id"] != $userid && get_user_class() < UC_MODERATOR))
	stderr($tracker_lang['error'], "Нет доступа");

$page = $_GET["page"];

$action = $_GET["action"];

//-------- Global variables

$perpage = 25;

//-------- Action: View posts

if ($action == "viewposts")
{
	$select_is = "COUNT(DISTINCT p.id)";

	$from_is = "posts AS p LEFT JOIN topics as t ON p.topicid = t.id LEFT JOIN forums AS f ON t.forumid = f.id";

	$where_is = "p.userid = $userid AND f.minclassread <= " . $CURUSER['class'];

	$order_is = "p.id DESC";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is";

	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_row($res) or stderr($tracker_lang['error'], "Посты не найдены");

	$postcount = $arr[0];

	//------ Make page menu

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $postcount, $_SERVER["PHP_SELF"] . "?action=viewposts&id=$userid&");

	//------ Get user data

	$res = sql_query("SELECT username, donor, warned, enabled FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 1)
	{
  	$arr = mysql_fetch_assoc($res);

	  $subject = "<a href=userdetails.php?id=$userid><b>$arr[username]</b></a>" . get_user_icons($arr, true);
	}
	else
	    $subject = "unknown[$userid]";

	//------ Get posts

 	$from_is = "posts AS p LEFT JOIN topics as t ON p.topicid = t.id LEFT JOIN forums AS f ON t.forumid = f.id LEFT JOIN readposts as r ON p.topicid = r.topicid AND p.userid = r.userid";

	$select_is = "f.id AS f_id, f.name, t.id AS t_id, t.subject, t.lastpost, r.lastpostread, p.*";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is $limit";

	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0) stderr($tracker_lang['error'], "Посты не найдены");

	stdhead("История постов");

	print("<h1>История постов для $subject</h1>\n");

	if ($postcount > $perpage) echo $pagertop;

	//------ Print table

	begin_main_frame();

	begin_frame();

	while ($arr = mysql_fetch_assoc($res))
	{
	    $postid = $arr["id"];

	    $posterid = $arr["userid"];

	    $topicid = $arr["t_id"];

	    $topicname = $arr["subject"];

	    $forumid = $arr["f_id"];

	    $forumname = $arr["name"];

	    $newposts = ($arr["lastpostread"] < $arr["lastpost"]) && $CURUSER["id"] == $userid;

	    $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " назад)";

	    print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>
	    $added&nbsp;--&nbsp;<b>Форум:&nbsp;</b>
	    <a href=forums.php?action=viewforum&forumid=$forumid>$forumname</a>
	    &nbsp;--&nbsp;<b>Тема:&nbsp;</b>
	    <a href=forums.php?action=viewtopic&topicid=$topicid>$topicname</a>
      &nbsp;--&nbsp;<b>Пост:&nbsp;</b>
      #<a href=forums.php?action=viewtopic&topicid=$topicid&page=p$postid#$postid>$postid</a>" .
      ($newposts ? " &nbsp;<b>(<font color=red>НОВЫЙ!</font>)</b>" : "") .
	    "</td></tr></table></p>\n");

	    begin_table(true);

	    $body = format_comment($arr["body"]);

	    if (is_valid_id($arr['editedby']))
	    {
        	$subres = sql_query("SELECT username FROM users WHERE id=$arr[editedby]");
	        if (mysql_num_rows($subres) == 1)
	        {
	            $subrow = mysql_fetch_assoc($subres);
	            $body .= "<p><font size=1 class=small>Последний раз редактировалось <a href=userdetails.php?id=$arr[editedby]><b>$subrow[username]</b></a> at $arr[editedat] GMT</font></p>\n";
	        }
	    }

	    print("<tr valign=top><td class=comment>$body</td></tr>\n");

	    end_table();
	}

	end_frame();

	end_main_frame();

	if ($postcount > $perpage) echo $pagerbottom;

	stdfoot();

	die;
}

//-------- Action: View comments

if ($action == "viewcomments")
{
	$select_is = "COUNT(*)";

	// LEFT due to orphan comments
	$from_is = "comments AS c LEFT JOIN torrents as t
	            ON c.torrent = t.id";

	$where_is = "c.user = $userid";
	$order_is = "c.id DESC";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is";

	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

	$arr = mysql_fetch_row($res) or stderr($tracker_lang['error'], "Комментарии не найдены");

	$commentcount = $arr[0];

	//------ Make page menu

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $commentcount, $_SERVER["PHP_SELF"] . "?action=viewcomments&id=$userid&");

	//------ Get user data

	$res = sql_query("SELECT username, donor, warned, enabled FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 1)
	{
		$arr = mysql_fetch_assoc($res);

	  $subject = "<a href=userdetails.php?id=$userid><b>$arr[username]</b></a>" . get_user_icons($arr, true);
	}
	else
	  $subject = "unknown[$userid]";

	//------ Get comments

	$select_is = "t.name, c.torrent AS t_id, c.id, c.added, c.text";

	$query = "SELECT $select_is FROM $from_is WHERE $where_is ORDER BY $order_is $limit";

	$res = sql_query($query) or sqlerr(__FILE__, __LINE__);

	if (mysql_num_rows($res) == 0) stderr($tracker_lang['error'], "Комментарии не найдены");

	stdhead("История комментариев");

	print("<h1>История комментариев для $subject</h1>\n");

	if ($commentcount > $perpage) echo $pagertop;

	//------ Print table

	begin_main_frame();

	begin_frame();

	while ($arr = mysql_fetch_assoc($res))
	{

		$commentid = $arr["id"];

	  $torrent = $arr["name"];

    // make sure the line doesn't wrap
	  if (strlen($torrent) > 55) $torrent = substr($torrent,0,52) . "...";

	  $torrentid = $arr["t_id"];

	  //find the page; this code should probably be in details.php instead

	  $subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent = $torrentid AND id < $commentid")
	  	or sqlerr(__FILE__, __LINE__);
	  $subrow = mysql_fetch_row($subres);
    $count = $subrow[0];
    $comm_page = floor($count/20);
    $page_url = $comm_page?"&page=$comm_page":"";

	  $added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " назад)";

	  print("<p class=sub><table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded>".
	  "$added&nbsp;---&nbsp;<b>Торрент:&nbsp;</b>".
	  ($torrent?("<a href=details.php?id=$torrentid&tocomm=1>$torrent</a>"):" [Удален] ").
	  "&nbsp;---&nbsp;<b>Комментарий:&nbsp;</b>#<a href=details.php?id=$torrentid&tocomm=1$page_url>$commentid</a>
	  </td></tr></table></p>\n");

	  begin_table(true);

	  $body = format_comment($arr["text"]);

	  print("<tr valign=top><td class=comment>$body</td></tr>\n");

	  end_table();
	}

	end_frame();

	end_main_frame();

	if ($commentcount > $perpage) echo $pagerbottom;

	stdfoot();

	die;
}

//-------- Handle unknown action

if ($action != "")
	stderr($tracker_lang['error'], "Неизвестное действие.");

//-------- Any other case

stderr($tracker_lang['error'], "Неверный или отсутствующий запрос.");

?>