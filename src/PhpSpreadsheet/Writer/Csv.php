<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

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
class Csv extends BaseWriter implements IWriter
{
    /**
     * PhpSpreadsheet object.
     *
     * @var PhpSpreadsheet
     */
    private $spreadsheet;

    /**
     * Delimiter.
     *
     * @var string
     */
    private $delimiter = ',';

    /**
     * Enclosure.
     *
     * @var string
     */
    private $enclosure = '"';

    /**
     * Line ending.
     *
     * @var string
     */
    private $lineEnding = PHP_EOL;

    /**
     * Sheet index to write.
     *
     * @var int
     */
    private $sheetIndex = 0;

    /**
     * Whether to write a BOM (for UTF8).
     *
     * @var bool
     */
    private $useBOM = false;

    /**
     * Whether to write a Separator line as the first line of the file
     *     sep=x.
     *
     * @var bool
     */
    private $includeSeparatorLine = false;

    /**
     * Whether to write a fully Excel compatible CSV file.
     *
     * @var bool
     */
    private $excelCompatibility = false;

    /**
     * Create a new CSV.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet Spreadsheet object
     */
    public function __construct(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    /**
     * Save PhpSpreadsheet to file.
     *
     * @param string $pFilename
     *
     * @throws Exception
     */
    public function save($pFilename = null)
    {
        // Fetch sheet
        $sheet = $this->spreadsheet->getSheet($this->sheetIndex);

        $saveDebugLog = \PhpOffice\PhpSpreadsheet\Calculation::getInstance($this->spreadsheet)->getDebugLog()->getWriteDebugLog();
        \PhpOffice\PhpSpreadsheet\Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog(false);
        $saveArrayReturnType = \PhpOffice\PhpSpreadsheet\Calculation::getArrayReturnType();
        \PhpOffice\PhpSpreadsheet\Calculation::setArrayReturnType(\PhpOffice\PhpSpreadsheet\Calculation::RETURN_ARRAY_AS_VALUE);

        // Open file
        $fileHandle = fopen($pFilename, 'wb+');
        if ($fileHandle === false) {
            throw new Exception("Could not open file $pFilename for writing.");
        }

        if ($this->excelCompatibility) {
            $this->setUseBOM(true); //  Enforce UTF-8 BOM Header
            $this->setIncludeSeparatorLine(true); //  Set separator line
            $this->setEnclosure('"'); //  Set enclosure to "
            $this->setDelimiter(';'); //  Set delimiter to a semi-colon
            $this->setLineEnding("\r\n");
        }
        if ($this->useBOM) {
            // Write the UTF-8 BOM code if required
            fwrite($fileHandle, "\xEF\xBB\xBF");
        }
        if ($this->includeSeparatorLine) {
            // Write the separator line if required
            fwrite($fileHandle, 'sep=' . $this->getDelimiter() . $this->lineEnding);
        }

        //    Identify the range that we need to extract from the worksheet
        $maxCol = $sheet->getHighestDataColumn();
        $maxRow = $sheet->getHighestDataRow();

        // Write rows to file
        for ($row = 1; $row <= $maxRow; ++$row) {
            // Convert the row to an array...
            $cellsArray = $sheet->rangeToArray('A' . $row . ':' . $maxCol . $row, '', $this->preCalculateFormulas);
            // ... and write to the file
            $this->writeLine($fileHandle, $cellsArray[0]);
        }

        // Close file
        fclose($fileHandle);

        \PhpOffice\PhpSpreadsheet\Calculation::setArrayReturnType($saveArrayReturnType);
        \PhpOffice\PhpSpreadsheet\Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);
    }

    /**
     * Get delimiter.
     *
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Set delimiter.
     *
     * @param string $pValue Delimiter, defaults to ,
     *
     * @return CSV
     */
    public function setDelimiter($pValue = ',')
    {
        $this->delimiter = $pValue;

        return $this;
    }

    /**
     * Get enclosure.
     *
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * Set enclosure.
     *
     * @param string $pValue Enclosure, defaults to "
     *
     * @return CSV
     */
    public function setEnclosure($pValue = '"')
    {
        if ($pValue == '') {
            $pValue = null;
        }
        $this->enclosure = $pValue;

        return $this;
    }

    /**
     * Get line ending.
     *
     * @return string
     */
    public function getLineEnding()
    {
        return $this->lineEnding;
    }

    /**
     * Set line ending.
     *
     * @param string $pValue Line ending, defaults to OS line ending (PHP_EOL)
     *
     * @return CSV
     */
    public function setLineEnding($pValue = PHP_EOL)
    {
        $this->lineEnding = $pValue;

        return $this;
    }

    /**
     * Get whether BOM should be used.
     *
     * @return bool
     */
    public function getUseBOM()
    {
        return $this->useBOM;
    }

    /**
     * Set whether BOM should be used.
     *
     * @param bool $pValue Use UTF-8 byte-order mark? Defaults to false
     *
     * @return CSV
     */
    public function setUseBOM($pValue = false)
    {
        $this->useBOM = $pValue;

        return $this;
    }

    /**
     * Get whether a separator line should be included.
     *
     * @return bool
     */
    public function getIncludeSeparatorLine()
    {
        return $this->includeSeparatorLine;
    }

    /**
     * Set whether a separator line should be included as the first line of the file.
     *
     * @param bool $pValue Use separator line? Defaults to false
     *
     * @return CSV
     */
    public function setIncludeSeparatorLine($pValue = false)
    {
        $this->includeSeparatorLine = $pValue;

        return $this;
    }

    /**
     * Get whether the file should be saved with full Excel Compatibility.
     *
     * @return bool
     */
    public function getExcelCompatibility()
    {
        return $this->excelCompatibility;
    }

    /**
     * Set whether the file should be saved with full Excel Compatibility.
     *
     * @param bool $pValue Set the file to be written as a fully Excel compatible csv file
     *                                Note that this overrides other settings such as useBOM, enclosure and delimiter
     *
     * @return CSV
     */
    public function setExcelCompatibility($pValue = false)
    {
        $this->excelCompatibility = $pValue;

        return $this;
    }

    /**
     * Get sheet index.
     *
     * @return int
     */
    public function getSheetIndex()
    {
        return $this->sheetIndex;
    }

    /**
     * Set sheet index.
     *
     * @param int $pValue Sheet index
     *
     * @return CSV
     */
    public function setSheetIndex($pValue = 0)
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    /**
     * Write line to CSV file.
     *
     * @param resource $pFileHandle PHP filehandle
     * @param array $pValues Array containing values in a row
     *
     * @throws Exception
     */
    private function writeLine($pFileHandle = null, $pValues = null)
    {
        if (is_array($pValues)) {
            // No leading delimiter
            $writeDelimiter = false;

            // Build the line
            $line = '';

            foreach ($pValues as $element) {
                // Escape enclosures
                $element = str_replace($this->enclosure, $this->enclosure . $this->enclosure, $element);

                // Add delimiter
                if ($writeDelimiter) {
                    $line .= $this->delimiter;
                } else {
                    $writeDelimiter = true;
                }

                // Add enclosed string
                $line .= $this->enclosure . $element . $this->enclosure;
            }

            // Add line ending
            $line .= $this->lineEnding;

            // Write to file
            fwrite($pFileHandle, $line);
        } else {
            throw new Exception('Invalid data row passed to CSV writer.');
        }
    }
}
