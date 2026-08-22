<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use Composer\Pcre\Preg;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
     * Whether number of columns should be allowed to vary
     * between rows, or use a fixed range based on the max
     * column overall.
     */
    private bool $variableColumns = false;

    private bool $preferHyperlinkToLabel = false;

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
        $sheet->calculateArrays($this->preCalculateFormulas);

        // Open file
        $this->openFileHandle($filename);

        if ($this->excelCompatibility) {
            $this->setUseBOM(true); //  Enforce UTF-8 BOM Header
            $this->setIncludeSeparatorLine(true); //  Set separator line
            $this->setEnclosure('"'); //  Set enclosure to "
            $this->setDelimiter(';'); //  Set delimiter to a semicolon
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
        $row = 0;
        foreach ($sheet->rangeToArrayYieldRows("A1:$maxCol$maxRow", '', $this->preCalculateFormulas) as $cellsArray) {
            ++$row;
            if ($this->variableColumns) {
                $column = $sheet->getHighestDataColumn($row);
                if ($column === 'A' && !$sheet->cellExists("A$row")) {
                    $cellsArray = [];
                } else {
                    array_splice($cellsArray, Coordinate::columnIndexFromString($column));
                }
            }
            if ($this->preferHyperlinkToLabel) {
                foreach ($cellsArray as $key => $value) {
                    $url = $sheet->getCell([$key + 1, $row])->getHyperlink()->getUrl();
                    if ($url !== '') {
                        $cellsArray[$key] = $url;
                    }
                }
            }
            /** @var string[] $cellsArray */
            $this->writeLine($this->fileHandle, $cellsArray);
        }

        $this->maybeCloseFileHandle();
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

    public function setOutputEncoding(string $outputEncoding): self
    {
        $this->outputEncoding = $outputEncoding;

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
     * Write line to CSV file.
     *
     * @param resource $fileHandle PHP filehandle
     * @param string[] $values Array containing values in a row
     */
    private function writeLine($fileHandle, array $values): void
    {
        // No leading delimiter
        $delimiter = '';

        // Build the line
        $line = '';

        foreach ($values as $element) {
            if (Preg::isMatch('/^([+-])?(\d+)[.](\d+)/', $element, $matches)) {
                // Excel will "convert" file with pop-up
                // if there are more than 15 digits precision.
                $whole = $matches[2];
                if ($whole !== '0') {
                    $wholeLen = strlen($whole);
                    $frac = $matches[3];
                    $maxFracLen = 15 - $wholeLen;
                    if ($maxFracLen >= 0 && strlen($frac) > $maxFracLen) {
                        $result = sprintf("%.{$maxFracLen}F", $element);
                        if (str_contains($result, '.')) {
                            $element = Preg::replace('/[.]?0+$/', '', $result); // strip trailing zeros
                        }
                    }
                }
            }
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
            $line = (string) mb_convert_encoding($line, $this->outputEncoding);
        }
        fwrite($fileHandle, $line);
    }

    /**
     * Get whether number of columns should be allowed to vary
     * between rows, or use a fixed range based on the max
     * column overall.
     */
    public function getVariableColumns(): bool
    {
        return $this->variableColumns;
    }

    /**
     * Set whether number of columns should be allowed to vary
     * between rows, or use a fixed range based on the max
     * column overall.
     */
    public function setVariableColumns(bool $pValue): self
    {
        $this->variableColumns = $pValue;

        return $this;
    }

    /**
     * Get whether hyperlink or label should be output.
     */
    public function getPreferHyperlinkToLabel(): bool
    {
        return $this->preferHyperlinkToLabel;
    }

    /**
     * Set whether hyperlink or label should be output.
     */
    public function setPreferHyperlinkToLabel(bool $preferHyperlinkToLabel): self
    {
        $this->preferHyperlinkToLabel = $preferHyperlinkToLabel;

        return $this;
    }
}
