<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Csv extends BaseWriter
{
    /**
     * PhpSpreadsheet object.
     *
     * @var Spreadsheet
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
     * Output encoding.
     *
     * @var string
     */
    private $outputEncoding = '';

    /**
     * Create a new CSV.
     *
     * @param Spreadsheet $spreadsheet Spreadsheet object
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    /**
     * Save PhpSpreadsheet to file.
     *
     * @param resource|string $pFilename
     */
    public function save($pFilename): void
    {
        // Fetch sheet
        $sheet = $this->spreadsheet->getSheet($this->sheetIndex);

        $saveDebugLog = Calculation::getInstance($this->spreadsheet)->getDebugLog()->getWriteDebugLog();
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog(false);
        $saveArrayReturnType = Calculation::getArrayReturnType();
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);

        // Open file
        $this->openFileHandle($pFilename);

        if ($this->excelCompatibility) {
            $this->setUseBOM(true); //  Enforce UTF-8 BOM Header
            $this->setIncludeSeparatorLine(true); //  Set separator line
            $this->setEnclosure('"'); //  Set enclosure to "
            $this->setDelimiter(';'); //  Set delimiter to a semi-colon
            $this->setLineEnding("\r\n");
        }

        if ($this->useBOM) {
            // Write the UTF-8 BOM code if required
            fwrite($this->fileHandle, "\xEF\xBB\xBF");
        }

        if ($this->includeSeparatorLine) {
            // Write the separator line if required
            fwrite($this->fileHandle, 'sep=' . $this->getDelimiter() . $this->lineEnding);
        }

        //    Identify the range that we need to extract from the worksheet
        $maxCol = $sheet->getHighestDataColumn();
        $maxRow = $sheet->getHighestDataRow();

        // Write rows to file
        for ($row = 1; $row <= $maxRow; ++$row) {
            // Convert the row to an array...
            $cellsArray = $sheet->rangeToArray('A' . $row . ':' . $maxCol . $row, '', $this->preCalculateFormulas);
            // ... and write to the file
            $this->writeLine($this->fileHandle, $cellsArray[0]);
        }

        $this->maybeCloseFileHandle();
        Calculation::setArrayReturnType($saveArrayReturnType);
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);
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
     * @param string $pValue Delimiter, defaults to ','
     *
     * @return $this
     */
    public function setDelimiter($pValue)
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
     * @return $this
     */
    public function setEnclosure($pValue = '"')
    {
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
     * @return $this
     */
    public function setLineEnding($pValue)
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
     * @return $this
     */
    public function setUseBOM($pValue)
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
     * @return $this
     */
    public function setIncludeSeparatorLine($pValue)
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
     * @return $this
     */
    public function setExcelCompatibility($pValue)
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
     * @return $this
     */
    public function setSheetIndex($pValue)
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    /**
     * Get output encoding.
     *
     * @return string
     */
    public function getOutputEncoding()
    {
        return $this->outputEncoding;
    }

    /**
     * Set output encoding.
     *
     * @param string $pValue Output encoding
     *
     * @return $this
     */
    public function setOutputEncoding($pValue)
    {
        $this->outputEncoding = $pValue;

        return $this;
    }

    private $enclosureRequired = true;

    public function setEnclosureRequired(bool $value): self
    {
        $this->enclosureRequired = $value;

        return $this;
    }

    public function getEnclosureRequired(): bool
    {
        return $this->enclosureRequired;
    }

    /**
     * Write line to CSV file.
     *
     * @param resource $pFileHandle PHP filehandle
     * @param array $pValues Array containing values in a row
     */
    private function writeLine($pFileHandle, array $pValues): void
    {
        // No leading delimiter
        $delimiter = '';

        // Build the line
        $line = '';

        foreach ($pValues as $element) {
            // Add delimiter
            $line .= $delimiter;
            $delimiter = $this->delimiter;
            // Escape enclosures
            $enclosure = $this->enclosure;
            if ($enclosure) {
                // If enclosure is not required, use enclosure only if
                // element contains newline, delimiter, or enclosure.
                if (!$this->enclosureRequired && strpbrk($element, "$delimiter$enclosure\n") === false) {
                    $enclosure = '';
                } else {
                    $element = str_replace($enclosure, $enclosure . $enclosure, $element);
                }
            }
            // Add enclosed string
            $line .= $enclosure . $element . $enclosure;
        }

        // Add line ending
        $line .= $this->lineEnding;

        // Write to file
        if ($this->outputEncoding != '') {
            $line = mb_convert_encoding($line, $this->outputEncoding);
        }
        fwrite($pFileHandle, $line);
    }
}
