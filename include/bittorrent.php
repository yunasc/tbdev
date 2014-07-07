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

/* 
 * Защита от двойного инклуда ядра
 * Protection from double including the core
*/

if (!defined('IN_TRACKER')) {
	// DEFINE IMPORTANT CONSTANTS
	define('IN_TRACKER', true);

	if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS'])) {
		echo 'Request tainting attempted.';
		exit;
	}

	// SET PHP ENVIRONMENT
	@error_reporting(E_ALL & ~E_NOTICE);
	@ini_set('error_reporting', E_ALL & ~E_NOTICE);
	@ini_set('display_errors', '1');
	@ini_set('display_startup_errors', '0');
	@ini_set('ignore_repeated_errors', '1');
	@ignore_user_abort(1);
	@set_time_limit(0);
	@set_magic_quotes_runtime(0);
	@session_start();
	define ('ROOT_PATH', dirname(dirname(__FILE__))."/");

$allowed_referrers = <<<REF

REF;

	// referrer check for POSTs; this is simply designed to prevent self-submitting
	// forms on foreign hosts from doing nasty things
	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' AND !defined('SKIP_REFERRER_CHECK')) {
		if ($_SERVER['HTTP_HOST'] OR $_ENV['HTTP_HOST']) {
			$http_host = ($_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_ENV['HTTP_HOST']);
		} else if ($_SERVER['SERVER_NAME'] OR $_ENV['SERVER_NAME']) {
			$http_host = ($_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : $_ENV['SERVER_NAME']);
		}

		if ($http_host AND $_SERVER['HTTP_REFERER']) {
			$http_host = preg_replace('#:80$#', '', trim($http_host));
			$referrer_parts = @parse_url($_SERVER['HTTP_REFERER']);
			if (isset($referrer_parts['port']))
				$ref_port = intval($referrer_parts['port']);
			else
				$ref_post = 80;
			$ref_host = $referrer_parts['host'] . ((!empty($ref_port) AND $ref_port != '80') ? ":$ref_port" : '');

			$allowed = preg_split('#\s+#', $allowed_referrers, -1, PREG_SPLIT_NO_EMPTY);
			$allowed[] = preg_replace('#^www\.#i', '', $http_host);
			$allowed[] = '.paypal.com';

			$pass_ref_check = false;
			foreach ($allowed AS $host) {
				if (preg_match('#' . preg_quote($host, '#') . '$#siU', $ref_host)) {
					$pass_ref_check = true;
					break;
				}
			}
			unset($allowed);

			if ($pass_ref_check == false)
				die('In order to accept POST request originating from this domain, the admin must add this domain to the whitelist.');
		}
	}

	function timer() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	// Some basic checking for engine to work
	// Check for PHP version
	if (version_compare(PHP_VERSION, '5.2.0', '<'))
		die('Извините, трекер работает на PHP от версии 5.2 и выше. Обновите версию PHP.');
	// Check for php-spl
	if (!interface_exists('ArrayAccess'))
		die('У вас не установлено расширение PHP SPL (Standard PHP Library). Без установки этого расширения дальнейшая работа невозможна.');

	// Additional security countermeasures
	// Will be enabled later...
	/*if (file_exists('install'))
		die('После установки нужно обязательно удалить папку install.');*/
	if (ini_get('register_globals') == '1' || strtolower(ini_get('register_globals')) == 'on')
		die('Отключите register_globals в php.ini/.htaccess (угроза безопасности)');
	if ((int) ini_get('short_open_tag') == '0')
		die('Включите short_open_tag в php.ini/.htaccess (техническое требование)');

	if (!file_exists('include/secrets.local.php'))
		die('Создайте файл include/secrets.local.php и переместите в него свои локальные настройки из include/secrets.php (техническое требование)');

	if (!file_exists('include/config.local.php'))
		die('Создайте файл include/config.local.php и переместите в него свои локальные настройки из include/config.php (техническое требование)');

	// Variables for Start Time
	$tstart = timer(); // Start time

	// INCLUDE BACK-END
	if (empty($rootpath))
		$rootpath = ROOT_PATH;

	require_once($rootpath . 'include/core.php');
}

?>