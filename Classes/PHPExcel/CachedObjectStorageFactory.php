<?php

class PHPExcel_CachedObjectStorageFactory {
	const cache_in_memory				= 'Memory';
	const cache_in_memory_gzip			= 'MemoryGZip';
	const cache_in_memory_serialized	= 'MemorySerialized';
	const cache_igbinary				= 'Igbinary';
	const cache_to_discISAM				= 'DiscISAM';
	const cache_to_apc					= 'APC';
	const cache_to_memcache				= 'Memcache';
	const cache_to_phpTemp				= 'PHPTemp';
	const cache_to_wincache				= 'Wincache';
	const cache_to_sqlite				= 'SQLite';
	const cache_to_sqlite3				= 'SQLite3';


	private static $_cacheStorageMethod = null;

	private static $_cacheStorageClass = null;


	private static $_storageMethods = array(
		self::cache_in_memory,
		self::cache_in_memory_gzip,
		self::cache_in_memory_serialized,
		self::cache_igbinary,
		self::cache_to_phpTemp,
		self::cache_to_discISAM,
		self::cache_to_apc,
		self::cache_to_memcache,
		self::cache_to_wincache,
		self::cache_to_sqlite,
		self::cache_to_sqlite3,
	);


	private static $_storageMethodDefaultParameters = array(
		self::cache_in_memory				=> array(
													),
		self::cache_in_memory_gzip			=> array(
													),
		self::cache_in_memory_serialized	=> array(
													),
		self::cache_igbinary				=> array(
													),
		self::cache_to_phpTemp				=> array( 'memoryCacheSize'	=> '1MB'
													),
		self::cache_to_discISAM				=> array( 'dir'				=> NULL
													),
		self::cache_to_apc					=> array( 'cacheTime'		=> 600
													),
		self::cache_to_memcache				=> array( 'memcacheServer'	=> 'localhost',
													  'memcachePort'	=> 11211,
													  'cacheTime'		=> 600
													),
		self::cache_to_wincache				=> array( 'cacheTime'		=> 600
													),
		self::cache_to_sqlite				=> array(
													),
		self::cache_to_sqlite3				=> array(
													),
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


	public static function getAllCacheStorageMethods() {
		return self::$_storageMethods;
	}	//	function getCacheStorageMethods()


	public static function getCacheStorageMethods() {
		$activeMethods = array();
		foreach(self::$_storageMethods as $storageMethod) {
			$cacheStorageClass = 'PHPExcel_CachedObjectStorage_'.$storageMethod;
			if (call_user_func(array($cacheStorageClass,'cacheMethodIsAvailable'))) {
				$activeMethods[] = $storageMethod;
			}
		}
		return $activeMethods;
	}	//	function getCacheStorageMethods()


	public static function initialize($method = self::cache_in_memory, $arguments = array()) {
		if (!in_array($method,self::$_storageMethods)) {
			return false;
		}

		$cacheStorageClass = 'PHPExcel_CachedObjectStorage_'.$method;
		if (!call_user_func(array($cacheStorageClass,'cacheMethodIsAvailable'))) {
			return false;
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