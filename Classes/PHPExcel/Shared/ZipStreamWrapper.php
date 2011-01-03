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
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Shared_ZipStreamWrapper
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_ZipStreamWrapper {
	/**
	 * Internal ZipAcrhive
	 *
	 * @var ZipAcrhive
	 */
    private $_archive;

    /**
     * Filename in ZipAcrhive
     *
     * @var string
     */
    private $_fileNameInArchive = '';

    /**
     * Position in file
     *
     * @var int
     */
    private $_position = 0;

    /**
     * Data
     *
     * @var mixed
     */
    private $_data = '';

    /**
     * Register wrapper
     */
    public static function register() {
		@stream_wrapper_unregister("zip");
		@stream_wrapper_register("zip", __CLASS__);
    }

    /**
     * Open stream
     */
    public function stream_open($path, $mode, $options, &$opened_path) {
        // Check for mode
        if ($mode{0} != 'r') {
            throw new Exception('Mode ' . $mode . ' is not supported. Only read mode is supported.');
        }

		$pos = strrpos($path, '#');
		$url['host'] = substr($path, 6, $pos - 6); // 6: strlen('zip://')
		$url['fragment'] = substr($path, $pos + 1);

        // Open archive
        $this->_archive = new ZipArchive();
        $this->_archive->open($url['host']);

        $this->_fileNameInArchive = $url['fragment'];
        $this->_position = 0;
        $this->_data = $this->_archive->getFromName( $this->_fileNameInArchive );

        return true;
    }

    /**
     * Stat stream
     */
    public function stream_stat() {
        return $this->_archive->statName( $this->_fileNameInArchive );
    }

    /**
     * Read stream
     */
    function stream_read($count) {
        $ret = substr($this->_data, $this->_position, $count);
        $this->_position += strlen($ret);
        return $ret;
    }

    /**
     * Tell stream
     */
    public function stream_tell() {
        return $this->_position;
    }

    /**
     * EOF stream
     */
    public function stream_eof() {
        return $this->_position >= strlen($this->_data);
    }

    /**
     * Seek stream
     */
    public function stream_seek($offset, $whence) {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($this->_data) && $offset >= 0) {
                     $this->_position = $offset;
                     return true;
                } else {
                     return false;
                }
                break;

            case SEEK_CUR:
                if ($offset >= 0) {
                     $this->_position += $offset;
                     return true;
                } else {
                     return false;
                }
                break;

            case SEEK_END:
                if (strlen($this->_data) + $offset >= 0) {
                     $this->_position = strlen($this->_data) + $offset;
                     return true;
                } else {
                     return false;
                }
                break;

            default:
                return false;
        }
    }
}
