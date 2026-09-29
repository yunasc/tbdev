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
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
if ($action == 'delete') {
    $id = (int)(isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : 0));
    if (!is_valid_id($id))
        stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
    $returnto = safe_local_return(isset($_POST['returnto']) ? $_POST['returnto'] : (isset($_GET['returnto']) ? $_GET['returnto'] : 'index.php'));
    if ($_SERVER['REQUEST_METHOD'] != 'POST')
        stderr('Delete index release', '<form method="post" action="indexdelete.php">'
            . '<input type="hidden" name="action" value="delete">'
            . '<input type="hidden" name="id" value="'.$id.'">'
            . '<input type="hidden" name="returnto" value="'.htmlspecialchars_uni($returnto).'">'
            . '<input type="hidden" name="csrf_token" value="'.csrf_token().'">'
            . '<button type="submit">Confirm deletion</button></form>');
    csrf_require_post();
    sql_query("DELETE FROM indexreleases WHERE id = ".sqlesc($id)) or sqlerr(__FILE__, __LINE__);
    header('Location: '.$returnto);
    exit;
}
?>