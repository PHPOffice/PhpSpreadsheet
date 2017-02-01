<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

if (!defined('PCLZIP_TEMPORARY_DIR')) {
    define('PCLZIP_TEMPORARY_DIR', File::sysGetTempDir() . DIRECTORY_SEPARATOR);
}

use PhpOffice\PhpSpreadsheet\Shared\PCLZip\PclZip;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class ZipArchive
{
    /**    constants */
    const OVERWRITE = 'OVERWRITE';
    const CREATE = 'CREATE';

    /**
     * Temporary storage directory.
     *
     * @var string
     */
    private $tempDir;

    /**
     * Zip Archive Stream Handle.
     *
     * @var string
     */
    private $zip;

    /**
     * Open a new zip archive.
     *
     * @param string $fileName Filename for the zip archive
     *
     * @return bool
     */
    public function open($fileName)
    {
        $this->tempDir = File::sysGetTempDir();
        $this->zip = new PclZip($fileName);

        return true;
    }

    /**
     * Close this zip archive.
     */
    public function close()
    {
    }

    /**
     * Add a new file to the zip archive from a string of raw data.
     *
     * @param string $localname Directory/Name of the file to add to the zip archive
     * @param string $contents String of data to add to the zip archive
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function addFromString($localname, $contents)
    {
        $filenameParts = pathinfo($localname);

        $handle = fopen($this->tempDir . '/' . $filenameParts['basename'], 'wb');
        fwrite($handle, $contents);
        fclose($handle);

        $res = $this->zip->add($this->tempDir . '/' . $filenameParts['basename'], PCLZIP_OPT_REMOVE_PATH, $this->tempDir, PCLZIP_OPT_ADD_PATH, $filenameParts['dirname']);
        if ($res == 0) {
            throw new \PhpOffice\PhpSpreadsheet\Writer\Exception('Error zipping files : ' . $this->zip->errorInfo(true));
        }

        unlink($this->tempDir . '/' . $filenameParts['basename']);
    }

    /**
     * Find if given fileName exist in archive (Emulate ZipArchive locateName()).
     *
     * @param string $fileName Filename for the file in zip archive
     *
     * @return bool
     */
    public function locateName($fileName)
    {
        $fileName = strtolower($fileName);

        $list = $this->zip->listContent();
        $listCount = count($list);
        $index = -1;
        for ($i = 0; $i < $listCount; ++$i) {
            if (strtolower($list[$i]['filename']) == strtolower($fileName) ||
                strtolower($list[$i]['stored_filename']) == strtolower($fileName)) {
                $index = $i;
                break;
            }
        }

        return ($index > -1) ? $index : false;
    }

    /**
     * Extract file from archive by given fileName (Emulate ZipArchive getFromName()).
     *
     * @param string $fileName Filename for the file in zip archive
     *
     * @return string $contents File string contents
     */
    public function getFromName($fileName)
    {
        $index = $this->locateName($fileName);

        if ($index !== false) {
            $extracted = $this->getFromIndex($index);
        } else {
            $fileName = substr($fileName, 1);
            $index = $this->locateName($fileName);
            if ($index === false) {
                return false;
            }
            $extracted = $this->zip->getFromIndex($index);
        }

        $contents = $extracted;
        if ((is_array($extracted)) && ($extracted != 0)) {
            $contents = $extracted[0]['content'];
        }

        return $contents;
    }

    /**
     * @param int $index
     */
    public function getFromIndex($index)
    {
        $extracted = $this->zip->extractByIndex($index, PCLZIP_OPT_EXTRACT_AS_STRING);
        $contents = '';
        if ((is_array($extracted)) && ($extracted != 0)) {
            $contents = $extracted[0]['content'];
        }
    }
}
