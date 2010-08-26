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
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

if (!defined('DATE_W3C')) {
  define('DATE_W3C', 'Y-m-d\TH:i:sP');
}

if (!defined('DEBUGMODE_ENABLED')) {
  define('DEBUGMODE_ENABLED', false);
}


/**
 * PHPExcel_Shared_XMLWriter
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_XMLWriter {
	/** Temporary storage method */
	const STORAGE_MEMORY	= 1;
	const STORAGE_DISK		= 2;

	/**
	 * Internal XMLWriter
	 *
	 * @var XMLWriter
	 */
	private $_xmlWriter;

	/**
	 * Temporary filename
	 *
	 * @var string
	 */
	private $_tempFileName = '';

	/**
	 * Create a new PHPExcel_Shared_XMLWriter instance
	 *
	 * @param int		$pTemporaryStorage			Temporary storage location
	 * @param string	$pTemporaryStorageFolder	Temporary storage folder
	 */
    public function __construct($pTemporaryStorage = self::STORAGE_MEMORY, $pTemporaryStorageFolder = './') {
    	// Create internal XMLWriter
    	$this->_xmlWriter = new XMLWriter();

    	// Open temporary storage
    	if ($pTemporaryStorage == self::STORAGE_MEMORY) {
    		$this->_xmlWriter->openMemory();
    	} else {
    		// Create temporary filename
    		$this->_tempFileName = @tempnam($pTemporaryStorageFolder, 'xml');

    		// Open storage
    		if ($this->_xmlWriter->openUri($this->_tempFileName) === false) {
    			// Fallback to memory...
    			$this->_xmlWriter->openMemory();
    		}
    	}

    	// Set default values
		if (DEBUGMODE_ENABLED) {
	    	$this->_xmlWriter->setIndent(true);
	    }
    }

    /**
     * Destructor
     */
    public function __destruct() {
    	// Desctruct XMLWriter
    	unset($this->_xmlWriter);

    	// Unlink temporary files
    	if ($this->_tempFileName != '') {
    		@unlink($this->_tempFileName);
    	}
    }

    /**
     * Get written data
     *
     * @return $data
     */
    public function getData() {
    	if ($this->_tempFileName == '') {
    		return $this->_xmlWriter->outputMemory(true);
    	} else {
    		$this->_xmlWriter->flush();
    		return file_get_contents($this->_tempFileName);
    	}
    }

    /**
     * Catch function calls (and pass them to internal XMLWriter)
     *
     * @param unknown_type $function
     * @param unknown_type $args
     */
    public function __call($function, $args) {
    	try {
    		@call_user_func_array(array($this->_xmlWriter, $function), $args);
    	} catch (Exception $ex) {
    		// Do nothing!
    	}
    }

    /**
     * Fallback method for writeRaw, introduced in PHP 5.2
     *
     * @param string $text
     * @return string
     */
    public function writeRaw($text)
    {
    	if (isset($this->_xmlWriter) && is_object($this->_xmlWriter) && (method_exists($this->_xmlWriter, 'writeRaw'))) {
            return $this->_xmlWriter->writeRaw(htmlspecialchars($text));
    	}

    	return $this->text($text);
    }
}
