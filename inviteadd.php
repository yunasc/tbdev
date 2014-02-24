<?php
/*+----------------------------------------------------+*/
/*| made by putyn @ tbdev 31/05/2009 updated 30/09/2009|*/
/*+----------------------------------------------------+*/

require_once "include/bittorrent.php";

dbconn();
loggedinorreturn();



if (get_user_class() < UC_ADMINISTRATOR)
	stderr("Error", "Access denied.");
	
	//dont forget to edit this 
	$maxclass = UC_SYSOP;
	$firstclass = UC_USER;
	$use_subject = true;
	
	function mkpositive($n)
	{
		return strstr((string)$n,"-") ? 0 : $n ; // this will return 0 for negative numbers 
	}
	
	if ($HTTP_SERVER_VARS["REQUEST_METHOD"] == "POST")
	{
		$classes = isset($_POST["classes"]) ? $_POST["classes"] : "";
		$all = ($classes[0] == 255 ? true : false );
		
		if(empty($classes) || sizeof($classes) == 0 )
			stderr("Err","You need at least one class selected");
		$a_do = array("add","remove","remove_all");
		$do = isset($_POST["do"]) && in_array($_POST["do"],$a_do) ? $_POST["do"] : "";
		if(empty($do))
			stderr("Err","wtf are you trying to do ");
			
		$invites = isset($_POST["invites"]) ? 0+$_POST["invites"] : 0;
		if($invites == 0 && ($do == "add" || $do == "remove"))
			stderr("Err","You can't remove/add 0");
			
		$sendpm = isset($_POST["pm"]) && $_POST["pm"] == "yes" ? true : false;
		
		$pms = array();
		$users = array();
		//select the users
		$q = mysql_query("SELECT id,invites,username FROM users ".($all ? "" : "WHERE class in (".join(",",$classes).")" )." ORDER BY id desc ") or sqlerr(__FILE__, __LINE__);
		if(mysql_num_rows($q) == 0)
		stderr("Sorry","There are no users in the class(es) you selected");
			while($a = mysql_fetch_assoc($q))
			{
				$users[] = "(".$a["id"].", ".($do == "remove_all" ? 0 : ($do == "add" ? $a["invites"] + $invites : mkpositive($a["invites"] - $invites))) .")";
				if($sendpm)
				{
$subject = sqlesc($do == "remove_all" && $do == "remove" ?  "" : "");
$body = sqlesc("".$a['username']."\n \n ". ($do == "remove_all" ?  "Все приглашения были отняты у Вашего класса пользователей" : ($do == "add" ? "$invites".($invites > 1 ? " приглашения были добавлены " : " приглашение было добавлено ")." Вашему классу пользователей" : "$invites".($invites > 1 ? " приглашения были отняты " : " приглашение было отнято ")."у Вашей группы пользователей")). " !\n \n Администрация ".$tracker_lang['site_name']." ");
$pms[] = "(0,".$a['id'].",".sqlesc(get_date_time()).",$body ".($use_subject ? ",$subject" : "").")" ;
				}
			}
			
			if(sizeof($users) > 0)
				$r = mysql_query("INSERT INTO users(id,invites) VALUES ".join(",",$users)." ON DUPLICATE key UPDATE invites=values(invites) ") or sqlerr(__FILE__, __LINE__);
			if(sizeof($pms) > 0)
				$r1 = mysql_query("INSERT INTO messages (sender, receiver, added, msg ".($use_subject ? ", subject" : "").") VALUES ".join(",",$pms)." ") or sqlerr(__FILE__, __LINE__);
				
			if($r && ($sendpm ? $r1 : true))
			{
				header("Refresh: 2; url=".$_SERVER["PHP_SELF"]);
				stderr("Succes","Operation done!");
			}
			else
				stderr("Error","Something was wrong");
	}
	$HTMLOUT ='';
	$HTMLOUT .= "<form  action=\"".$_SERVER["PHP_SELF"]."\" method=\"post\">
	<table width=\"500\" cellpadding=\"5\" cellspacing=\"0\" border=\"1\" align=\"center\">
	  <tr>
		<td valign=\"top\" align=\"right\">Classes</td>
		<td width=\"100%\" align=\"left\" colspan=\"3\">";
				$HTMLOUT .= "<label for=\"all\"><input type=\"checkbox\" name=\"classes[]\" value=\"255\" id=\"all\" />Всем классам</label><br/>\n";
				for($i=$firstclass;$i<$maxclass+1; $i++ )
				$HTMLOUT .= "<label for=\"c$i\"><input type=\"checkbox\" name=\"classes[]\" value=\"$i\" id=\"c$i\" />".get_user_class_name($i)." </label><br/>\n";
	$HTMLOUT .= "</td>
	  </tr>
	  <tr>
		<td valign=\"top\" align=\"center\" >Options</td>
		<td valign=\"top\">Do
		  <select name=\"do\" >
			<option value=\"add\">add invites</option>
			<option value=\"remove\">remove invites</option>
			<option value=\"remove_all\">Remove all invites</option>
		  </select></td>
		<td>Invites <input type=\"text\" maxlength=\"2\" name=\"invites\" size=\"5\" />
		</td>
		<td >Send pm <select name=\"pm\" ><option value=\"no\">no</option><option value=\"yes\">yes</option></select></td></tr>
		<tr><td colspan=\"4\" align=\"center\"><input type=\"submit\" value=\"Do!\" /></td></tr>
	</table>
	</form>";
	
	stdhead("Add/Remove invites");
        print($HTMLOUT);
        stdfoot();
?>
