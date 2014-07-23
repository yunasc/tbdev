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

# IMPORTANT: Do not edit below unless you know what you are doing!
if (!defined('IN_TRACKER') && !defined('IN_ANNOUNCE'))
    die("Hacking attempt!");

$cache = Cache_Init::Create($cache_type);

class Cache_Init {
    static function Create($cache_type) {
        $class_name = 'Cache_'.$cache_type;
        if (class_exists($class_name)) {
            try {
                return new $class_name($cache_type);
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        } else
            die('Selected cache type of ' . $cache_type . ' doesn\'t exists.');
    }
}

class Cache_Common {
    protected $used = false;
    public $cache_enabled = true;

    /**
     * @param string $cache_type
     * @throws Exception
     */
    function __construct($cache_type) {
        if (!$this->is_installed()) {
            throw new Exception('Error: ' . $cache_type . ' extension not installed');
        }
    }

    /**
     * Returns value of variable and return true on success
     * @param string $name
     * @return bool
     */
    function get($name) {
        return false;
    }

    /**
     * Store value of variable and return true on success
     * @param string $name
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    function set($name, $value, $ttl = 0) {
        return false;
    }

    /**
     * Remove variable and return true on success
     * @return bool
     */
    function remove($name) {
        return false;
    }

    /**
     * Check if selected cache type is installed on system
     * @return bool
     */
    function is_installed() {
        return false;
    }
}

class Cache_FileCache extends Cache_Common {
    protected	$used			= true;
    protected	$cache_folder	= './cache/';
    public		$default_ttl	= 600;
    /**
     * @return bool
     */
    function is_installed() {
        return file_exists($this->cache_folder) && is_writable($this->cache_folder);
    }

    /**
     * @param string $name
     * @return bool|mixed
     */
    function get($name) {
        return unserialize(file_get_contents($this->cache_folder . $name));
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    function set($name, $value, $ttl = 0) {
        // Real TTL is not possible with FileCache
        return file_put_contents($this->cache_folder . $name, serialize($value));
    }

    /**
     * @param $name
     * @return bool
     */
    function exists($name) {
        return file_exists($this->cache_folder . $name) && (TIMENOW - $this->default_ttl < filemtime($this->cache_folder . $name)) && filesize($this->cache_folder . $name);
    }

    /**
     * @param $name
     * @return bool
     */
    function remove($name) {
        return unlink($this->cache_folder . $name);
    }
}

class Cache_XCache extends Cache_Common {
    protected $used = true;

    /**
     * @return bool
     */
    function is_installed() {
        return function_exists('xcache_get');
    }

    /**
     * @param string $name
     * @return bool|mixed
     */
    function get($name) {
        return unserialize(xcache_get($name));
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    function set($name, $value, $ttl = 0) {
        return xcache_set($name, serialize($value), $ttl);
    }

    /**
     * @param $name
     * @return bool
     */
    function exists($name) {
        return (bool) xcache_isset($name) && $this->cache_enabled;
    }

    /**
     * @param $name
     * @return bool
     */
    function remove($name) {
        return xcache_unset($name);
    }
}

class Cache_eAccelerator extends Cache_Common {
    protected $used = true;

    /**
     * @return bool
     */
    function is_installed() {
        return function_exists('eaccelerator_get');
    }

    /**
     * @param string $name
     * @return bool|mixed
     */
    function get($name) {
        return unserialize(eaccelerator_get($name));
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    function set($name, $value, $ttl = 0) {
        return eaccelerator_put($name, serialize($value), $ttl);
    }

    /**
     * @param string $name
     * @return bool
     */
    function exists($name) {
        return eaccelerator_get($name) && $this->cache_enabled;
    }

    /**
     * @param string $name
     * @return bool
     */
    function remove($name) {
        return eaccelerator_rm($name);
    }
}

class Cache_Memcache extends Cache_Common {
    protected	$ip	    = '127.0.0.1';
    protected	$port	= 11211;

    /**
     * Connect
     */
    function __construct() {
        $this->_memcache = new Memcache();
        $this->_memcache->connect($this->ip, $this->port) or die ("Could not connect Memcache");
    }

    /**
     * @return bool
     */
    function is_installed() {
        return extension_loaded('memcache');
    }

    /**
     * @param string $name
     * @return bool|mixed
     */
    function get($name) {
        return $this->_memcache->get($name);
    }

    /**a
     * @param string $name
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    function set($name, $value, $ttl = 0) {
        return $this->_memcache->set($name, serialize($value), TRUE, $ttl);
    }

    /**
     * @param $name
     * @return bool
     */
    function exists($name) {
        return (bool) $this->_memcache->get($name) && $this->cache_enabled;
    }

    /**
     * @param $name
     * @return bool
     */
    function remove($name) {
        return $this->_memcache->delete($name);
    }
}
?>