<?php
if (!defined('BLOCK_FILE')) {
 Header("Location: ../index.php");
 exit;
}

global $tracker_lang;

$blocktitle = $tracker_lang['search'];

$content = '<table width="100%">
   <tr><td class="embedded">
   &nbsp;'.$tracker_lang['torrents'].'
   <form method="get" action=browse.php>
   <input type="text" name="search" size="20" value="" /></td></tr>
   <tr><td class="embedded" style="padding-top: 3px;">
   <input type="submit" value="'.$tracker_lang['search_btn'].'!" /></td></tr>
   </form>
   <tr><td class="embedded" style="padding-top: 3px;"></table>';

?>