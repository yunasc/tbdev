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

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();


function cleanit($array, $index, $maxlength)
 {
   if (isset($array["{$index}"]))
   {
      $input = substr($array["{$index}"], 0, $maxlength);
      $input = mysql_real_escape_string($input);
      return ($input);
   }
   return NULL;
 }
 
//$action = $_GET["action"];
$pollid = cleanit($_GET, "id", 2);
//$returnto = $_GET["returnto"];

if (get_user_class() < UC_MODERATOR)
  stderr($tracker_lang['error'], "Нет доступа.");
 
stdhead("Обзор опросов");

if (!(isset($_GET['id']))) {
 
$sql = sql_query("SELECT id, added, question FROM polls ORDER BY id DESC") or sqlerr(__FILE__, __LINE__);
//$sql = db_query("SELECT id, added, question FROM polls ORDER BY id DESC");


print("<h1>Обзор опросов</h1>\n");

print("<p><table width=750 border=1 cellspacing=0 cellpadding=5><tr>\n" .
"<td class=colhead align=center>ID</td><td class=colhead>Добавлен</td><td class=colhead>Вопрос</td></tr>\n");

if (mysql_num_rows($sql) == 0) {
 print("<tr><td colspan=3>Извините...Нет голосовавших пользователей!</td></tr></table>");
 stdfoot();
 exit;
 }
 
while ($poll = mysql_fetch_assoc($sql))
{
 $added = date("Y-m-d h-i-s",strtotime($poll['added'])) . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"]))) . " назад)";
 print("<tr><td align=center><a href=\"polloverview.php?id={$poll['id']}\">{$poll['id']}</a></td><td>{$added}</td><td><a href=\"polloverview.php?id={$poll['id']}\">{$poll['question']}</a></td></tr>\n");
 
}

print("</table>\n");

} else {

if (isset($_GET['id'])) {
 
$sql = sql_query("SELECT * FROM polls WHERE id = {$pollid} ORDER BY id DESC") or sqlerr(__FILE__, __LINE__);



print("<h1>Обзор опроса</h1>\n");

print("<p><table width=750 border=1 cellspacing=0 cellpadding=5><tr>\n" .
"<td class=colhead align=center>ID</td><td class=colhead>Добавлен</td><td class=colhead>Вопрос</td></tr>\n");

if (mysql_num_rows($sql) == 0) {
 print("<tr><td colspan=2>Извините...Нет опроса с таким ID!</td></tr></table>");
 stdfoot();
 exit;
 }
 
while ($poll = mysql_fetch_assoc($sql))
{
 $o = array($poll["option0"], $poll["option1"], $poll["option2"], $poll["option3"], $poll["option4"],
  $poll["option5"], $poll["option6"], $poll["option7"], $poll["option8"], $poll["option9"],
  $poll["option10"], $poll["option11"], $poll["option12"], $poll["option13"], $poll["option14"],
  $poll["option15"], $poll["option16"], $poll["option17"], $poll["option18"], $poll["option19"]);
 
 $added = date("Y-m-d h-i-s",strtotime($poll['added'])) . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($poll["added"]))) . " ago)";
 print("<tr><td align=center><a href=\"polloverview.php?id={$poll['id']}\">{$poll['id']}</a></td><td>{$added}</td><td><a href=\"polloverview.php?id={$poll['id']}\">{$poll['question']}</a></td></tr>\n");
 
}

print("</table><br />\n");

print("<h1>Обзор ответов</h1><br />\n");
print("<table width=750 border=1 cellspacing=0 cellpadding=5><tr><td class=colhead>Опция №</td><td class=colhead>Ответ</td></tr>\n");
foreach($o as $key=>$value) {
 if($value != "")
 print("<tr><td>{$key}</td><td>{$value}</td></tr>\n");
 }
print("</table>\n");
//print_r($o);

$sql2 = sql_query("SELECT pollanswers. * , users.username FROM pollanswers LEFT JOIN users ON users.id = pollanswers.userid WHERE pollid = {$pollid} AND selection < 20 ORDER  BY users.id DESC ") or sqlerr(__FILE__, __LINE__);

print("<h1>Обзор голосовавших пользователей</h1>\n");

print("<p><table width=750 border=1 cellspacing=0 cellpadding=5><tr>\n" .
"<td class=colhead align=center>Пользователь</td><td class=colhead>Выбор</td></tr>\n");

if (mysql_num_rows($sql2) == 0) {
 print("<tr><td colspan=2>Извините...Нет голосовавших пользователей!</td></tr></table>");
 stdfoot();
 exit;
 }
 
while ($useras = mysql_fetch_assoc($sql2))
{
 $username  = ($useras['username'] ? $useras['username'] : "Неизвестно");
 //$useras['selection']--;
 print("<tr><td>{$username}</td><td>{$o[$useras['selection']]}</td></tr>\n");
}
print("</table>\n");

}
}

stdfoot();
?>