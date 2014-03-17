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

dbconn(false);

//loggedinorreturn();
parked();

$cats = genrelist();

$searchstr = '';

if (isset($_GET['search']))
    $searchstr = (string)unesc($_GET["search"]);
$cleansearchstr = htmlspecialchars_uni($searchstr);
if (empty($cleansearchstr))
    unset($cleansearchstr);

// sorting by MarkoStamcar

if (isset($_GET['sort']) && isset($_GET['type'])) {

    $column = '';
    $ascdesc = '';

    switch ($_GET['sort']) {
        case '1':
            $column = "name";
            break;
        case '2':
            $column = "numfiles";
            break;
        case '3':
            $column = "comments";
            break;
        case '4':
            $column = "added";
            break;
        case '5':
            $column = "size";
            break;
        case '6':
            $column = "times_completed";
            break;
        case '7':
            $column = "seeders";
            break;
        case '8':
            $column = "leechers";
            break;
        case '9':
            $column = "owner";
            break;
        case '10':
            if (get_user_class() >= UC_MODERATOR)
                $column = "moderatedby";
            break;
        default:
            $column = "id";
            break;
    }

    switch ($_GET['type']) {
        case 'asc':
            $ascdesc = "ASC";
            $linkascdesc = "asc";
            break;
        case 'desc':
            $ascdesc = "DESC";
            $linkascdesc = "desc";
            break;
        default:
            $ascdesc = "DESC";
            $linkascdesc = "desc";
            break;
    }


    $orderby = "ORDER BY t." . $column . " " . $ascdesc;
    $pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";

} else {

    $orderby = "ORDER BY t.not_sticky DESC, t.id DESC";
    $pagerlink = "";

}

$addparam = "";
$wherea = array();
$wherecatina = array();
$incldead = 0;

if (isset($_GET['incldead'])) {
    if ($_GET["incldead"] == 1) {
        $addparam .= "incldead=1&amp;";
        if (!isset($CURUSER) || get_user_class() < UC_ADMINISTRATOR)
            $wherea[] = "banned != 'yes'";
    } elseif ($_GET["incldead"] == 2) {
        $addparam .= "incldead=2&amp;";
        $wherea[] = "visible = 'no'";
    } elseif ($_GET["incldead"] == 3) {
        $addparam .= "incldead=3&amp;";
        $wherea[] = "free = 'yes'";
        $wherea[] = "visible = 'yes'";
    } elseif ($_GET["incldead"] == 4) {
        $addparam .= "incldead=4&amp;";
        $wherea[] = "seeders = 0";
        $wherea[] = "visible = 'yes'";
    }
    $incldead = (int)$_GET['incldead'];
} else
    $wherea[] = "visible = 'yes'";

if (isset($_GET['cat']))
    $category = (int)$_GET["cat"]; else
    $category = 0;

if (isset($_GET['all']))
    $all = $_GET["all"]; else
    $all = false;

if (!$all)
    if (!$_GET && $CURUSER["notifs"]) {
        $all = True;
        foreach ($cats as $cat) {
            $all &= $cat[id];
            if (strpos($CURUSER["notifs"], "[cat" . $cat[id] . "]") !== false) {
                $wherecatina[] = $cat[id];
                $addparam .= "c$cat[id]=1&amp;";
            }
        }
    } elseif ($category) {
        if (!is_valid_id($category))
            stderr($tracker_lang['error'], "Invalid category ID.");
        $wherecatina[] = $category;
        $addparam .= "cat=$category&amp;";
    } else {
        $all = true;
        foreach ($cats as $cat) {
            $all &= $_GET["cr$car[id]"];
            if (isset($_GET["c$cat[id]"])) {
                $wherecatina[] = $cat[id];
                $addparam .= "c$cat[id]=1&amp;";
            }
        }
    }

if ($all) {
    $wherecatina = array();
    $addparam = "";
}

if (count($wherecatina) > 1)
    $wherecatin = implode(",", $wherecatina); elseif (count($wherecatina) == 1)
    $wherea[] = "category = $wherecatina[0]";

$wherebase = $wherea;

if (isset($cleansearchstr)) {
    $wherea[] = "t.name LIKE '%" . sqlwildcardesc($searchstr) . "%'";
    $addparam .= "search=" . urlencode($searchstr) . "&amp;";
}

$where = implode(" AND ", $wherea);
if (isset($wherecatin) && !empty($wherecatin))
    $where .= ($where ? " AND " : "") . "t.category IN (" . $wherecatin . ")";

if ($where != "")
    $where = "WHERE $where";

$res = sql_query("SELECT COUNT(*) FROM torrents AS t $where") or die(mysql_error());
$row = mysql_fetch_array($res);
$count = $row[0];
$num_torrents = $count;

if (!$count && isset($cleansearchstr)) {
    $wherea = $wherebase;
    //$orderby = "ORDER BY id DESC";
    $searcha = explode(" ", $cleansearchstr);
    $sc = 0;
    foreach ($searcha as $searchss) {
        if (strlen($searchss) <= 1)
            continue;
        $sc++;
        if ($sc > 5)
            break;
        $ssa = array();
        $ssa[] = "t.name LIKE '%" . sqlwildcardesc($searchss) . "%'";
    }
    if ($sc) {
        $where = implode(" AND ", $wherea);
        if ($where != "")
            $where = "WHERE $where";
        $res = sql_query("SELECT COUNT(*) FROM torrents AS t $where");
        $row = mysql_fetch_array($res);
        $count = $row[0];
    }
}

$torrentsperpage = $CURUSER["torrentsperpage"];
if (!$torrentsperpage)
    $torrentsperpage = 25;

if ($count) {
    if ($addparam != "") {
        if ($pagerlink != "") {
            if ($addparam{strlen($addparam) - 1} != ";") { // & = &amp;
                $addparam = $addparam . "&" . $pagerlink;
            } else {
                $addparam = $addparam . $pagerlink;
            }
        }
    } else {
        $addparam = $pagerlink;
    }
    list($pagertop, $pagerbottom, $limit) = pager($torrentsperpage, $count, "browse.php?" . $addparam);
    $query = "SELECT t.id, t.moderated, t.moderatedby, t.category, (t.leechers + t.remote_leechers) AS leechers, (t.seeders + t.remote_seeders) AS seeders, t.multitracker, t.last_mt_update, t.free, t.name, t.info_hash, t.times_completed, t.size, t.added, t.comments, t.numfiles, t.filename, t.not_sticky, t.owner," . "IF(t.numratings < $minvotes, NULL, ROUND(t.ratingsum / t.numratings, 1)) AS rating, c.name AS cat_name, c.image AS cat_pic, u.username, u.class" . ($CURUSER ? ", EXISTS(SELECT * FROM readtorrents WHERE readtorrents.userid = " . sqlesc($CURUSER["id"]) . " AND readtorrents.torrentid = t.id) AS readtorrent" : ", 1 AS readtorrent") . " FROM torrents AS t LEFT JOIN categories AS c ON t.category = c.id LEFT JOIN users AS u ON t.owner = u.id $where $orderby $limit";
    $res = sql_query($query) or die(mysql_error());
} else
    unset($res);
if (isset($cleansearchstr))
    stdhead($tracker_lang['search_results_for'] . " \"$cleansearchstr\""); else
    stdhead($tracker_lang['browse']);

?>

    <style type="text/css" media="screen">

        a.catlink:link, a.catlink:visited {
            text-decoration: none;
        }

        a.catlink:hover {
            color: #A83838;
        }

    </style>

    <table class="embedded" cellspacing="0" cellpadding="5" width="100%">
        <tr>
            <td class="colhead" align="center" colspan="12">Список торрентов</td>
        </tr>
        <tr>
            <td colspan="12">

                <form method="get" action="browse.php">
                    <table class="embedded" align="center">
                        <tr>
                            <td class="bottom">
                                <table class="bottom">
                                    <tr>

                                        <?
                                        $i = 0;
                                        foreach ($cats as $cat) {
                                            $catsperrow = 5;
                                            print(($i && $i % $catsperrow == 0) ? "</tr><tr>" : "");
                                            print("<td class=\"bottom\" style=\"padding-bottom: 2px;padding-left: 7px\"><input name=\"c$cat[id]\" type=\"checkbox\" " . (in_array($cat['id'], $wherecatina) ? "checked " : "") . "value=\"1\"><a class=\"catlink\" href=\"browse.php?cat=$cat[id]\">" . htmlspecialchars_uni($cat['name']) . "</a></td>\n");
                                            $i++;
                                        }

                                        $alllink = "<div align=\"left\">(<a href=\"browse.php?all=1\"><b>" . $tracker_lang['show_all'] . "</b></a>)</div>";

                                        $ncats = count($cats);
                                        $nrows = ceil($ncats / $catsperrow);
                                        $lastrowcols = $ncats % $catsperrow;

                                        if ($lastrowcols != 0) {
                                            if ($catsperrow - $lastrowcols != 1) {
                                                print("<td class=\"bottom\" rowspan=\"" . ($catsperrow - $lastrowcols - 1) . "\">&nbsp;</td>");
                                            }
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="embedded">
                                <center>
                                    <?= $tracker_lang['search']; ?>:
                                    <input type="text" id="searchinput" name="search" size="40" autocomplete="off"
                                           ondblclick="suggest(event.keyCode,this.value);"
                                           onkeyup="suggest(event.keyCode,this.value);"
                                           onkeypress="return noenter(event.keyCode);"
                                           value="<?= htmlspecialchars_uni($searchstr) ?>"/>
                                    <?= $tracker_lang['in']; ?>
                                    <select name="incldead">
                                        <option value="0"><?= $tracker_lang['active']; ?></option>
                                        <option
                                            value="1"<? print($incldead == 1 ? " selected" : ""); ?>><?= $tracker_lang['including_dead']; ?></option>
                                        <option
                                            value="2"<? print($incldead == 2 ? " selected" : ""); ?>><?= $tracker_lang['only_dead']; ?></option>
                                        <option
                                            value="3"<? print($incldead == 3 ? " selected" : ""); ?>><?= $tracker_lang['golden_torrents']; ?></option>
                                        <option
                                            value="4"<? print($incldead == 4 ? " selected" : ""); ?>><?= $tracker_lang['no_seeds']; ?></option>
                                    </select>
                                    <select name="cat">
                                        <option value="0">(<?= $tracker_lang['all_types']; ?>)</option>
                                        <?

                                        //$cats = genrelist();
                                        $catdropdown = "";
                                        foreach ($cats as $cat) {
                                            $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
                                            if (isset($_GET['cat']) && $cat["id"] == $_GET["cat"])
                                                $catdropdown .= " selected=\"selected\"";
                                            $catdropdown .= ">" . htmlspecialchars_uni($cat["name"]) . "</option>\n";
                                        }

                                        ?>
                                        <?= $catdropdown ?>
                                    </select>
                                    <input class="btn" type="submit" value="<?= $tracker_lang['search']; ?>!"/>
                                </center>
                </form>
                <script language="JavaScript" src="js/suggest.js" type="text/javascript"></script>
                <div id="suggcontainer" style="text-align: left; width: 520px; display: none;">
                    <div id="suggestions"
                         style="cursor: default; position: absolute; background-color: #FFFFFF; border: 1px solid #777777;"></div>
                </div>
            </td>
        </tr>
    </table>

<?

if (isset($cleansearchstr))
    print('<tr><td class="index" colspan="12">' . $tracker_lang['search_results_for'] . ' "' . htmlspecialchars_uni($searchstr) . '"</td></tr>');

echo '</td></tr>';

if ($num_torrents) {

    echo '<tr><td class="index" colspan="12">';
    echo $pagertop;
    echo '</td></tr>';

    torrenttable($res, "index");

    echo '<tr><td class="index" colspan="12">';
    echo $pagerbottom;
    echo '</td></tr>';

} else {
    if (isset($cleansearchstr)) {
        print("<tr><td class=\"index\" colspan=\"12\">" . $tracker_lang['nothing_found'] . "</td></tr>\n");
        //print("<p>Попробуйте изменить запрос поиска.</p>\n");
    } else {
        print("<tr><td class=\"index\" colspan=\"12\">" . $tracker_lang['nothing_found'] . "</td></tr>\n");
        //print("<p>Извините, данная категория пустая.</p>\n");
    }
}

echo '</table>';

stdfoot();

?>