<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Csv extends BaseWriter
{
    /**
     * PhpSpreadsheet object.
     */
    private Spreadsheet $spreadsheet;

    /**
     * Delimiter.
     */
    private string $delimiter = ',';

    /**
     * Enclosure.
     */
    private string $enclosure = '"';

    /**
     * Line ending.
     */
    private string $lineEnding = PHP_EOL;

    /**
     * Sheet index to write.
     */
    private int $sheetIndex = 0;

    /**
     * Whether to write a UTF8 BOM.
     */
    private bool $useBOM = false;

    /**
     * Whether to write a Separator line as the first line of the file
     *     sep=x.
     */
    private bool $includeSeparatorLine = false;

    /**
     * Whether to write a fully Excel compatible CSV file.
     */
    private bool $excelCompatibility = false;

    /**
     * Output encoding.
     */
    private string $outputEncoding = '';

    /**
     * Create a new CSV.
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    /**
     * Save PhpSpreadsheet to file.
     *
     * @param resource|string $filename
     */
    public function save($filename, int $flags = 0): void
    {
        $this->processFlags($flags);

        // Fetch sheet
        $sheet = $this->spreadsheet->getSheet($this->sheetIndex);

        $saveDebugLog = Calculation::getInstance($this->spreadsheet)->getDebugLog()->getWriteDebugLog();
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog(false);
        $saveArrayReturnType = Calculation::getArrayReturnType();
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);

        // Open file
        $this->openFileHandle($filename);

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
        foreach ($sheet->rangeToArrayYieldRows("A1:$maxCol$maxRow", '', $this->preCalculateFormulas) as $cellsArray) {
            $this->writeLine($this->fileHandle, $cellsArray);
        }

        $this->maybeCloseFileHandle();
        Calculation::setArrayReturnType($saveArrayReturnType);
        Calculation::getInstance($this->spreadsheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    public function setEnclosure(string $enclosure = '"'): self
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function getLineEnding(): string
    {
        return $this->lineEnding;
    }

    public function setLineEnding(string $lineEnding): self
    {
        $this->lineEnding = $lineEnding;

        return $this;
    }

    /**
     * Get whether BOM should be used.
     */
    public function getUseBOM(): bool
    {
        return $this->useBOM;
    }

    /**
     * Set whether BOM should be used, typically when non-ASCII characters are used.
     */
    public function setUseBOM(bool $useBOM): self
    {
        $this->useBOM = $useBOM;

        return $this;
    }

    /**
     * Get whether a separator line should be included.
     */
    public function getIncludeSeparatorLine(): bool
    {
        return $this->includeSeparatorLine;
    }

    /**
     * Set whether a separator line should be included as the first line of the file.
     */
    public function setIncludeSeparatorLine(bool $includeSeparatorLine): self
    {
        $this->includeSeparatorLine = $includeSeparatorLine;

        return $this;
    }

    /**
     * Get whether the file should be saved with full Excel Compatibility.
     */
    public function getExcelCompatibility(): bool
    {
        return $this->excelCompatibility;
    }

    /**
     * Set whether the file should be saved with full Excel Compatibility.
     *
     * @param bool $excelCompatibility Set the file to be written as a fully Excel compatible csv file
     *                                Note that this overrides other settings such as useBOM, enclosure and delimiter
     */
    public function setExcelCompatibility(bool $excelCompatibility): self
    {
        $this->excelCompatibility = $excelCompatibility;

        return $this;
    }

    public function getSheetIndex(): int
    {
        return $this->sheetIndex;
    }

    public function setSheetIndex(int $sheetIndex): self
    {
        $this->sheetIndex = $sheetIndex;

        return $this;
    }

    public function getOutputEncoding(): string
    {
        return $this->outputEncoding;
    }

    public function setOutputEncoding(string $outputEnconding): self
    {
        $this->outputEncoding = $outputEnconding;

        return $this;
    }

    private bool $enclosureRequired = true;

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
     * Convert boolean to TRUE/FALSE; otherwise return element cast to string.
     */
    private static function elementToString(mixed $element): string
    {
        if (is_bool($element)) {
            return $element ? 'TRUE' : 'FALSE';
        }

        return (string) $element;
    }

    /**
     * Write line to CSV file.
     *
     * @param resource $fileHandle PHP filehandle
     * @param array $values Array containing values in a row
     */
    private function writeLine($fileHandle, array $values): void
    {
        // No leading delimiter
        $delimiter = '';

        // Build the line
        $line = '';

        foreach ($values as $element) {
            $element = self::elementToString($element);
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
        fwrite($fileHandle, $line);
    }
}
