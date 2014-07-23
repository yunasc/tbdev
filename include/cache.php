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

$cache = Cache_Init::Create($cache_type, $cache_config);


class Cache_Init {
	/**
	 * @param string $cache_type
	 * @param array $cache_config
	 * @throws Exception|UnexpectedValueException
	 * @return Cache_FileCache|Cache_XCache|Cache_eAccelerator|Cache_Memcache
	 */
	static function Create($cache_type, $cache_config = array()) {
		$class_name = 'Cache_' . $cache_type;
		if (class_exists($class_name)) {
			try {
				if (method_exists($class_name, 'config'))
					return new $class_name($cache_type, $cache_config);
				else
					return new $class_name($cache_type);
			} catch (\Exception $e) {
				throw new Exception($e->getMessage());
			}
		} else
			throw new UnexpectedValueException('Selected cache type of ' . $cache_type . ' doesn\'t exists.');
	}
}

abstract class Cache_Common {
	public $cache_enabled = true;
	protected $used = false;

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
	 * Check if selected cache type is installed on system
	 * @return bool
	 */
	abstract function is_installed();

	/**
	 * Returns value of variable and return true on success
	 * @param string $name
	 * @return bool
	 */
	abstract function get($name);

	/**
	 * Store value of variable and return true on success
	 * @param string $name
	 * @param mixed $value
	 * @param int $ttl
	 * @return bool
	 */
	abstract function set($name, $value, $ttl = 0);

	/**
	 * Remove variable and return true on success
	 * @return bool
	 */
	abstract function remove($name);
}

class Cache_FileCache extends Cache_Common {
	public $default_ttl = 600;
	protected $used = true;
	protected $cache_folder = './cache/';

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
		return (bool)xcache_isset($name) && $this->cache_enabled;
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
	protected $ip = '127.0.0.1'; // Not really needed here
	protected $port = 11211; // Not really needed here

	/**
	 * Create Memcache class and connect to Memcache server
	 * @param string $cache_type
	 * @param $cache_config
	 * @throws Exception
	 */
	function __construct($cache_type, $cache_config) {
		parent::__construct($cache_type);
		if (is_null($cache_config))
			throw new Exception('Memcache class requires configuration. Please, use include/config.local.php to modify ones.');
		$this->_memcache = new Memcache();
		$this->config($cache_config);
		$this->_memcache->connect($this->ip, $this->port) or die ("Could not connect Memcache");
	}

	/**
	 * @param array $config
	 * @return bool
	 */
	function config(array $config){
		foreach($config as $name => $value)
			$this->$name = $value;
		return true;
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
		return $this->_memcache->set($name, serialize($value), 0, $ttl);
	}

	/**
	 * @param $name
	 * @return bool
	 */
	function exists($name) {
		return (bool)$this->_memcache->get($name) && $this->cache_enabled;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	function remove($name) {
		return $this->_memcache->delete($name);
	}
}

class Cache_Redis extends Cache_Common {
	protected $ip = '127.0.0.1'; // Not really needed here
	protected $port = 6379; // Not really needed here

	/**
	 * Connect
	 */
	function __construct($cache_type, $cache_config) {
		throw new Exception('Redis cache class is not complete. Please use different cache mechanism.');
		parent::__construct($cache_type);
		if (is_null($cache_config))
			throw new Exception('Redis cache requires configuration. Please, use include/config.local.php to modify ones.');
		$this->_redis = new Redis();
		$this->config($cache_config);
		$this->_redis->open($this->ip, $this->port) or die ("Could not connect Memcache");
	}

	function config(array $config){
		foreach($config as $name => $value)
			$this->$name = $value;
	}

	/**
	 * @return bool
	 */
	function is_installed() {
		return class_exists('Redis');
	}

	/**
	 * @param string $name
	 * @return bool|mixed
	 */
	function get($name) {
		return $this->_redis->get($name);
	}

	/**a
	 * @param string $name
	 * @param mixed $value
	 * @param int $ttl
	 * @return bool
	 */
	function set($name, $value, $ttl = 0) {
		return $this->_redis->set($name, serialize($value), TRUE, $ttl);
	}

	/**
	 * @param $name
	 * @return bool
	 */
	function exists($name) {
		return (bool)$this->_redis->get($name) && $this->cache_enabled;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	function remove($name) {
		return $this->_redis->delete($name);
	}
}

?>