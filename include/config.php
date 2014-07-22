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

# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE'))
  die('Hacking attempt!');

$SITE_ONLINE = true;
//$SITE_ONLINE = local_user();
//$SITE_ONLINE = false;

$max_torrent_size = 1024 * 1024;
$announce_interval = 60 * 30;
$signup_timeout = 86400 * 3;
$minvotes = 1;
$max_dead_torrent_time = 6 * 3600;

// Max users on site
$maxusers = 10000;
$torrent_dir = 'torrents';

// Email for sender/return path.
$SITEEMAIL = 'noreply@' . $_SERVER['HTTP_HOST'];

$SITENAME = 'TBDev Yuna Scatari Edition';

$autoclean_interval = 900;
$pic_base_url = './pic';

// [BEGIN] Custom variables from Yuna Scatari
// TTL
$use_ttl = 1; // Использовать TTL.
$ttl_days = 28; // Сколько дней торрент может жить до TTL.
$avatar_max_width = 100; // Максимальная ширина аватары.
$avatar_max_height = 100; // Максимальная высота аватары.
$points_per_hour = 1; // Сколько добавлять бонусов в час, если пользователь сидирует.
$points_per_cleanup = $points_per_hour*($autoclean_interval/3600); // Don't change it!
$default_theme = 'TBDev'; // Тема по умолчанию.
$nc = 'no'; // Не пропускать на трекер пиров с закрытыми портами.
$default_language = 'russian'; // Язык трекера по умолчанию.
$deny_signup = 0; // Запретить регистрацию. 1 = регистрация отключена, 0 = регистрация включена.
$allow_invite_signup = 1; // Разрешить регистрацию через приглашения. 1 = разрешена, 0 = не разрешена.
$ctracker = 1; // Use CrackerTracker - anti-cracking system. I personaly think it's un-needed...
$use_email_act = 1; // Использовать активацию по почте, иначе - автоматическая активация при регистрации.
$use_wait = 1; // Использовать ожидание на пользователях которые имеют плохой рейтинг.
$use_lang = 1; // Включить языковую систему. Выключите если вы хотите перевести шаблоны и другие файлы - тогда все фразы от системы станут пустым местом.
$use_captcha = 1; // Использовать защиту от авто-регистраций.
$use_blocks = 1; // Использовать систему блоков. 1 - да, 0 - нет. Если ее отключить то админ-панель и ее блочный модуль не смогут нормально работать при работе с блоками.
$use_gzip = 1; // Использовать сжатие GZip на страницах.
$use_ipbans = 1; // Использовать функцию блокирования IP-адресов. 0 - нет, 1 - да.
$use_sessions = 1; // Использовать сессии. 0 - нет, 1 - да.
$smtptype = 'advanced'; // Тип отправки почты, по умолчанию advanced, лучше не менять
$allow_block_hide = true; // Разрешить сворачивание блоков
$check_for_working_mta = true; // Проверять работу почтового MTA при регистрации пользователя (TCP connect @ domain:25)
$force_private_tracker = true; // Yet not working
$max_image_size = 1024*1024; // 1mb
$allow_guests_details = false; // Разрешить гостям доступ к странице деталей торрента

$admin_email = 'admin@'.$_SERVER['HTTP_HOST']; // Почта администратора трекера, для формы обратной связи
$website_name = 'TBDev'; // Краткое имя сайта, для формы обратной связи

$enable_adv_antidreg = false; // Использовать продвинутую систему против двойных регистраций. Пояснение внизу:

$_COOKIE_SALT = 'default'; // Соль для cookie пользователей

/*
 * Если наш юзер сделал просто выход (ссылка "Выход" aka logout.php) то у него в куксах останется uid
 * При регистрации будет проверятся наличие этой куки.
 * Если она есть - то не будет давать регистрацию (сообщения "Регистрация невозможна").
 * Если-же ее нету, то проходит проверка по IP. В случае нахождения юзера с таким IP, и он отключен - тоже не пропустит + поставит куку от того uid
 * Вроде все довольно просто и в то-же время эффективно.
 * Спасибо StarLine в свое время за идею =)
 * По-умолчанию false т.к иногда бывают проблемы с таким,
 * особенно из-за включенного по-умолчанию авто-удаления пользователей через 28 дней неактивности
 */
// [END] Custom variables from Yuna Scatari

?>