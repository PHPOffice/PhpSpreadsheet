<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2012 PHPExcel
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
 * @package    PHPExcel_Shared_ZipArchive
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/PCLZip/pclzip.lib.php';


/**
 * PHPExcel_Shared_ZipArchive
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_ZipArchive
 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_ZipArchive
{

	/**	constants */
	const OVERWRITE		= 'OVERWRITE';
	const CREATE		= 'CREATE';


	/**
	 * Temporary storage directory
	 *
	 * @var string
	 */
	private $_tempDir;

	/**
	 * Zip Archive Stream Handle
	 *
	 * @var string
	 */
	private $_zip;


    /**
	 * Open a new zip archive
	 *
	 * @param	string	$fileName	Filename for the zip archive
	 * @return	boolean
     */
	public function open($fileName)
	{
		$this->_tempDir = PHPExcel_Shared_File::sys_get_temp_dir();

		$this->_zip = new PclZip($fileName);

		return true;
	}


    /**
	 * Close this zip archive
	 *
     */
	public function close()
	{
	}


    /**
	 * Add a new file to the zip archive from a string of raw data.
	 *
	 * @param	string	$localname		Directory/Name of the file to add to the zip archive
	 * @param	string	$contents		String of data to add to the zip archive
     */
	public function addFromString($localname, $contents)
	{
		$filenameParts = pathinfo($localname);

		$handle = fopen($this->_tempDir.'/'.$filenameParts["basename"], "wb");
		fwrite($handle, $contents);
		fclose($handle);

		$res = $this->_zip->add($this->_tempDir.'/'.$filenameParts["basename"],
								PCLZIP_OPT_REMOVE_PATH, $this->_tempDir,
								PCLZIP_OPT_ADD_PATH, $filenameParts["dirname"]
							   );
		if ($res == 0) {
			throw new Exception("Error zipping files : " . $this->_zip->errorInfo(true));
		}

		unlink($this->_tempDir.'/'.$filenameParts["basename"]);
	}

}
