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
dbconn(false);

loggedinorreturn();

$pic = html_uni($_GET["pic"]);

if(empty($pic))
stderr("Внимание, обнаружена ошибка", "По данному адресу публикаций на сайте не найдено, либо у вас нет доступа для просмотра информации по данному адресу.");

$allowed_image_types = array("image/gif" => "gif", "image/png" => "png", "image/jpeg" => "jpeg", "image/jpg" => "jpg");

$file_extension = pathinfo($pic, PATHINFO_EXTENSION);

if (!in_array($file_extension, $allowed_image_types))
stderr("Внимание, обнаружена ошибка", "По данному адресу публикаций на сайте не найдено, либо у вас нет доступа для просмотра информации по данному адресу.");


stdhead("Просмотр картинки");



print("<h1>".$pic."</h1>\n");


print("<p align=center><img width=\"100%\" src=\"".(file_exists("torrents/images/".$pic) ? "torrents/images/".$pic : $pic_base_url."/default_torrent.png")."
\"></p>\n");
echo "<h3 align=center><a href=\"javascript: history.go(-1)\">Назад</a></h3>";
stdfoot();
?>
