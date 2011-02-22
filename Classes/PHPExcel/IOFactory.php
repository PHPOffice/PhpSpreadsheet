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


/**	PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 *	@ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}

/**
 * PHPExcel_IOFactory
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_IOFactory
{
	/**
	 *	Search locations
	 *
	 *	@var	array
	 *	@access	private
	 *	@static
	 */
	private static $_searchLocations = array(
		array( 'type' => 'IWriter', 'path' => 'PHPExcel/Writer/{0}.php', 'class' => 'PHPExcel_Writer_{0}' ),
		array( 'type' => 'IReader', 'path' => 'PHPExcel/Reader/{0}.php', 'class' => 'PHPExcel_Reader_{0}' )
	);

	/**
	 *	Autoresolve classes
	 *
	 *	@var	array
	 *	@access	private
	 *	@static
	 */
	private static $_autoResolveClasses = array(
		'Excel2007',
		'Excel5',
		'Excel2003XML',
		'OOCalc',
		'SYLK',
		'Gnumeric',
		'CSV',
	);

    /**
     *	Private constructor for PHPExcel_IOFactory
     */
    private function __construct() { }

    /**
     *	Get search locations
     *
	 *	@static
	 *	@access	public
     *	@return	array
     */
	public static function getSearchLocations() {
		return self::$_searchLocations;
	}	//	function getSearchLocations()

	/**
	 *	Set search locations
	 *
	 *	@static
	 *	@access	public
	 *	@param	array $value
	 *	@throws	Exception
	 */
	public static function setSearchLocations($value) {
		if (is_array($value)) {
			self::$_searchLocations = $value;
		} else {
			throw new Exception('Invalid parameter passed.');
		}
	}	//	function setSearchLocations()

	/**
	 *	Add search location
	 *
	 *	@static
	 *	@access	public
	 *	@param	string $type		Example: IWriter
	 *	@param	string $location	Example: PHPExcel/Writer/{0}.php
	 *	@param	string $classname 	Example: PHPExcel_Writer_{0}
	 */
	public static function addSearchLocation($type = '', $location = '', $classname = '') {
		self::$_searchLocations[] = array( 'type' => $type, 'path' => $location, 'class' => $classname );
	}	//	function addSearchLocation()

	/**
	 *	Create PHPExcel_Writer_IWriter
	 *
	 *	@static
	 *	@access	public
	 *	@param	PHPExcel $phpExcel
	 *	@param	string  $writerType	Example: Excel2007
	 *	@return	PHPExcel_Writer_IWriter
	 *	@throws	Exception
	 */
	public static function createWriter(PHPExcel $phpExcel, $writerType = '') {
		// Search type
		$searchType = 'IWriter';

		// Include class
		foreach (self::$_searchLocations as $searchLocation) {
			if ($searchLocation['type'] == $searchType) {
				$className = str_replace('{0}', $writerType, $searchLocation['class']);
				$classFile = str_replace('{0}', $writerType, $searchLocation['path']);

				$instance = new $className($phpExcel);
				if (!is_null($instance)) {
					return $instance;
				}
			}
		}

		// Nothing found...
		throw new Exception("No $searchType found for type $writerType");
	}	//	function createWriter()

	/**
	 *	Create PHPExcel_Reader_IReader
	 *
	 *	@static
	 *	@access	public
	 *	@param	string $readerType	Example: Excel2007
	 *	@return	PHPExcel_Reader_IReader
	 *	@throws	Exception
	 */
	public static function createReader($readerType = '') {
		// Search type
		$searchType = 'IReader';

		// Include class
		foreach (self::$_searchLocations as $searchLocation) {
			if ($searchLocation['type'] == $searchType) {
				$className = str_replace('{0}', $readerType, $searchLocation['class']);
				$classFile = str_replace('{0}', $readerType, $searchLocation['path']);

				$instance = new $className();
				if (!is_null($instance)) {
					return $instance;
				}
			}
		}

		// Nothing found...
		throw new Exception("No $searchType found for type $readerType");
	}	//	function createReader()

	/**
	 *	Loads PHPExcel from file using automatic PHPExcel_Reader_IReader resolution
	 *
	 *	@static
	 *	@access public
	 *	@param 	string 		$pFileName
	 *	@return	PHPExcel
	 *	@throws	Exception
	 */
	public static function load($pFilename) {
		$reader = self::createReaderForFile($pFilename);
		return $reader->load($pFilename);
	}	//	function load()

	/**
	 *	Identify file type using automatic PHPExcel_Reader_IReader resolution
	 *
	 *	@static
	 *	@access public
	 *	@param 	string 		$pFileName
	 *	@return	string
	 *	@throws	Exception
	 */
	public static function identify($pFilename) {
		$reader = self::createReaderForFile($pFilename);
		$className = get_class($reader);
		$classType = explode('_',$className);
		unset($reader);
		return array_pop($classType);
	}	//	function identify()

	/**
	 *	Create PHPExcel_Reader_IReader for file using automatic PHPExcel_Reader_IReader resolution
	 *
	 *	@static
	 *	@access	public
	 *	@param 	string 		$pFileName
	 *	@return	PHPExcel_Reader_IReader
	 *	@throws	Exception
	 */
	public static function createReaderForFile($pFilename) {

		// First, lucky guess by inspecting file extension
		$pathinfo = pathinfo($pFilename);

		if (isset($pathinfo['extension'])) {
			switch (strtolower($pathinfo['extension'])) {
				case 'xlsx':
					$reader = self::createReader('Excel2007');
					break;
				case 'xls':
					$reader = self::createReader('Excel5');
					break;
				case 'ods':
					$reader = self::createReader('OOCalc');
					break;
				case 'slk':
					$reader = self::createReader('SYLK');
					break;
				case 'xml':
					$reader = self::createReader('Excel2003XML');
					break;
				case 'gnumeric':
					$reader = self::createReader('Gnumeric');
					break;
				case 'csv':
					// Do nothing
					// We must not try to use CSV reader since it loads
					// all files including Excel files etc.
					break;
				default:
					break;
			}

			// Let's see if we are lucky
			if (isset($reader) && $reader->canRead($pFilename)) {
				return $reader;
			}

		}

		// If we reach here then "lucky guess" didn't give any result

		// Try loading using self::$_autoResolveClasses
		foreach (self::$_autoResolveClasses as $autoResolveClass) {
			$reader = self::createReader($autoResolveClass);
			if ($reader->canRead($pFilename)) {
				return $reader;
			}
		}

	}	//	function createReaderForFile()
}
