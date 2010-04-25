<?php
/**
Mp3 Captcha Audio Addon Class - scripts.titude.nl 2007(c)
Filename: mp3captcha.php
A Php Audio Addon for existing image captcha's
Requirements: PHP 5 or up

Update 12-2007 - added a silent.mp3 to prevent abrupt sound ending
on the new 7.x quicktime player with IE 7

Note: Since this audio addon makes no difference between
upper and lowercase characters make sure that when validating
your captcha session with the captcha post data that both these
values are set to lowercase for validation, like:

if (strtolower($_POST['your_captchaval']) == strtolower($_SESSION['your_captchasession'])) {
	echo "It passed";
}

Javascript and Html to use with your form

<script language="javascript" type="text/javascript"><!--
	// Part of Mp3Captcha (c) scripts.titude.nl 2007 - leave this comment 
	// url of the Mp3Captcha.php ( to webroot ) 
	var mp3cf = "/captcha/mp3captcha.php";
	// click delay 1500 = 1.5 sec
	var delaytime = 1500;
	var delayer = false;
	var msie = navigator.userAgent.toLowerCase();
	msie = (msie.indexOf("msie") > -1) ? true : false;
	function captchaMp3() {
		var d = new Date();
		if (delayer) {
			return false;
		}
		delayer = true;
		setTimeout('resetdelay()', delaytime);
		if (document.all && msie) { 	
			embed = document.createElement("bgsound");
			embed.setAttribute("src", mp3cf + "?nc=" + d.getTime());
			document.getElementsByTagName("body")[0].appendChild(embed);
		} else if (document.getElementById) { 
			var mp3player = '<embed src="'+mp3cf + "?nc=" + d.getTime()+'"';
			mp3player += ' hidden="true" type="audio/x-mpeg" autostart="true" />';
			document.getElementById('codecf').innerHTML = mp3player; 
			return true;
		}
	}
	function resetdelay() {
		delayer = false;
	}
	//--></script>
<div id="codecf" style="position: absolute; width: 1px; height: 1px; visible: hidden;">
</div>

<a href="javascript:captchaMp3();void(0)" onmouseover="window.status=''; return true;">Listen to code</a>

- The English (US)  alphabet and number sound files originate from:
  Quadravox Inc - http://www.quadravox.com/
- The dutch ( belgian ) alphabet and number sound files originate from:
  Dutch Grammar  - http://www.dutchgrammar.com/
- The English (EN) , France and German alphabet and number sound files originate from:
  LanguageGuide.org  - http://www.languageguide.org/
( Thanks a lot for letting me use these )

Used settings for the mp3 files: 22050 Hz - 24 kb/s Mono to keep overall size small
*/

/********************************************************************************
* @ Mp3 Captcha Audio Addon Class - scripts.titude.nl 2007(c)
********************************************************************************/
class mp3captcha {
/********************************************************************************
* @ DEFAULT CONFIG Audio Captcha Addon
********************************************************************************/	
		// path to the sound dir with language dirs
	public $sounddir = "sounds";
		// default language dir -
		//  Language dirs Lowercase Country Code - ISO 3166
	public $language = "en";
		// prefix of mp3 file ( optional )
	public $prefix = "";
		// Sample path: sounds/en/a.mp3

		// use charmap.php in sound dirs to map sounds
	public $mapping = false;
	
		// internal values - private
	private $mp3str = "";
	private $mp3tag = "";
	private $ccode = "";
	private $deflang = "";
 
/********************************************************************************
* @ public function mp3captcha()
* @ sets default language + var with captcha session value
********************************************************************************/   
	public function __construct($code = "") {
		$this->ccode = $code;
		$this->deflang = $this->language;
	}

/********************************************************************************
* @ public function mp3stitch()
* @ Couple soundfiles to the captcha code characters
********************************************************************************/   
	public function mp3stitch() {
		if ($this->ccode != "") {
			$mp3s = array();
			$this->ccode = strtolower($this->ccode);
			if (substr($this->sounddir, -1 ) == '/') {
				$this->sounddir = substr($this->sounddir, 0, -1);
			}
				// Choose language dir
			$sdir = $this->sounddir . "/" . strtolower($this->language);
				// If not present choose default language dir
			if (strlen($this->language) != 2 || !is_dir($sdir)) {
				$sdir = $this->sounddir . "/" . $this->deflang;
			}
			$charmap = array();
			if ($this->mapping && file_exists($sdir . "/charmap.php")) {
					include_once($sdir . "/charmap.php");
			}
			for ($i = 0; $i < strlen($this->ccode); $i++) {
				$cdir = array();
				$cdir[] = $sdir;
				if ($this->mapping && !empty($charmap) && $this->ccode[$i] != "") {
					foreach ($charmap as $key=>$val) {
						if (is_dir($this->sounddir . "/" . $key) && stristr($val, $this->ccode[$i])) {
							$cdir[] = $this->sounddir . "/" . $key;
						}					
					}
				}
				shuffle($cdir);
				if (file_exists($cdir[0] . "/" . $this->prefix . strtolower($this->ccode[$i]) . ".mp3")) {
					$mp3s[$i] = $cdir[0] . "/" . $this->prefix . strtolower($this->ccode[$i]) . ".mp3";
				}
			}
				// Captha length == Number of soundfiles
			if (strlen($this->ccode) == count($mp3s)) {
				foreach ($mp3s as $mp3) {
						// Actual mp3 join here
					$this->mp3add($mp3);
				}
				// added a 1.5 sec silent mp3 - to prevent abrupt sound ending ( new quicktime 7.x on IE 7 )
				if (file_exists($this->sounddir . "/silent.mp3")) {
					$this->mp3add($this->sounddir . "/silent.mp3");
				}
			}
		}
			// Stream out
		$this->mp3stream();
	}
  
/********************************************************************************
* @ private function mp3add()
* @ Open a mp3 - strip it tags and add it to string
********************************************************************************/         
	private function mp3add($mp3 = "") {
		$mp3tmp = $this->mp3str;
		if ($mp3 != "" && file_exists($mp3)) {
			$this->mp3str = file_get_contents($mp3);
			$this->mp3strip();
			$this->mp3str = $mp3tmp . $this->mp3str;
			return true;
		} else if ($mp3 == "") {
			return true;
		}
		return false;
	}

/********************************************************************************
* @ private function mp3strip()
* @ Strips begin and end tags from mp3 string
* @ Set the mp3 header ( from first file )
********************************************************************************/
	private function mp3strip() {
		$i = 0;
		for ($i = 0; $i < strlen($this->mp3str); $i++) {
			if (ord(substr($this->mp3str, $i, 1)) == 255) {
				break;
			}
		}
		$mp3tmp = $this->mp3str;
		$this->mp3str = substr($this->mp3str, $i);
		if ($this->mp3tag == "") {
			$this->mp3tag = str_replace($this->mp3str, "" , $mp3tmp);
		}
		if (strtolower(substr(substr($this->mp3str,(strlen($this->mp3str) - 128)), 0, 3)) == "tag") {
			$this->mp3str = substr($this->mp3str, 0, (strlen($this->mp3str) - 129));
		}
	}

/********************************************************************************
* @ private function mp3stream()
* @ Output the new mp3 file
********************************************************************************/	
	private function mp3stream() {
		$this->mp3str = $this->mp3tag . $this->mp3str;
		header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: inline; filename=captcha.mp3");
		header("Content-type: audio/mpeg");
		header("Cache-Control: post-check=0, pre-check=0");
		header("Pragma: public");
		header("Connection: close");
		header("Content-Length: " . strlen($this->mp3str));
		echo $this->mp3str;
    }
}
/********************************************************************************
Copyright (c) 2007, scripts.titude.nl - all rights reserved.
Author: scripts-AT-do-Not-Spam-titude.nl - Netherlands

Disclaimer & License

NOTE THAT THE RIGHTS OF THE USED AUDIO FILES LAY WITH THE RESPECTED
OWNERS AND THAT THE USE IS GRANTED TO USE THEM WITH THIS SCRIPT

THE AUTHOR MAKES NO REPRESENTATIONS OR WARRANTIES, EXPRESS OR
IMPLIED. BY WAY OF EXAMPLE, BUT NOT LIMITATION, THE AUTHOR MAKES NO 
REPRESENTATIONS OR WARRANTIES OF MERCHANTABILITY OR FITNESS FOR
ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SCRIPT, COMPONENTS, 
OR DOCUMENTATION WILL NOT INFRINGE ANY PATENTS, COPYRIGHTS, 
TRADEMARKS, OR OTHER RIGHTS. THE AUTHOR SHALL NOT BE HELD LIABLE 
FOR ANY LIABILITY NOR FOR ANY DIRECT, INDIRECT, OR CONSEQUENTIAL 
DAMAGES WITH RESPECT TO ANY CLAIM BY RECIPIENT OR ANY THIRD PARTY 
ON ACCOUNT OF OR ARISING FROM THIS AGREEMENT OR USE OF THIS SCRIPT
AND ITS COMPONENTS.

Released under GNU Lesser General Public License - http://www.gnu.org/licenses/lgpl.html

********************************************************************************/
?>