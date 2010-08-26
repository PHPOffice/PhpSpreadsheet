<?php

class PHPExcel_CachedObjectStorageFactory {
	const cache_in_memory				= 'Memory';
	const cache_in_memory_gzip			= 'MemoryGZip';
	const cache_in_memory_serialized	= 'MemorySerialized';
	const cache_to_discISAM				= 'DiscISAM';
	const cache_to_apc					= 'APC';
	const cache_to_memcache				= 'Memcache';
	const cache_to_phpTemp				= 'PHPTemp';
	const cache_to_wincache				= 'Wincache';


	private static $_cacheStorageMethod = null;

	private static $_cacheStorageClass = null;


	private static $_storageMethods = array(
		self::cache_in_memory,
		self::cache_in_memory_gzip,
		self::cache_in_memory_serialized,
		self::cache_to_phpTemp,
		self::cache_to_discISAM,
		self::cache_to_apc,
		self::cache_to_memcache,
		self::cache_to_wincache,
	);


	private static $_storageMethodDefaultParameters = array(
		self::cache_in_memory				=> array(
													),
		self::cache_in_memory_gzip			=> array(
													),
		self::cache_in_memory_serialized	=> array(
													),
		self::cache_to_phpTemp				=> array( 'memoryCacheSize'	=> '1MB'
													),
		self::cache_to_discISAM				=> array(
													),
		self::cache_to_apc					=> array( 'cacheTime'		=> 600
													),
		self::cache_to_memcache				=> array( 'memcacheServer'	=> 'localhost',
													  'memcachePort'	=> 11211,
													  'cacheTime'		=> 600
													),
		self::cache_to_wincache				=> array( 'cacheTime'		=> 600
													)
	);


	private static $_storageMethodParameters = array();


	public static function getCacheStorageMethod() {
		if (!is_null(self::$_cacheStorageMethod)) {
			return self::$_cacheStorageMethod;
		}
		return null;
	}	//	function getCacheStorageMethod()


	public static function getCacheStorageClass() {
		if (!is_null(self::$_cacheStorageClass)) {
			return self::$_cacheStorageClass;
		}
		return null;
	}	//	function getCacheStorageClass()


	public static function getCacheStorageMethods() {
		return self::$_storageMethods;
	}	//	function getCacheStorageMethods()


	public static function initialize($method = self::cache_in_memory, $arguments = array()) {
		if (!in_array($method,self::$_storageMethods)) {
			return false;
		}

		switch($method) {
			case self::cache_to_apc	:
				if (!function_exists('apc_store')) {
					return false;
				}
				if (apc_sma_info() === false) {
					return false;
				}
				break;
			case self::cache_to_memcache :
				if (!function_exists('memcache_add')) {
					return false;
				}
				break;
			case self::cache_to_wincache :
				if (!function_exists('wincache_ucache_add')) {
					return false;
				}
				break;
		}

		self::$_storageMethodParameters[$method] = self::$_storageMethodDefaultParameters[$method];
		foreach($arguments as $k => $v) {
			if (isset(self::$_storageMethodParameters[$method][$k])) {
				self::$_storageMethodParameters[$method][$k] = $v;
			}
		}

		if (is_null(self::$_cacheStorageMethod)) {
			self::$_cacheStorageClass = 'PHPExcel_CachedObjectStorage_'.$method;
			self::$_cacheStorageMethod = $method;
		}
		return true;
	}	//	function initialize()


	public static function getInstance(PHPExcel_Worksheet $parent) {
		if (is_null(self::$_cacheStorageMethod)) {
			self::initialize();
		}

		$instance = new self::$_cacheStorageClass($parent,self::$_storageMethodParameters[self::$_cacheStorageMethod]);
		if (!is_null($instance)) {
			return $instance;
		}

		return false;
	}	//	function getInstance()

}