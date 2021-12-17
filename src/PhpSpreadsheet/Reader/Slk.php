<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Slk extends BaseReader
{
    /**
     * Input encoding.
     *
     * @var string
     */
    private $inputEncoding = 'ANSI';

    /**
     * Sheet index to read.
     *
     * @var int
     */
    private $sheetIndex = 0;

    /**
     * Formats.
     *
     * @var array
     */
    private $formats = [];

    /**
     * Format Count.
     *
     * @var int
     */
    private $format = 0;

    /**
     * Fonts.
     *
     * @var array
     */
    private $fonts = [];

    /**
     * Font Count.
     *
     * @var int
     */
    private $fontcount = 0;

    /**
     * Create a new SYLK Reader instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Validate that the current file is a SYLK file.
     */
    public function canRead(string $filename): bool
    {
        try {
            $this->openFile($filename);
        } catch (ReaderException $e) {
            return false;
        }

        // Read sample data (first 2 KB will do)
        $data = (string) fread($this->fileHandle, 2048);

        // Count delimiters in file
        $delimiterCount = substr_count($data, ';');
        $hasDelimiter = $delimiterCount > 0;

        // Analyze first line looking for ID; signature
        $lines = explode("\n", $data);
        $hasId = substr($lines[0], 0, 4) === 'ID;P';

        fclose($this->fileHandle);

        return $hasDelimiter && $hasId;
    }

    private function canReadOrBust(string $filename): void
    {
        if (!$this->canRead($filename)) {
            throw new ReaderException($filename . ' is an Invalid SYLK file.');
        }
        $this->openFile($filename);
    }

    /**
     * Set input encoding.
     *
     * @deprecated no use is made of this property
     *
     * @param string $inputEncoding Input encoding, eg: 'ANSI'
     *
     * @return $this
     *
     * @codeCoverageIgnore
     */
    public function setInputEncoding($inputEncoding)
    {
        $this->inputEncoding = $inputEncoding;

        return $this;
    }

    /**
     * Get input encoding.
     *
     * @deprecated no use is made of this property
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getInputEncoding()
    {
        return $this->inputEncoding;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @param string $filename
     *
     * @return array
     */
    public function listWorksheetInfo($filename)
    {
        // Open file
        $this->canReadOrBust($filename);
        $fileHandle = $this->fileHandle;
        rewind($fileHandle);

        $worksheetInfo = [];
        $worksheetInfo[0]['worksheetName'] = basename($filename, '.slk');

        // loop through one row (line) at a time in the file
        $rowIndex = 0;
        $columnIndex = 0;
        while (($rowData = fgets($fileHandle)) !== false) {
            $columnIndex = 0;

            // convert SYLK encoded $rowData to UTF-8
            $rowData = StringHelper::SYLKtoUTF8($rowData);

            // explode each row at semicolons while taking into account that literal semicolon (;)
            // is escaped like this (;;)
            $rowData = explode("\t", str_replace('¤', ';', str_replace(';', "\t", str_replace(';;', '¤', rtrim($rowData)))));

            $dataType = array_shift($rowData);
            if ($dataType == 'B') {
                foreach ($rowData as $rowDatum) {
                    switch ($rowDatum[0]) {
                        case 'X':
                            $columnIndex = (int) substr($rowDatum, 1) - 1;

                            break;
                        case 'Y':
                            $rowIndex = substr($rowDatum, 1);

                            break;
                    }
                }

                break;
            }
        }

        $worksheetInfo[0]['lastColumnIndex'] = $columnIndex;
        $worksheetInfo[0]['totalRows'] = $rowIndex;
        $worksheetInfo[0]['lastColumnLetter'] = Coordinate::stringFromColumnIndex($worksheetInfo[0]['lastColumnIndex'] + 1);
        $worksheetInfo[0]['totalColumns'] = $worksheetInfo[0]['lastColumnIndex'] + 1;

        // Close file
        fclose($fileHandle);

        return $worksheetInfo;
    }

    /**
     * Loads PhpSpreadsheet from file.
     *
     * @return Spreadsheet
     */
    public function load(string $filename, int $flags = 0)
    {
        $this->processFlags($flags);

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Load into this instance
        return $this->loadIntoExisting($filename, $spreadsheet);
    }

    private const COLOR_ARRAY = [
        'FF00FFFF', // 0 - cyan
        'FF000000', // 1 - black
        'FFFFFFFF', // 2 - white
        'FFFF0000', // 3 - red
        'FF00FF00', // 4 - green
        'FF0000FF', // 5 - blue
        'FFFFFF00', // 6 - yellow
        'FFFF00FF', // 7 - magenta
    ];

    private const FONT_STYLE_MAPPINGS = [
        'B' => 'bold',
        'I' => 'italic',
        'U' => 'underline',
    ];

    private function processFormula(string $rowDatum, bool &$hasCalculatedValue, string &$cellDataFormula, string $row, string $column): void
    {
        $cellDataFormula = '=' . substr($rowDatum, 1);
        //    Convert R1C1 style references to A1 style references (but only when not quoted)
        $temp = explode('"', $cellDataFormula);
        $key = false;
        foreach ($temp as &$value) {
            //    Only count/replace in alternate array entries
            $key = !$key;
            if ($key) {
                preg_match_all('/(R(\[?-?\d*\]?))(C(\[?-?\d*\]?))/', $value, $cellReferences, PREG_SET_ORDER + PREG_OFFSET_CAPTURE);
                //    Reverse the matches array, otherwise all our offsets will become incorrect if we modify our way
                //        through the formula from left to right. Reversing means that we work right to left.through
                //        the formula
                $cellReferences = array_reverse($cellReferences);
                //    Loop through each R1C1 style reference in turn, converting it to its A1 style equivalent,
                //        then modify the formula to use that new reference
                foreach ($cellReferences as $cellReference) {
                    $rowReference = $cellReference[2][0];
                    //    Empty R reference is the current row
                    if ($rowReference == '') {
                        $rowReference = $row;
                    }
                    //    Bracketed R references are relative to the current row
                    if ($rowReference[0] == '[') {
                        $rowReference = (int) $row + (int) trim($rowReference, '[]');
                    }
                    $columnReference = $cellReference[4][0];
                    //    Empty C reference is the current column
                    if ($columnReference == '') {
                        $columnReference = $column;
                    }
                    //    Bracketed C references are relative to the current column
                    if ($columnReference[0] == '[') {
                        $columnReference = (int) $column + (int) trim($columnReference, '[]');
                    }
                    $A1CellReference = Coordinate::stringFromColumnIndex($columnReference) . $rowReference;

                    $value = substr_replace($value, $A1CellReference, $cellReference[0][1], strlen($cellReference[0][0]));
                }
            }
        }
        unset($value);
        //    Then rebuild the formula string
        $cellDataFormula = implode('"', $temp);
        $hasCalculatedValue = true;
    }

    private function processCRecord(array $rowData, Spreadsheet &$spreadsheet, string &$row, string &$column): void
    {
        //    Read cell value data
        $hasCalculatedValue = false;
        $cellDataFormula = $cellData = '';
        foreach ($rowData as $rowDatum) {
            switch ($rowDatum[0]) {
                case 'C':
                case 'X':
                    $column = substr($rowDatum, 1);

                    break;
                case 'R':
                case 'Y':
                    $row = substr($rowDatum, 1);

                    break;
                case 'K':
                    $cellData = substr($rowDatum, 1);

                    break;
                case 'E':
                    $this->processFormula($rowDatum, $hasCalculatedValue, $cellDataFormula, $row, $column);

                    break;
                case 'A':
                    $comment = substr($rowDatum, 1);
                    $columnLetter = Coordinate::stringFromColumnIndex((int) $column);
                    $spreadsheet->getActiveSheet()
                        ->getComment("$columnLetter$row")
                        ->getText()
                        ->createText($comment);

                    break;
            }
        }
        $columnLetter = Coordinate::stringFromColumnIndex((int) $column);
        $cellData = Calculation::unwrapResult($cellData);

        // Set cell value
        $this->processCFinal($spreadsheet, $hasCalculatedValue, $cellDataFormula, $cellData, "$columnLetter$row");
    }

    private function processCFinal(Spreadsheet &$spreadsheet, bool $hasCalculatedValue, string $cellDataFormula, string $cellData, string $coordinate): void
    {
        // Set cell value
        $spreadsheet->getActiveSheet()->getCell($coordinate)->setValue(($hasCalculatedValue) ? $cellDataFormula : $cellData);
        if ($hasCalculatedValue) {
            $cellData = Calculation::unwrapResult($cellData);
            $spreadsheet->getActiveSheet()->getCell($coordinate)->setCalculatedValue($cellData);
        }
    }

    private function processFRecord(array $rowData, Spreadsheet &$spreadsheet, string &$row, string &$column): void
    {
        //    Read cell formatting
        $formatStyle = $columnWidth = '';
        $startCol = $endCol = '';
        $fontStyle = '';
        $styleData = [];
        foreach ($rowData as $rowDatum) {
            switch ($rowDatum[0]) {
                case 'C':
                case 'X':
                    $column = substr($rowDatum, 1);

                    break;
                case 'R':
                case 'Y':
                    $row = substr($rowDatum, 1);

                    break;
                case 'P':
                    $formatStyle = $rowDatum;

                    break;
                case 'W':
                    [$startCol, $endCol, $columnWidth] = explode(' ', substr($rowDatum, 1));

                    break;
                case 'S':
                    $this->styleSettings($rowDatum, $styleData, $fontStyle);

                    break;
            }
        }
        $this->addFormats($spreadsheet, $formatStyle, $row, $column);
        $this->addFonts($spreadsheet, $fontStyle, $row, $column);
        $this->addStyle($spreadsheet, $styleData, $row, $column);
        $this->addWidth($spreadsheet, $columnWidth, $startCol, $endCol);
    }

    private const STYLE_SETTINGS_FONT = ['D' => 'bold', 'I' => 'italic'];

    private const STYLE_SETTINGS_BORDER = [
        'B' => 'bottom',
        'L' => 'left',
        'R' => 'right',
        'T' => 'top',
    ];

    private function styleSettings(string $rowDatum, array &$styleData, string &$fontStyle): void
    {
        $styleSettings = substr($rowDatum, 1);
        $iMax = strlen($styleSettings);
        for ($i = 0; $i < $iMax; ++$i) {
            $char = $styleSettings[$i];
            if (array_key_exists($char, self::STYLE_SETTINGS_FONT)) {
                $styleData['font'][self::STYLE_SETTINGS_FONT[$char]] = true;
            } elseif (array_key_exists($char, self::STYLE_SETTINGS_BORDER)) {
                $styleData['borders'][self::STYLE_SETTINGS_BORDER[$char]]['borderStyle'] = Border::BORDER_THIN;
            } elseif ($char == 'S') {
                $styleData['fill']['fillType'] = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_GRAY125;
            } elseif ($char == 'M') {
                if (preg_match('/M([1-9]\\d*)/', $styleSettings, $matches)) {
                    $fontStyle = $matches[1];
                }
            }
        }
    }

    private function addFormats(Spreadsheet &$spreadsheet, string $formatStyle, string $row, string $column): void
    {
        if ($formatStyle && $column > '' && $row > '') {
            $columnLetter = Coordinate::stringFromColumnIndex((int) $column);
            if (isset($this->formats[$formatStyle])) {
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->applyFromArray($this->formats[$formatStyle]);
            }
        }
    }

    private function addFonts(Spreadsheet &$spreadsheet, string $fontStyle, string $row, string $column): void
    {
        if ($fontStyle && $column > '' && $row > '') {
            $columnLetter = Coordinate::stringFromColumnIndex((int) $column);
            if (isset($this->fonts[$fontStyle])) {
                $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->applyFromArray($this->fonts[$fontStyle]);
            }
        }
    }

    private function addStyle(Spreadsheet &$spreadsheet, array $styleData, string $row, string $column): void
    {
        if ((!empty($styleData)) && $column > '' && $row > '') {
            $columnLetter = Coordinate::stringFromColumnIndex((int) $column);
            $spreadsheet->getActiveSheet()->getStyle($columnLetter . $row)->applyFromArray($styleData);
        }
    }

    private function addWidth(Spreadsheet $spreadsheet, string $columnWidth, string $startCol, string $endCol): void
    {
        if ($columnWidth > '') {
            if ($startCol == $endCol) {
                $startCol = Coordinate::stringFromColumnIndex((int) $startCol);
                $spreadsheet->getActiveSheet()->getColumnDimension($startCol)->setWidth((float) $columnWidth);
            } else {
                $startCol = Coordinate::stringFromColumnIndex((int) $startCol);
                $endCol = Coordinate::stringFromColumnIndex((int) $endCol);
                $spreadsheet->getActiveSheet()->getColumnDimension($startCol)->setWidth((float) $columnWidth);
                do {
                    $spreadsheet->getActiveSheet()->getColumnDimension(++$startCol)->setWidth((float) $columnWidth);
                } while ($startCol !== $endCol);
            }
        }
    }

    private function processPRecord(array $rowData, Spreadsheet &$spreadsheet): void
    {
        //    Read shared styles
        $formatArray = [];
        $fromFormats = ['\-', '\ '];
        $toFormats = ['-', ' '];
        foreach ($rowData as $rowDatum) {
            switch ($rowDatum[0]) {
                case 'P':
                    $formatArray['numberFormat']['formatCode'] = str_replace($fromFormats, $toFormats, substr($rowDatum, 1));

                    break;
                case 'E':
                case 'F':
                    $formatArray['font']['name'] = substr($rowDatum, 1);

                    break;
                case 'M':
                    $formatArray['font']['size'] = substr($rowDatum, 1) / 20;

                    break;
                case 'L':
                    $this->processPColors($rowDatum, $formatArray);

                    break;
                case 'S':
                    $this->processPFontStyles($rowDatum, $formatArray);

                    break;
            }
        }
        $this->processPFinal($spreadsheet, $formatArray);
    }

    private function processPColors(string $rowDatum, array &$formatArray): void
    {
        if (preg_match('/L([1-9]\\d*)/', $rowDatum, $matches)) {
            $fontColor = $matches[1] % 8;
            $formatArray['font']['color']['argb'] = self::COLOR_ARRAY[$fontColor];
        }
    }

    private function processPFontStyles(string $rowDatum, array &$formatArray): void
    {
        $styleSettings = substr($rowDatum, 1);
        $iMax = strlen($styleSettings);
        for ($i = 0; $i < $iMax; ++$i) {
            if (array_key_exists($styleSettings[$i], self::FONT_STYLE_MAPPINGS)) {
                $formatArray['font'][self::FONT_STYLE_MAPPINGS[$styleSettings[$i]]] = true;
            }
        }
    }

    private function processPFinal(Spreadsheet &$spreadsheet, array $formatArray): void
    {
        if (array_key_exists('numberFormat', $formatArray)) {
            $this->formats['P' . $this->format] = $formatArray;
            ++$this->format;
        } elseif (array_key_exists('font', $formatArray)) {
            ++$this->fontcount;
            $this->fonts[$this->fontcount] = $formatArray;
            if ($this->fontcount === 1) {
                $spreadsheet->getDefaultStyle()->applyFromArray($formatArray);
            }
        }
    }

    /**
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance.
     *
     * @param string $filename
     *
     * @return Spreadsheet
     */
    public function loadIntoExisting($filename, Spreadsheet $spreadsheet)
    {
        // Open file
        $this->canReadOrBust($filename);
        $fileHandle = $this->fileHandle;
        rewind($fileHandle);

        // Create new Worksheets
        while ($spreadsheet->getSheetCount() <= $this->sheetIndex) {
            $spreadsheet->createSheet();
        }
        $spreadsheet->setActiveSheetIndex($this->sheetIndex);
        $spreadsheet->getActiveSheet()->setTitle(substr(basename($filename, '.slk'), 0, Worksheet::SHEET_TITLE_MAXIMUM_LENGTH));

        // Loop through file
        $column = $row = '';

        // loop through one row (line) at a time in the file
        while (($rowDataTxt = fgets($fileHandle)) !== false) {
            // convert SYLK encoded $rowData to UTF-8
            $rowDataTxt = StringHelper::SYLKtoUTF8($rowDataTxt);

            // explode each row at semicolons while taking into account that literal semicolon (;)
            // is escaped like this (;;)
            $rowData = explode("\t", str_replace('¤', ';', str_replace(';', "\t", str_replace(';;', '¤', rtrim($rowDataTxt)))));

            $dataType = array_shift($rowData);
            if ($dataType == 'P') {
                //    Read shared styles
                $this->processPRecord($rowData, $spreadsheet);
            } elseif ($dataType == 'C') {
                //    Read cell value data
                $this->processCRecord($rowData, $spreadsheet, $row, $column);
            } elseif ($dataType == 'F') {
                //    Read cell formatting
                $this->processFRecord($rowData, $spreadsheet, $row, $column);
            } else {
                $this->columnRowFromRowData($rowData, $column, $row);
            }
        }

        // Close file
        fclose($fileHandle);

        // Return
        return $spreadsheet;
    }

    private function columnRowFromRowData(array $rowData, string &$column, string &$row): void
    {
        foreach ($rowData as $rowDatum) {
            $char0 = $rowDatum[0];
            if ($char0 === 'X' || $char0 == 'C') {
                $column = substr($rowDatum, 1);
            } elseif ($char0 === 'Y' || $char0 == 'R') {
                $row = substr($rowDatum, 1);
            }
        }
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
     * @param int $sheetIndex Sheet index
     *
     * @return $this
     */
    public function setSheetIndex($sheetIndex)
    {
        $this->sheetIndex = $sheetIndex;

        return $this;
    }
}
