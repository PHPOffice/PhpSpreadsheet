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
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

PHPExcel_Autoloader::Register();
PHPExcel_Shared_ZipStreamWrapper::register();
// check mbstring.func_overload
if (ini_get('mbstring.func_overload') & 2) {
	throw new Exception('Multibyte function overloading in PHP must be disabled for string functions (2).');
}
PHPExcel_Shared_String::buildCharacterSets();


class PHPExcel_Autoloader
{
	public static function Register() {
		return spl_autoload_register(array('PHPExcel_Autoloader', 'Load'));
	}	//	function Register()


	public static function Load($pObjectName){
		if ((class_exists($pObjectName)) || (strpos($pObjectName, 'PHPExcel') === False)) {
			return false;
		}

		$pObjectFilePath =	PHPEXCEL_ROOT.
							str_replace('_',DIRECTORY_SEPARATOR,$pObjectName).
							'.php';

		if ((file_exists($pObjectFilePath) === false) || (is_readable($pObjectFilePath) === false)) {
			return false;
		}

		require($pObjectFilePath);
	}	//	function Load()

}