<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

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
class XMLWriter extends \XMLWriter
{
    public static $debugEnabled = false;

    /** Temporary storage method */
    const STORAGE_MEMORY = 1;
    const STORAGE_DISK = 2;

    /**
     * Temporary filename.
     *
     * @var string
     */
    private $tempFileName = '';

    /**
     * Create a new XMLWriter instance.
     *
     * @param int $pTemporaryStorage Temporary storage location
     * @param string $pTemporaryStorageFolder Temporary storage folder
     */
    public function __construct($pTemporaryStorage = self::STORAGE_MEMORY, $pTemporaryStorageFolder = null)
    {
        // Open temporary storage
        if ($pTemporaryStorage == self::STORAGE_MEMORY) {
            $this->openMemory();
        } else {
            // Create temporary filename
            if ($pTemporaryStorageFolder === null) {
                $pTemporaryStorageFolder = File::sysGetTempDir();
            }
            $this->tempFileName = @tempnam($pTemporaryStorageFolder, 'xml');

            // Open storage
            if ($this->openUri($this->tempFileName) === false) {
                // Fallback to memory...
                $this->openMemory();
            }
        }

        // Set default values
        if (self::$debugEnabled) {
            $this->setIndent(true);
        }
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // Unlink temporary files
        if ($this->tempFileName != '') {
            @unlink($this->tempFileName);
        }
    }

    /**
     * Get written data.
     *
     * @return $data
     */
    public function getData()
    {
        if ($this->tempFileName == '') {
            return $this->outputMemory(true);
        }
        $this->flush();

        return file_get_contents($this->tempFileName);
    }

    /**
     * Wrapper method for writeRaw.
     *
     * @param string|string[] $text
     *
     * @return bool
     */
    public function writeRawData($text)
    {
        if (is_array($text)) {
            $text = implode("\n", $text);
        }

        return $this->writeRaw(htmlspecialchars($text));
    }
}
