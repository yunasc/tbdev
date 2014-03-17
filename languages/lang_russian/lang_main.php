<?php

class Language implements arrayaccess {
	private $tlr = array();
	public function __construct() {
		$this->tlr = (array) require_once('lang_constants.php');
	}

    public function offsetSet($offset, $value) {
    	if ($this->offsetExists($offset))
    		die('Changing already existing values is prohibited');
        $this->tlr[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->tlr[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->tlr[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->tlr[$offset]) ? $this->tlr[$offset] : 'NO_LANG_'.strtoupper($offset);
    }
}

$tracker_lang = new Language;

?>