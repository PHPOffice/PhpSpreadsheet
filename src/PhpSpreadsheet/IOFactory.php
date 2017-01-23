<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Shared\File;

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
class IOFactory
{
    /**
     * Search locations.
     *
     * @var array
     * @static
     */
    private static $searchLocations = [
        ['type' => 'IWriter', 'path' => 'PhpSpreadsheet/Writer/{0}.php', 'class' => '\\PhpOffice\\PhpSpreadsheet\\Writer\\{0}'],
        ['type' => 'IReader', 'path' => 'PhpSpreadsheet/Reader/{0}.php', 'class' => '\\PhpOffice\\PhpSpreadsheet\\Reader\\{0}'],
    ];

    /**
     * Autoresolve classes.
     *
     * @var array
     * @static
     */
    private static $autoResolveClasses = [
        'Xlsx',
        'Xls',
        'Xml',
        'Ods',
        'Slk',
        'Gnumeric',
        'Html',
        'Csv',
    ];

    /**
     * Private constructor for IOFactory.
     */
    private function __construct()
    {
    }

    /**
     * Get search locations.
     *
     * @static
     *
     * @return array
     */
    public static function getSearchLocations()
    {
        return self::$searchLocations;
    }

    /**
     * Set search locations.
     *
     * @static
     *
     * @param array $value
     *
     * @throws Reader\Exception
     */
    public static function setSearchLocations($value)
    {
        if (is_array($value)) {
            self::$searchLocations = $value;
        } else {
            throw new Reader\Exception('Invalid parameter passed.');
        }
    }

    /**
     * Add search location.
     *
     * @static
     *
     * @param string $type Example: IWriter
     * @param string $location Example: PhpSpreadsheet/Writer/{0}.php
     * @param string $classname Example: Writer\{0}
     */
    public static function addSearchLocation($type = '', $location = '', $classname = '')
    {
        self::$searchLocations[] = ['type' => $type, 'path' => $location, 'class' => $classname];
    }

    /**
     * Create Writer\IWriter.
     *
     * @static
     *
     * @param Spreadsheet $spreadsheet
     * @param string $writerType Example: Xlsx
     *
     * @throws Writer\Exception
     *
     * @return Writer\IWriter
     */
    public static function createWriter(Spreadsheet $spreadsheet, $writerType)
    {
        // Search type
        $searchType = 'IWriter';

        // Include class
        foreach (self::$searchLocations as $searchLocation) {
            if ($searchLocation['type'] == $searchType) {
                $className = str_replace('{0}', $writerType, $searchLocation['class']);

                $instance = new $className($spreadsheet);
                if ($instance !== null) {
                    return $instance;
                }
            }
        }

        // Nothing found...
        throw new Writer\Exception("No $searchType found for type $writerType");
    }

    /**
     * Create Reader\IReader.
     *
     * @static
     *
     * @param string $readerType Example: Xlsx
     *
     * @throws Reader\Exception
     *
     * @return Reader\IReader
     */
    public static function createReader($readerType = '')
    {
        // Search type
        $searchType = 'IReader';

        // Include class
        foreach (self::$searchLocations as $searchLocation) {
            if ($searchLocation['type'] == $searchType) {
                $className = str_replace('{0}', $readerType, $searchLocation['class']);

                $instance = new $className();
                if ($instance !== null) {
                    return $instance;
                }
            }
        }

        // Nothing found...
        throw new Reader\Exception("No $searchType found for type $readerType");
    }

    /**
     * Loads Spreadsheet from file using automatic Reader\IReader resolution.
     *
     * @static
     *
     * @param string $pFilename The name of the spreadsheet file
     *
     * @throws Reader\Exception
     *
     * @return Spreadsheet
     */
    public static function load($pFilename)
    {
        $reader = self::createReaderForFile($pFilename);

        return $reader->load($pFilename);
    }

    /**
     * Identify file type using automatic Reader\IReader resolution.
     *
     * @static
     *
     * @param string $pFilename The name of the spreadsheet file to identify
     *
     * @throws Reader\Exception
     *
     * @return string
     */
    public static function identify($pFilename)
    {
        $reader = self::createReaderForFile($pFilename);
        $className = get_class($reader);
        $classType = explode('\\', $className);
        unset($reader);

        return array_pop($classType);
    }

    /**
     * Create Reader\IReader for file using automatic Reader\IReader resolution.
     *
     * @static
     *
     * @param string $pFilename The name of the spreadsheet file
     *
     * @throws Reader\Exception
     *
     * @return Reader\IReader
     */
    public static function createReaderForFile($pFilename)
    {
        File::assertFile($pFilename);

        // First, lucky guess by inspecting file extension
        $pathinfo = pathinfo($pFilename);

        $extensionType = null;
        if (isset($pathinfo['extension'])) {
            switch (strtolower($pathinfo['extension'])) {
                case 'xlsx':            //    Excel (OfficeOpenXML) Spreadsheet
                case 'xlsm':            //    Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
                case 'xltx':            //    Excel (OfficeOpenXML) Template
                case 'xltm':            //    Excel (OfficeOpenXML) Macro Template (macros will be discarded)
                    $extensionType = 'Xlsx';
                    break;
                case 'xls':                //    Excel (BIFF) Spreadsheet
                case 'xlt':                //    Excel (BIFF) Template
                    $extensionType = 'Xls';
                    break;
                case 'ods':                //    Open/Libre Offic Calc
                case 'ots':                //    Open/Libre Offic Calc Template
                    $extensionType = 'Ods';
                    break;
                case 'slk':
                    $extensionType = 'Slk';
                    break;
                case 'xml':                //    Excel 2003 SpreadSheetML
                    $extensionType = 'Xml';
                    break;
                case 'gnumeric':
                    $extensionType = 'Gnumeric';
                    break;
                case 'htm':
                case 'html':
                    $extensionType = 'Html';
                    break;
                case 'csv':
                    // Do nothing
                    // We must not try to use CSV reader since it loads
                    // all files including Excel files etc.
                    break;
                default:
                    break;
            }

            if ($extensionType !== null) {
                $reader = self::createReader($extensionType);
                // Let's see if we are lucky
                if (isset($reader) && $reader->canRead($pFilename)) {
                    return $reader;
                }
            }
        }

        // If we reach here then "lucky guess" didn't give any result
        // Try walking through all the options in self::$autoResolveClasses
        foreach (self::$autoResolveClasses as $autoResolveClass) {
            //    Ignore our original guess, we know that won't work
            if ($autoResolveClass !== $extensionType) {
                $reader = self::createReader($autoResolveClass);
                if ($reader->canRead($pFilename)) {
                    return $reader;
                }
            }
        }

        throw new Reader\Exception('Unable to identify a reader for this file');
    }
}
