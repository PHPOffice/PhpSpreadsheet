<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2010 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
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
	PHPExcel_Autoloader::Register();
	PHPExcel_Shared_ZipStreamWrapper::register();
	// check mbstring.func_overload
	if (ini_get('mbstring.func_overload') & 2) {
		throw new Exception('Multibyte function overloading in PHP must be disabled for string functions (2).');
	}
}


class PHPExcel_Settings
{
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