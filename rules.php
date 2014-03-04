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

dbconn();

//loggedinorreturn();

stdhead("Правила");

begin_main_frame();

?>

<? begin_frame("Общие правила - <font color=#004E98>За несоблюдение - удаление с трекера!</font>"); ?>
<ul>
<li>Данные правила не подлежат обсуждению и обязательны для выполнения всеми без исключения пользователями трекера рангом от <i>простого пользователя</i> до <i>модератора</i> (Администраторы и Дирекция - как лица, эти правила устанавливающие - поступают по своему усмотрению). Если вам не нравятся эти правила и вы хотите для себя другие правила - вы всегда можете создать свой собственный сайт и делать там все, что вам нравится.</li>
<li><b>Слово Администрации - закон для пользователей трекера!</b> В этом правиле нет исключений - <?=$SITENAME?> является частным битторрент трекером, и его политика определяется исключительно владельцами ресурса.</li>
<li><a name="warning"></a>Злостный нарушитель правил трекера получает предупреждение (<img src="pic/warned.gif"> ). Это значит, что <b>следующего</b> предупреждения не будет! Пользователь будет просто удален с трекера.</li>
<li>В случае постоянного нарушения правил, рецидивист будет забанен - доступ к трекеру с его IP адреса будет закрыт.</li>
<li>Не регистрируйте для себя несколько аккаунтов - мы регулярно проверяем нашу базу данных и без труда вычисляем умельцев такого рода.</li>
<li>Общение на форуме и на <?=$SITENAME?> ведётся на литературном <b>русском языке</b>. Другие языки вы можете использовать только в случае крайней необходимости. В случае отсутствия русской раскладки клавитуры, используйте <i>транслит</i> или <i>виртуальную клавитуру</i>.
<li>Мы рады любым предложениям и замечаниям, направленным на улучшение работы нашего трекера. Мы также просим вас сообщать Дирекции о любых ошибках и неточностях в работе сайте. Общие вопросы работы трекера вы можете обсужать в открытой дискуссии на <a href=forums.php>форуме</a>, все частные проблемы рекомендуется решать в <b>частной переписке</b>, используя систему личных сообщений.
<li>Используйте для регистрации только <b>рабочий</b> email адрес. Вы также всегда можете изменить его в вашем профиле пользователя. Мы не рекомендуем вам бесплатные почтовые службы <u>Mail.ru</u> или <u>hotmail.com</u> из-за используемых на них агрессивных системах анти-спама. Если вы долго не получаете письмо с подтверждением о регистрации/перерегистрации, смотрите <a href=./faq.php>ЧаВо</a> или используйте другую почтовую службу. В свою очередь мы заверяем вас, что ваш почтовый адрес, равно как любая другая ваша персональная информация, не будет использована ни в каких других целях и никогда не будет передана третьим лицам. Мы рекомендуем вам также указать дополнительный способ для связи (номер <a href=http://www.icq.com>ICQ</a>, имя в <a href=http://www.skype.com>Skype</a> или в <a href=http://www.msn.com>MSN</a>) с вами в том случае, если возникнет такая необходимость. </li>

<? end_frame(); ?>
<? begin_frame("Правила закачек - <font color=#004E98>Важнее не бывает!</font>"); ?>
<ul>
<li>Доступ к файлам нашего трекера ничем не ограничен - для вашего удобства мы отключили <i>antileech</i> систему. Но это не значит, что мы не можем включить ее вновь.</li>
<li>По умолчанию, вы можете быть <b>пиром одновременно на 4 различных раздачах</b>. Если ваш канал позволяет большее, вы можете обратится с соответствующей просьбой к одному из администраторов.
<li>Администрация сайта относится с уважением к нашим пользователям и не намерена ограничивать возможности использовать трекер "по умолчанию". Мы надеемся что вы так же будете относится к другим пользователям, раздающим и к Администрации.</li>
<li>Оптимальное соотношение "Скачал/Отдал" равно 1, то есть сколько скачал - столько и отдал. Стремитесь поддерживать его, если хотите долгой и безоблачной жизни на нашем трекере, как врочем, и на любом другом: не закрывайте ваш БитТоррент клиент как можно дольше! В случае, если вы остались единственных сидом (владельцем полной копии) на раздаче, пожалуйста оставайтесь на раздаче, если даже ваш рейтинг выше 1 - сегодня вы помогли кому-то, завтра он поможет вам.</li>
<li>Если у вас возникают общие проблемы со скачиванием (трекер неправильно отображает вашу статистику, вы не можете подключится к трекеру, скорость ваших закачек очень низкая), прочтите наши <a href=./faq.php>ЧаВо</a> - вы найдёте в них всю необходимую вам информацию. В случае возникновения каких-либо вопросов по конкретной раздаче, просьба обращаться не к администрации, а к раздающему, используя систему Личных Сообщений или Комментариев к раздаче.</li>
<? end_frame(); ?>
<? begin_frame("Правила комментирования торрентов<!-- - <font color=#004E98>Please follow these guidelines or else you might end up with a warning!</font>-->"); ?>
<? begin_frame(); ?>
<b>Комментарии, содержащие интересные сведения и/или доброжелательные, остроумные и веселые - флудом не являются, и мы им только рады!</b>
<? end_frame(); ?>
<ul>
<li>Система комментариев торрентов создана для того чтобы: (1) высказать свое <i>уважение</i> и <i>благодарность</i> раздающему, (2) задать интересующий вас <i>конкретный технический вопрос</i> относительно раздачи или релиза,(3) сообщить <i>интересную информацию</i>, относящуюся к раздаче.
<li>Если вы не намерены подключаться к раздаче, не комментируйте ее.
<li>Никакого другого языка кроме литературного русского в комментариях.
<li>Запрещены флэйм и флуд.
<li>Запрещены ссылки на варез-порталы.
<li>Запросы серийных номеров, крэков и патчей <b>запрещены</b>.
<li>Помните, раздачи на нашем трекере ни в коей мере не предназначены для удовлетворения ваших личных запросов: если вам не нравится раздача, попробуйте поискать что-либо подходящее вам в другом месте.</li>
<? end_frame(); ?>
<? begin_frame("Рекомендации к аватарам - <font color=#004E98>Убедительная просьба следовать нижеизложенным правилам</font>"); ?>
<ul>
<li>Разрешены форматы .gif, .jpg и .png.</li>
<li>Рекомендуемые параметры: <b><?=$avatar_max_width;?> X <?=$avatar_max_height;?> пикселей</b> в ширину и не размером более 150 Kб.</li>
<li>Не используйте оскорбительные материалы (а именно: религиозные и политические материалы, материалы, изображающие жестокость, насилие и порнографию). Сомневаетесь? Спросите <a href=staff.php>Администрацию</a>.</li>
<? end_frame(); ?>

<? if (get_user_class() >= UC_UPLOADER) { ?>

<? begin_frame("Правила загрузок - <font color=#004E98>Torrents violating these rules may be deleted without notice</font>"); ?>
<ul>
<li>All uploads must include a proper NFO.</li>
<li>Only scene releases. If it's not on <a href=redir.php?url=http://www.nforce.nl class=altlink>NFOrce</a> or <a href=http://www.grokmusiq.com/ class=altlink>grokMusiQ</a> then forget it!</li>
<li>The stuff must not be older than seven (7) days.</li>
<li>All files must be in original format (usually 14.3 MB RARs).</li>
<li>Pre-release stuff should be labeled with an *ALPHA* or *BETA* tag.</li>
<li>Make sure not to include any serial numbers, CD keys or similar in the description (you do <b>not</b> need to edit the NFO!).</li>
<li>Make sure your torrents are well-seeded for at least 24 hours.</li>
<li>Do not include the release date in the torrent name.</li>
<li>Stay active! You risk being demoted if you have no active torrents.</li>
</ul>
<br />
<ul>
If you have something interesting that somehow violate these rules (e.g. not ISO format), ask a mod and we might make an exception.

<? end_frame(); ?>

<? } if (get_user_class() >= UC_MODERATOR) { ?>

<? begin_frame("Звания на $SITENAME - <font color=#004E98>Информация к размышлению</font>"); ?>
<br />
<table border=0 cellspacing=3 cellpadding=0>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b><font color="black">Пользователь</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Обычный, нормальный пользователь трекера</td></tr>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; &nbsp;&nbsp;<b><font color="#D21E36">Опытный&nbsp;пользователь</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Трекер автоматически присваивает (и отбирает) это звание у пользователей, чей аккаунт активен не менее 4 недель, кто залил более 25 GB и имеет рейтинг 1.05. Модератор может вручную присвоить этот статус до следующего автоматического исполнения скрипта.</td>
</tr>
<!--<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b><img src="pic/star.gif"></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>This status is given ONLY by Redbeard since he is the only one who can verify that they actually donated something.</td>
</tr>-->
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b><font color="#9C2FE0">VIP</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Человек, оказывающий финансовую или другую помощь сайту</td>
</tr>
<!--<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b>Other</b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Специальное звание для друзей и верных зрителей <?=$SITENAME?>.</td>
</tr>-->
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b><font color="orange">Заливающий</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Пользователь с правом раздавать на <?=$SITENAME?>. Присваивается Администраторами. Есть подходящие кандидаты? Пишите в приват, не стесняйтесь.</td>
</tr>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b><font color="red">Модератор</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Назначаются Администрацией и имеют функции модераторов.</td>
</tr>
<tr>
	<td class=embedded bgcolor="#F5F4EA" valign=top>&nbsp; <b><font color="green">Администратор</font></b></td>
	<td class=embedded width=5>&nbsp;</td>
	<td class=embedded>Ваше непосредственное начальство.</td>
</tr>
</table>
<br />
<?
	end_frame();
	begin_frame("Правила модерирования - <font color=#004E98>Use your better judgement!</font>");
?>
<ul>
<!--<li>The most important rule: Use your better judgment!</li>-->
<li>Не бойтесь сказать <b>НЕТ</b>! (a.k.a. "Helshad's rule".)
<!--<li>Don't defy another mod in public, instead send a PM or through IM.</li>-->
<li>Будьте толерантными! Дайте пользователю(ям) шанс на реабилитацию.</li>
<!--<li>Don't act prematurely, let the users make their mistakes and THEN correct them.</li>-->
<li>Старайтесь исрпвлять любой "офф топик" вместо закрывания темы.</li>
<li>Перемещайте темы вместо того что-бы закрывать.</li>
<!--<li>Be tolerant when moderating the Chit-chat section (give them some slack).</li>-->
<li>Если вы закрыли тему, напишите короткое обьяснение почему вы ее закрыли.</li>
<li>Прежде чем отключить аккаунт, напишите ему/ей ЛС и если они ответят, установите им испытательный срок на 2 недели.</li>
<li>Не отключайте аккаунт пользователя пока он или она не были членом хотя-бы 4 недели.</li>
<li><b>Всегда</b> указывайте причину (в поле комментария) почему вы забанили / предупредили пользователя.</li>
<br />

<?
	end_frame();
	begin_frame("Возможности модераторов - <font color=#004E98>Какие мои привелегии как модератора?</font>");
?>
<ul>
<li>Вы можете удалять и редактировать посты в форуме.</li>
<li>Вы можете удалять и редактировать торренты.</li>
<li>Вы можете удалять и редактировать аватары пользователей.</li>
<li>Вы можете отключать пользователей.</li>
<li>Вы можете редактировать тайтлы VIP'ам.</li>
<li>Вы можете видеть полную информацию о пользователях.</li>
<li>Вы можете добавлять коментарии к пользователям (для других модераторов и администраторов).</li>
<li>Вы можете перестать читать потому-что вы уже знаете про эти возможности. ;)</li>
<li>В конце концов посмотрите страничку <a href=staff.php class=altlink>Администрация</a> (правый верхний угол).</li>

<? end_frame(); ?>

<p align=right><font size=1 color=#004E98><b>Правила отредактированы 30.07.2006 (03:41 GMT+2)</b></font></p>

<? }
end_main_frame();
stdfoot(); ?>