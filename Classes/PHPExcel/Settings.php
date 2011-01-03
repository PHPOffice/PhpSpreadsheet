<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Settings
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


class PHPExcel_Settings
{
	/**	constants */
	const PCLZIP		= 'PHPExcel_Shared_ZipArchive';
	const ZIPARCHIVE	= 'ZipArchive';


	private static $_zipClass	= self::ZIPARCHIVE;


	/**
	 * Set the Zip Class to use (PCLZip or ZipArchive)
	 *
	 * @param	 string	$zipClass			PHPExcel_Settings::PCLZip or PHPExcel_Settings::ZipArchive
	 * @return	 boolean					Success or failure
	 */
	public static function setZipClass($zipClass) {
		if (($zipClass == self::PCLZIP) ||
			($zipClass == self::ZIPARCHIVE)) {
			self::$_zipClass = $zipClass;
			return True;
		}
		return False;
	}	//	function setZipClass()


	/**
	 * Return the Zip Class to use (PCLZip or ZipArchive)
	 *
	 * @return	 string						Zip Class to use	- PHPExcel_Settings::PCLZip or PHPExcel_Settings::ZipArchive
	 */
	public static function getZipClass() {
		return self::$_zipClass;
	}	//	function getZipClass()


	public static function getCacheStorageMethod() {
		return PHPExcel_CachedObjectStorageFactory::$_cacheStorageMethod;
	}	//	function getCacheStorageMethod()


	public static function getCacheStorageClass() {
		return PHPExcel_CachedObjectStorageFactory::$_cacheStorageClass;
	}	//	function getCacheStorageClass()


	public static function setCacheStorageMethod($method = PHPExcel_CachedObjectStorageFactory::cache_in_memory, $arguments = array()) {
		return PHPExcel_CachedObjectStorageFactory::initialize($method,$arguments);
	}	//	function setCacheStorageMethod()


	public static function setLocale($locale){
		return PHPExcel_Calculation::getInstance()->setLocale($locale);
	}	//	function setLocale()

}