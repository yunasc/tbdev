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
if (!defined("IN_TRACKER"))
  die("Hacking attempt!");

// INCLUDE/REQUIRE BACK-END
require_once($rootpath . 'include/init.php');
require_once($rootpath . 'include/global.php');
require_once($rootpath . 'include/config.php');
require_once($rootpath . 'include/config.local.php');
require_once($rootpath . 'include/functions.php');
require_once($rootpath . 'include/blocks.php');
require_once($rootpath . 'include/secrets.php');
require_once($rootpath . 'include/secrets.local.php');

// INCLUDE SECURITY BACK-END
if ($ctracker) require_once($rootpath . 'include/ctracker.php');

// LOAD GZIP/OUTPUT BUFFERING
if ($use_gzip) gzip();

// IMPORTANT CONSTANTS
define ("BETA", 0); // Set 0 to remove *BETA* notice.
define ("BETA_NOTICE", "\n<br />Внимание! Версия не для промышленого использования!");
define ("DEBUG_MODE", 0); // Shows the queries at the bottom of the page.

// BACKWARD CODE COMPATIBILITY
if (!isset($HTTP_POST_VARS) && isset($_POST)) {
	$HTTP_POST_VARS = $_POST;
	$HTTP_GET_VARS = $_GET;
	$HTTP_SERVER_VARS = $_SERVER;
	$HTTP_COOKIE_VARS = $_COOKIE;
	$HTTP_ENV_VARS = $_ENV;
	$HTTP_POST_FILES = $_FILES;
}

// STRIP MAGIC QUOTES FROM REQUEST
if (get_magic_quotes_gpc()) {
	if (!empty($_GET))    { $_GET    = strip_magic_quotes($_GET);    }
	if (!empty($_POST))   { $_POST   = strip_magic_quotes($_POST);   }
	if (!empty($_COOKIE)) { $_COOKIE = strip_magic_quotes($_COOKIE); }
}
// DO SOME EXTRA STUFF
if (!get_magic_quotes_gpc()) {
	if (is_array($HTTP_GET_VARS)) {
		while (list($k, $v) = each($HTTP_GET_VARS)) {
			if (is_array($HTTP_GET_VARS[$k])) {
				while (list($k2, $v2) = each($HTTP_GET_VARS[$k])) {
					$HTTP_GET_VARS[$k][$k2] = addslashes($v2);
				}
				@reset($HTTP_GET_VARS[$k]);
			} else {
				$HTTP_GET_VARS[$k] = addslashes($v);
			}
		}
		@reset($HTTP_GET_VARS);
	}

	if (is_array($HTTP_POST_VARS)) {
		while (list($k, $v) = each($HTTP_POST_VARS)) {
			if (is_array($HTTP_POST_VARS[$k])) {
				while (list($k2, $v2) = each($HTTP_POST_VARS[$k])) {
					$HTTP_POST_VARS[$k][$k2] = addslashes($v2);
				}
				@reset($HTTP_POST_VARS[$k]);
			} else {
				$HTTP_POST_VARS[$k] = addslashes($v);
			}
		}
		@reset($HTTP_POST_VARS);
	}

	if (is_array($HTTP_COOKIE_VARS)) {
		while (list($k, $v) = each($HTTP_COOKIE_VARS)) {
			if (is_array($HTTP_COOKIE_VARS[$k])) {
				while (list($k2, $v2) = each($HTTP_COOKIE_VARS[$k])) {
					$HTTP_COOKIE_VARS[$k][$k2] = addslashes($v2);
				}
				@reset($HTTP_COOKIE_VARS[$k]);
			} else {
				$HTTP_COOKIE_VARS[$k] = addslashes($v);
			}
		}
		@reset($HTTP_COOKIE_VARS);
	}
}

?>