<?php
if (!defined('UC_SYSOP'))
	die('Direct access denied.');
?><html><head>
<title><?= $title ?></title>
<link rel="stylesheet" href="./themes/<?=$ss_uri."/".$ss_uri?>.css" type="text/css">
<script language="javascript" type="text/javascript" src="js/resizer.js"></script>
<!--<script language="javascript" type="text/javascript" src="js/tooltips.js"></script>-->
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery.migrate.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery.cookies.js"></script>

<!-- GreenSock JS -->
<script language="javascript" type="text/javascript" src="js/TweenMax.min.js"></script>
<!-- GreenSock jQuery plugin for .animate() via GSAP -->
<script language="javascript" type="text/javascript" src="js/jquery.gsap.min.js"></script>

<script language="javascript" type="text/javascript" src="js/blocks.js"></script>
<script language="javascript" type="text/javascript" src="js/lightbox.js"></script>
<script type="text/javascript">
<!--

var ExternalLinks_InNewWindow = '1';

function initSpoilers(context) {
	var context = context || 'body';
	$('div.spoiler-head', $(context))
		.click(function(){
		    var ctx = $(this).next('div.spoiler-body');
			var code = ctx.children('textarea').text();
			if (code) {
			    ctx.children('textarea').replaceWith(code);
			    initSpoilers(ctx);
			}
			$(this).toggleClass('unfolded');
            $(this).next('div.spoiler-body').slideToggle('fast');
            $(this).next('div.spoiler-body').next().slideToggle('fast');
		});
}

$(document).ready(function(){
	initSpoilers('body');
	$(function() {$('a[rel*=lightbox]').lightBox();});
});

//-->
</script>
<?
if($keywords)
    echo "<meta name=\"keywords\" content=\"$keywords\" />\n";
if($description)
    echo "<meta name=\"description\" content=\"$description\" />\n";
?>
<link rel="alternate" type="application/rss+xml" title="Последние торренты" href="<?=$DEFAULTBASEURL?>/rss.php">
<link rel="shortcut icon" href="<?=$DEFAULTBASEURL;?>/favicon.ico" type="image/x-icon" />

</head>
<body>

<table width="100%" class="clear" border="0" cellspacing="0" cellpadding="0" style="background: transparent;">
<tr>
<td class="embedded" width="50%" background="./themes/<?=$ss_uri;?>/images/logobg.jpg">
<a href="<?=$DEFAULTBASEURL?>"><img style="border: none" alt="<?=$SITENAME?>" title="<?=$SITENAME?>" src="./themes/<?=$ss_uri;?>/images/logo.jpg" /></a>
</td>
<td class="embedded" width="50%" align="right" style="text-align: right" background="./themes/<?=$ss_uri;?>/images/logobg.jpg">
	<noindex><iframe src="http://bit-torrent.kiev.ua/banner.php" width="468" height="60" marginwidth="0" marginheight="0" scrolling="no" frameborder="0"></iframe></noindex>&nbsp;
</td>
</tr>
</table>

<!-- Top Navigation Menu for unregistered-->
<table width="100%" border="0" cellspacing="0" cellpadding="2"><tr>
<td align="center" class="topnav">&nbsp;<a href="<?=$DEFAULTBASEURL;?>/"><font color="#FFFFFF"><?=$tracker_lang['homepage'];?></font></a>
&nbsp;&#8226;&nbsp;
<a href="browse.php"><font color="#FFFFFF"><?=$tracker_lang['browse'];?></font></a>
<? if ($CURUSER) { ?>
&nbsp;&#8226;&nbsp;
<a href="bookmarks.php"><font color="#FFFFFF"><?=$tracker_lang['bookmarks'];?></font></a>
<? } ?>
<? if (get_user_class() >= UC_UPLOADER) { ?>
&nbsp;&#8226;&nbsp;
<a href="upload.php"><font color="#FFFFFF"><?=$tracker_lang['upload'];?></font></a>
<? } ?>
<? if ($CURUSER) { ?>
&nbsp;&#8226;&nbsp;
<a href="log.php"><font color="#FFFFFF"><?=$tracker_lang['logs'];?></font></a>
<? } ?>
&nbsp;&#8226;&nbsp;
<a href="rules.php"><font color="#FFFFFF"><?=$tracker_lang['rules'];?></font></a>
&nbsp;&#8226;&nbsp;
<a href="faq.php"><font color="#FFFFFF"><?=$tracker_lang['faq'];?></font></a>
<? if ($CURUSER) { ?>
&nbsp;&#8226;&nbsp;
<!--<a href="helpdesk.php"><font color="#FFFFFF">Тех. Поддержка</font></a>
&nbsp;&#8226;&nbsp;-->
<a href="staff.php"><font color="#FFFFFF"><?=$tracker_lang['staff'];?></font></a>
<? } ?>
&nbsp;&#8226;&nbsp;
<a href="contactus.php"><font color="#FFFFFF"><?=$tracker_lang['contactus'];?></font></a>
</td></tr>
</table>
<!-- /////// Top Navigation Menu for unregistered-->

<!-- /////// some vars for the statusbar;o) //////// -->

<? if ($CURUSER) { ?>

<?

$uped = mksize($CURUSER['uploaded']);
$downed = mksize($CURUSER['downloaded']);
if ($CURUSER["downloaded"] > 0) {
	$ratio = $CURUSER['uploaded'] / $CURUSER['downloaded'];
	$ratio = number_format($ratio, 3);
	$color = get_ratio_color($ratio);
	if ($color)
		$ratio = "<font color=$color>$ratio</font>";
} elseif ($CURUSER["uploaded"] > 0)
	$ratio = "Inf.";
else
	$ratio = "---";

$medaldon = $warn = '';

if ($CURUSER['donor'] == "yes")
	$medaldon = "<img src=\"pic/star.gif\" alt=\"Донор\" title=\"Донор\">";
if ($CURUSER['warned'] == "yes")
	$warn = "<img src=\"pic/warned.gif\" alt=\"Предупрежден\" title=\"Предупрежден\">";

//// check for messages ////////////////// 
        $res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location=1") or print(mysql_error()); 
        $arr1 = mysql_fetch_row($res1);
        $messages = $arr1[0];
        /*$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location=1 AND unread='yes'") or print(mysql_error()); 
        $arr1 = mysql_fetch_row($res1);
        $unread = $arr1[0];*/
        $res1 = sql_query("SELECT COUNT(*) FROM messages WHERE sender=" . $CURUSER["id"] . " AND saved='yes'") or print(mysql_error()); 
        $arr1 = mysql_fetch_row($res1);
        $outmessages = $arr1[0];
        if ($unread)
                $inboxpic = "<img height=\"16px\" style=\"border:none\" alt=\"inbox\" title=\"Есть новые сообщения\" src=\"pic/pn_inboxnew.gif\">"; 
        else
                $inboxpic = "<img height=\"16px\" style=\"border:none\" alt=\"inbox\" title=\"Нет новых сообщений\" src=\"pic/pn_inbox.gif\">";

$res2 = sql_query("SELECT COUNT(*) FROM peers WHERE userid=" . $CURUSER["id"] . " AND seeder='yes'") or print(mysql_error());
$row = mysql_fetch_row($res2);
$activeseed = $row[0];

$res2 = sql_query("SELECT COUNT(*) FROM peers WHERE userid=" . $CURUSER["id"] . " AND seeder='no'") or print(mysql_error());
$row = mysql_fetch_row($res2);
$activeleech = $row[0];

//// end

?>

<!-- //////// start the statusbar ///////////// -->

</table>

<p>

<table align="center" cellpadding="4" cellspacing="0" border="0" style="width:100%">
<tr>
<td class="tablea"><table align="center" style="width:100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td class="bottom" align="left"><span class="smallfont"><?=$tracker_lang['welcome_back'];?><b><a href="userdetails.php?id=<?=$CURUSER['id']?>"><?=get_user_class_color($CURUSER['class'], $CURUSER['username'])?></a></b><?=$medaldon?><?=$warn?>&nbsp; [<a href="bookmarks.php">Закладки</a>] [<a href="mybonus.php">Мой бонус</a>] [<a href="logout.php">Выйти</a>]<br/>
<font color=1900D1>Рейтинг:</font> <?=$ratio?>&nbsp;&nbsp;<font color=green>Раздал:</font> <font color=black><?=$uped?></font>&nbsp;&nbsp;<font color=darkred>Скачал:</font> <font color=black><?=$downed?></font>&nbsp;&nbsp;<font color=darkblue>Бонус:</font> <a href="mybonus.php" class="online"><font color=black><?=$CURUSER["bonus"]?></font></a>&nbsp;&nbsp;<font color=1900D1>Торренты:&nbsp;</font></span> <img alt="Раздает" title="Раздает" src="./themes/<?=$ss_uri;?>/images/arrowup.gif">&nbsp;<font color=black><span class="smallfont"><?=$activeseed?></span></font>&nbsp;&nbsp;<img alt="Качает" title="Качает" src="./themes/<?=$ss_uri;?>/images/arrowdown.gif">&nbsp;<font color=black><span class="smallfont"><?=$activeleech?></span></font></td>
<td class="bottom" align="right"><span class="smallfont">Текущее время: <span id="clock">Загрузка...</span>

<!-- clock hack -->
<script type="text/javascript">
function refrClock()
{
var d=new Date();
var s=d.getSeconds();
var m=d.getMinutes();
var h=d.getHours();
var day=d.getDay();
var date=d.getDate();
var month=d.getMonth();
var year=d.getFullYear();
var am_pm;
if (s<10) {s="0" + s}
if (m<10) {m="0" + m}
if (h>12) {h-=12;am_pm = "PM"}
else {am_pm="AM"}
if (h<10) {h="0" + h}
document.getElementById("clock").innerHTML=h + ":" + m + ":" + s + " " + am_pm;
setTimeout("refrClock()",1000);
}
refrClock();
</script>
<!-- / clock hack -->

<?
if ($messages){
print("<span class=smallfont><a href=message.php>$inboxpic</a> $messages ($unread новых)</span>");
if ($outmessages)
print("<span class=smallfont>&nbsp;&nbsp;<a href=message.php?action=viewmailbox&box=-1><img height=16px style=border:none alt=Отправленые title=Отправленые src=pic/pn_sentbox.gif></a> $outmessages</span>");
else
print("<span class=smallfont>&nbsp;&nbsp;<a href=message.php?action=viewmailbox&box=-1><img height=16px style=border:none alt=Отправленые title=Отправленые src=pic/pn_sentbox.gif></a> 0</span>");
}
else
{
print("<span class=smallfont><a href=message.php><img height=16px style=border:none alt=Полученные title=Полученные src=pic/pn_inbox.gif></a> 0</span>");
if ($outmessages)
print("<span class=smallfont>&nbsp;&nbsp;<a href=message.php?action=viewmailbox&box=-1><img height=16px style=border:none alt=Отправленые title=Отправленые src=pic/pn_sentbox.gif></a> $outmessages</span>");
else
print("<span class=smallfont>&nbsp;&nbsp;<a href=message.php?action=viewmailbox&box=-1><img height=16px style=border:none alt=Отправленые title=Отправленые src=pic/pn_sentbox.gif></a> 0</span>");
}
print("&nbsp;<a href=friends.php><img style=border:none alt=Друзья title=Друзья src=pic/buddylist.gif></a>");
print("&nbsp;<a href=getrss.php><img style=border:none alt=RSS title=RSS src=pic/rss.gif></a>");
?>
</span></td>

</tr>
</table></table>
<p>

<? } else {?>

<br />

<? } ?>
<!-- /////////// here we go, with the menu //////////// -->

<?php

$w = "width=\"100%\"";
//if ($_SERVER["REMOTE_ADDR"] == $_SERVER["SERVER_ADDR"]) $w = "width=984";

?>
<table class="mainouter" <?=$w; ?> border="1" cellspacing="0" cellpadding="5">

<!------------- MENU ------------------------------------------------------------------------>

<? $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>

<td valign="top" width="155">
<?

show_blocks("l");

if ($messages) {
                $message_in = "<span class=\"smallfont\">&nbsp;<a href=\"message.php\">$inboxpic</a> $messages " . sprintf($tracker_lang["new_pm"], $unread) . "</span>";
                if ($outmessages)
                        $message_out = "<span class=\"smallfont\">&nbsp;<a href=\"message.php?action=viewmailbox&box=-1\"><img height=\"16px\" style=\"border:none\" alt=\"" . $tracker_lang['outbox'] . "\" title=\"" . $tracker_lang['outbox'] . "\" src=\"pic/pn_sentbox.gif\"></a> $outmessages</span>";
                else
                        $message_out = "<span class=\"smallfont\">&nbsp;<a href=\"message.php?action=viewmailbox&box=-1\"><img height=\"16px\" style=\"border:none\" alt=\"" . $tracker_lang['outbox'] . "\" title=\"" . $tracker_lang['outbox'] . "\" src=\"pic/pn_sentbox.gif\"></a> 0</span>";
        }
        else {
                $message_in = "<span class=\"smallfont\">&nbsp;<a href=\"message.php\"><img height=\"16px\" style=\"border:none\" alt=\"".$tracker_lang['inbox']."\" title=\"".$tracker_lang['inbox']."\" src=\"pic/pn_inbox.gif\"></a> 0</span>";
                if ($outmessages)
                        $message_out = "<span class=\"smallfont\">&nbsp;<a href=\"message.php?action=viewmailbox&box=-1\"><img height=\"16px\" style=\"border:none\" alt=\"" . $tracker_lang['outbox'] . "\" title=\"" . $tracker_lang['outbox'] . "\" src=\"pic/pn_sentbox.gif\"></a> $outmessages</span>";
                else
                        $message_out = "<span class=\"smallfont\">&nbsp;<a href=\"message.php?action=viewmailbox&box=-1\"><img height=\"16px\" style=\"border:none\" alt=\"" . $tracker_lang['outbox'] . "\" title=\"" . $tracker_lang['outbox'] . "\" src=\"pic/pn_sentbox.gif\"></a> 0</span>";
        }

if ($CURUSER) {

	$userbar = "<center><a href=\"my.php\"><img src=\"" . ( $CURUSER["avatar"] ? $CURUSER["avatar"] : "./themes/$ss_uri/images/default_avatar.gif" ) . "\" width=\"100\" alt=\"".$tracker_lang['avatar']."\" title=\"".$tracker_lang['avatar']."\" border=\"0\" /></a></center>
	<br />
	<font color=\"1900D1\">{$tracker_lang['ratio']}:</font>&nbsp;{$ratio}<br />
	<font color=\"green\">{$tracker_lang['uploaded']}:</font>&nbsp;{$uped}<br />
	<font color=\"red\">{$tracker_lang['downloaded']}:</font>&nbsp;{$downed}<br />
	<font color=\"darkblue\">{$tracker_lang['bonus']}:</font>&nbsp;<a href=\"mybonus.php\" class=\"online\"><font color=black>$CURUSER[bonus]</font></a><br />
	<font color=\"blue\">{$tracker_lang['pm']}:</font>&nbsp;{$message_in} {$message_out}<br />
	{$tracker_lang['torrents']}:&nbsp;<img alt=\"{$tracker_lang['seeding']}\" title=\"{$tracker_lang['seeding']}\" src=\"./themes/$ss_uri/images/arrowup.gif\">&nbsp;<font color=green><span class=\"smallfont\">{$activeseed}</span></font>&nbsp;<img alt=\"{$tracker_lang['leeching']}\" title=\"{$tracker_lang['leeching']}\" src=\"./themes/$ss_uri/images/arrowdown.gif\">&nbsp;<font color=red><span class=\"smallfont\">{$activeleech}</span></font><br />
	{$tracker_lang['clock']}:&nbsp;<span id=\"clock2\">{$tracker_lang['loading']}...</span>

<!-- clock hack -->
<script type=\"text/javascript\">
function refrClock2()
{
var d=new Date();
var s=d.getSeconds();
var m=d.getMinutes();
var h=d.getHours();
var day=d.getDay();
var date=d.getDate();
var month=d.getMonth();
var year=d.getFullYear();
var am_pm;
if (s<10) {s=\"0\" + s}
if (m<10) {m=\"0\" + m}
if (h>12) {h-=12;am_pm = \"PM\"}
else {am_pm=\"AM\"}
if (h<10) {h=\"0\" + h}
document.getElementById(\"clock2\").innerHTML=h + \":\" + m + \":\" + s + \" \" + am_pm;
setTimeout(\"refrClock2()\",1000);
}
refrClock2();
</script>
<!-- / clock hack --><br />
	<font color=\"#FF6600\">".$tracker_lang['your_ip'].": " . $_SERVER["REMOTE_ADDR"] . "</font><br />
	<br />
	<center><img src=\"pic/disabled.gif\" border=\"0\" />&nbsp;[<a href=\"logout.php\">".$tracker_lang['logout']."</a>]</center>
	";
} else {
	$userbar = '<center><form method="post" action="takelogin.php">
<br />
'.$tracker_lang['username'].': <br />
<input type="text" size=20 name="username" /><br />
'.$tracker_lang['password'].': <br />

<input type="password" size=20 name="password" /><br />
<input type="submit" value="'.$tracker_lang['login'].'!" class=\"btn\"><br /><br />
</form></center>
<a class="menu" href="signup.php"><center>'.$tracker_lang['signup'].'</center></a>';
}

if ($CURUSER['override_class'] != 255) $usrclass = "&nbsp;<img src=\"pic/warning.gif\" title=".get_user_class_name($CURUSER['class'])." alt=".get_user_class_name($CURUSER['class']).">&nbsp;";

elseif(get_user_class() >= UC_MODERATOR) $usrclass = "&nbsp;<a href=\"setclass.php\"><img src=\"pic/warning.gif\" title=\"".get_user_class_name($CURUSER['class'])."\" alt=\"".get_user_class_name($CURUSER['class'])."\" border=\"0\"></a>&nbsp;";

	blok_menu($tracker_lang['welcome_back'].($CURUSER ? "<a href=\"$DEFAULTBASEURL/userdetails.php?id=" . $CURUSER["id"] . "\">" . $CURUSER["username"] . "</a>&nbsp;".$usrclass."&nbsp;" : "гость" ) . $medaldon . $warn, $userbar , "155");
	echo "<br />";

	$mainmenu = "<a class=\"menu\" href=\"index.php\">&nbsp;".$tracker_lang['homepage']."</a>"
           ."<a class=\"menu\" href=\"browse.php\">&nbsp;".$tracker_lang['browse']."</a>"
           ."<a class=\"menu\" href=\"log.php\">&nbsp;".$tracker_lang['log']."</a>"
           ."<a class=\"menu\" href=\"rules.php\">&nbsp;".$tracker_lang['rules']."</a>"
           ."<a class=\"menu\" href=\"faq.php\">&nbsp;".$tracker_lang['faq']."</a>"
           ."<a class=\"menu\" href=\"topten.php\">&nbsp;".$tracker_lang['topten']."</a>"
           ."<a class=\"menu\" href=\"formats.php\">&nbsp;".$tracker_lang['formats']."</a>";

	blok_menu($tracker_lang['main_menu'], $mainmenu, "155");
	echo "<br />";

if ($CURUSER) {

	$usermenu = "<a class=\"menu\" href=\"my.php\">&nbsp;".$tracker_lang['my']."</a>"
           ."<a class=\"menu\" href=\"userdetails.php?id=".$CURUSER["id"]."\">&nbsp;".$tracker_lang['profile']."</a>"
           ."<a class=\"menu\" href=\"bookmarks.php\">&nbsp;".$tracker_lang['bookmarks']."</a>"
           ."<a class=\"menu\" href=\"mybonus.php\">&nbsp;".$tracker_lang['my_bonus']."</a>"
           ."<a class=\"menu\" href=\"invite.php\">&nbsp;".$tracker_lang['invite']."</a>"
           ."<a class=\"menu\" href=\"users.php\">&nbsp;".$tracker_lang['users']."</a>"
           ."<a class=\"menu\" href=\"friends.php\">&nbsp;".$tracker_lang['personal_lists']."</a>"
           ."<a class=\"menu\" href=\"subnet.php\">&nbsp;".$tracker_lang['neighbours']."</a>"
           ."<a class=\"menu\" href=\"mytorrents.php\">&nbsp;".$tracker_lang['my_torrents']."</a>"
           ."<a class=\"menu\" href=\"logout.php\">&nbsp;".$tracker_lang['logout']."!</a>";

	blok_menu($tracker_lang['user_menu'], $usermenu, "155");
	echo "<br />";

	$messages = "<a class=\"menu\" href=\"message.php\">&nbsp;".$tracker_lang['inbox']."</a>"
           ."<a class=\"menu\" href=\"message.php?action=viewmailbox&box=-1\">&nbsp;".$tracker_lang['outbox']."</a>";

	blok_menu($tracker_lang['messages'], $messages, "155");
	echo "<br />";

}

	$bt_clients = '&nbsp;&nbsp;<a href="http://bitconjurer.org/BitTorrent/download.html" target="_blank"><font class=small color=green>'.$tracker_lang['official'].'</font></a><br />'
  			.'&nbsp;&nbsp;<a href="http://azureus.sourceforge.net/" target="_blank"><font class=small color=green>Azureus (Java)</font></a><br />'
  			.'&nbsp;&nbsp;<a href="http://www.bittornado.com/" target="_blank"><font class=small color=green>BitTornado</font></a><br />'
  			.'&nbsp;&nbsp;<a href="http://www.bitcomet.com/" target="_blank"><font class=small color=green>BitComet</font></a><br />'
  			.'&nbsp;&nbsp;<a href="http://www.bitlord.com/" target="_blank"><font class=small color=green>BitLord</font></a><br />'
  			.'&nbsp;&nbsp;<a href="http://www.macupdate.com/info.php/id/7170" target="_blank"><font class="small" color=green>Acquisition (Mac)</font></a><br />'
  			.'&nbsp;&nbsp;<a href="http://www.167bt.com/intl/" target="_blank"><font class=small color=green>BitSpirit</font></a><br />'
  			.'<hr width=100% color=#ffc58c size=1>'
			.'<font class=small color=red>&nbsp;&nbsp;'.$tracker_lang['clients_recomened_by_us'].'</font>';

	blok_menu($tracker_lang['torrent_clients'], $bt_clients, "155");
	echo "<br />";

?>
</td>

<td align="center" valign="top" class="outer" style="padding-top: 5px; padding-bottom: 5px">
<?

if ($CURUSER && $unread) {
	print("<p><table border=0 cellspacing=0 cellpadding=10 bgcolor=red><tr><td style='padding: 10px; background: red'>\n");
	print("<b><a href=\"message.php\"><font color=white>".sprintf($tracker_lang['new_pms'],$unread)."</font></a></b>");
	print("</td></tr></table></p>\n");
}

if ($CURUSER['override_class'] != 255 && $CURUSER) { // Second condition needed so that this box isn't displayed for non members/logged out members.
	print("<p><table border=0 cellspacing=0 cellpadding=10 bgcolor=green><tr><td style='padding: 10px; background: green'>\n");
	print("<b><a href=$DEFAULTBASEURL/restoreclass.php><font color=white>".$tracker_lang['lower_class']."</font></a></b>");
	print("</td></tr></table></p>\n");
}
 
 show_blocks('c');