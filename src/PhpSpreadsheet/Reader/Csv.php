<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Csv extends BaseReader
{
    /**
     * Input encoding.
     *
     * @var string
     */
    private $inputEncoding = 'UTF-8';

    /**
     * Delimiter.
     *
     * @var string
     */
    private $delimiter;

    /**
     * Enclosure.
     *
     * @var string
     */
    private $enclosure = '"';

    /**
     * Sheet index to read.
     *
     * @var int
     */
    private $sheetIndex = 0;

    /**
     * Load rows contiguously.
     *
     * @var bool
     */
    private $contiguous = false;

    /**
     * Row counter for loading rows contiguously.
     *
     * @var int
     */
    private $contiguousRow = -1;

    /**
     * The character that can escape the enclosure.
     *
     * @var string
     */
    private $escapeCharacter = '\\';

    /**
     * Create a new CSV Reader instance.
     */
    public function __construct()
    {
        $this->readFilter = new DefaultReadFilter();
    }

    /**
     * Set input encoding.
     *
     * @param string $pValue Input encoding, eg: 'UTF-8'
     *
     * @return Csv
     */
    public function setInputEncoding($pValue)
    {
        $this->inputEncoding = $pValue;

        return $this;
    }

    /**
     * Get input encoding.
     *
     * @return string
     */
    public function getInputEncoding()
    {
        return $this->inputEncoding;
    }

    /**
     * Move filepointer past any BOM marker.
     */
    protected function skipBOM()
    {
        rewind($this->fileHandle);

        switch ($this->inputEncoding) {
            case 'UTF-8':
                fgets($this->fileHandle, 4) == "\xEF\xBB\xBF" ?
                    fseek($this->fileHandle, 3) : fseek($this->fileHandle, 0);

                break;
            case 'UTF-16LE':
                fgets($this->fileHandle, 3) == "\xFF\xFE" ?
                    fseek($this->fileHandle, 2) : fseek($this->fileHandle, 0);

                break;
            case 'UTF-16BE':
                fgets($this->fileHandle, 3) == "\xFE\xFF" ?
                    fseek($this->fileHandle, 2) : fseek($this->fileHandle, 0);

                break;
            case 'UTF-32LE':
                fgets($this->fileHandle, 5) == "\xFF\xFE\x00\x00" ?
                    fseek($this->fileHandle, 4) : fseek($this->fileHandle, 0);

                break;
            case 'UTF-32BE':
                fgets($this->fileHandle, 5) == "\x00\x00\xFE\xFF" ?
                    fseek($this->fileHandle, 4) : fseek($this->fileHandle, 0);

                break;
            default:
                break;
        }
    }

    /**
     * Identify any separator that is explicitly set in the file.
     */
    protected function checkSeparator()
    {
        $line = fgets($this->fileHandle);
        if ($line === false) {
            return;
        }

        if ((strlen(trim($line, "\r\n")) == 5) && (stripos($line, 'sep=') === 0)) {
            $this->delimiter = substr($line, 4, 1);

            return;
        }

        return $this->skipBOM();
    }

    /**
     * Infer the separator if it isn't explicitly set in the file or specified by the user.
     */
    protected function inferSeparator()
    {
        if ($this->delimiter !== null) {
            return;
        }

        $potentialDelimiters = [',', ';', "\t", '|', ':', ' '];
        $counts = [];
        foreach ($potentialDelimiters as $delimiter) {
            $counts[$delimiter] = [];
        }

        // Count how many times each of the potential delimiters appears in each line
        $numberLines = 0;
        while (($line = $this->getNextLine()) !== false && (++$numberLines < 1000)) {
            $countLine = [];
            for ($i = strlen($line) - 1; $i >= 0; --$i) {
                $char = $line[$i];
                if (isset($counts[$char])) {
                    if (!isset($countLine[$char])) {
                        $countLine[$char] = 0;
                    }
                    ++$countLine[$char];
                }
            }
            foreach ($potentialDelimiters as $delimiter) {
                $counts[$delimiter][] = isset($countLine[$delimiter])
                    ? $countLine[$delimiter]
                    : 0;
            }
        }

        // Calculate the mean square deviations for each delimiter (ignoring delimiters that haven't been found consistently)
        $meanSquareDeviations = [];
        $middleIdx = floor(($numberLines - 1) / 2);

        foreach ($potentialDelimiters as $delimiter) {
            $series = $counts[$delimiter];
            sort($series);

            $median = ($numberLines % 2)
                ? $series[$middleIdx]
                : ($series[$middleIdx] + $series[$middleIdx + 1]) / 2;

            if ($median === 0) {
                continue;
            }

            $meanSquareDeviations[$delimiter] = array_reduce(
                $series,
                function ($sum, $value) use ($median) {
                    return $sum + pow($value - $median, 2);
                }
            ) / count($series);
        }

        // ... and pick the delimiter with the smallest mean square deviation (in case of ties, the order in potentialDelimiters is respected)
        $min = INF;
        foreach ($potentialDelimiters as $delimiter) {
            if (!isset($meanSquareDeviations[$delimiter])) {
                continue;
            }

            if ($meanSquareDeviations[$delimiter] < $min) {
                $min = $meanSquareDeviations[$delimiter];
                $this->delimiter = $delimiter;
            }
        }

        // If no delimiter could be detected, fall back to the default
        if ($this->delimiter === null) {
            $this->delimiter = reset($potentialDelimiters);
        }

        return $this->skipBOM();
    }

    /**
     * Get the next full line from the file.
     *
     * @param string $line
     *
     * @return bool|string
     */
    private function getNextLine($line = '')
    {
        // Get the next line in the file
        $newLine = fgets($this->fileHandle);

        // Return false if there is no next line
        if ($newLine === false) {
            return false;
        }

        // Add the new line to the line passed in
        $line = $line . $newLine;

        // Drop everything that is enclosed to avoid counting false positives in enclosures
        $enclosure = preg_quote($this->enclosure, '/');
        $line = preg_replace('/(' . $enclosure . '.*' . $enclosure . ')/U', '', $line);

        // See if we have any enclosures left in the line
        $matches = [];
        preg_match('/(' . $enclosure . ')/', $line, $matches);

        // if we still have an enclosure then we need to read the next line aswell
        if (count($matches) > 0) {
            $line = $this->getNextLine($line);
        }

        return $line;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @param string $pFilename
     *
     * @throws Exception
     *
     * @return array
     */
    public function listWorksheetInfo($pFilename)
    {
        // Open file
        if (!$this->canRead($pFilename)) {
            throw new Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        $this->openFile($pFilename);
        $fileHandle = $this->fileHandle;

        // Skip BOM, if any
        $this->skipBOM();
        $this->checkSeparator();
        $this->inferSeparator();

        $worksheetInfo = [];
        $worksheetInfo[0]['worksheetName'] = 'Worksheet';
        $worksheetInfo[0]['lastColumnLetter'] = 'A';
        $worksheetInfo[0]['lastColumnIndex'] = 0;
        $worksheetInfo[0]['totalRows'] = 0;
        $worksheetInfo[0]['totalColumns'] = 0;

        // Loop through each line of the file in turn
        while (($rowData = fgetcsv($fileHandle, 0, $this->delimiter, $this->enclosure, $this->escapeCharacter)) !== false) {
            ++$worksheetInfo[0]['totalRows'];
            $worksheetInfo[0]['lastColumnIndex'] = max($worksheetInfo[0]['lastColumnIndex'], count($rowData) - 1);
        }

        $worksheetInfo[0]['lastColumnLetter'] = Coordinate::stringFromColumnIndex($worksheetInfo[0]['lastColumnIndex'] + 1);
        $worksheetInfo[0]['totalColumns'] = $worksheetInfo[0]['lastColumnIndex'] + 1;

        // Close file
        fclose($fileHandle);

        return $worksheetInfo;
    }

    /**
     * Loads Spreadsheet from file.
     *
     * @param string $pFilename
     *
     * @throws Exception
     *
     * @return Spreadsheet
     */
    public function load($pFilename)
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Load into this instance
        return $this->loadIntoExisting($pFilename, $spreadsheet);
    }

    /**
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance.
     *
     * @param string $pFilename
     * @param Spreadsheet $spreadsheet
     *
     * @throws Exception
     *
     * @return Spreadsheet
     */
    public function loadIntoExisting($pFilename, Spreadsheet $spreadsheet)
    {
        $lineEnding = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', true);

        // Open file
        if (!$this->canRead($pFilename)) {
            throw new Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        $this->openFile($pFilename);
        $fileHandle = $this->fileHandle;

        // Skip BOM, if any
        $this->skipBOM();
        $this->checkSeparator();
        $this->inferSeparator();

        // Create new PhpSpreadsheet object
        while ($spreadsheet->getSheetCount() <= $this->sheetIndex) {
            $spreadsheet->createSheet();
        }
        $sheet = $spreadsheet->setActiveSheetIndex($this->sheetIndex);

        // Set our starting row based on whether we're in contiguous mode or not
        $currentRow = 1;
        if ($this->contiguous) {
            $currentRow = ($this->contiguousRow == -1) ? $sheet->getHighestRow() : $this->contiguousRow;
        }

        // Loop through each line of the file in turn
        while (($rowData = fgetcsv($fileHandle, 0, $this->delimiter, $this->enclosure, $this->escapeCharacter)) !== false) {
            $columnLetter = 'A';
            foreach ($rowData as $rowDatum) {
                if ($rowDatum != '' && $this->readFilter->readCell($columnLetter, $currentRow)) {
                    // Convert encoding if necessary
                    if ($this->inputEncoding !== 'UTF-8') {
                        $rowDatum = StringHelper::convertEncoding($rowDatum, 'UTF-8', $this->inputEncoding);
                    }

                    // Set cell value
                    $sheet->getCell($columnLetter . $currentRow)->setValue($rowDatum);
                }
                ++$columnLetter;
            }
            ++$currentRow;
        }

        // Close file
        fclose($fileHandle);

        if ($this->contiguous) {
            $this->contiguousRow = $currentRow;
        }

        ini_set('auto_detect_line_endings', $lineEnding);

        // Return
        return $spreadsheet;
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
     * @param string $delimiter Delimiter, eg: ','
     *
     * @return CSV
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

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
     * @param string $enclosure Enclosure, defaults to "
     *
     * @return CSV
     */
    public function setEnclosure($enclosure)
    {
        if ($enclosure == '') {
            $enclosure = '"';
        }
        $this->enclosure = $enclosure;

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
    public function setSheetIndex($pValue)
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    /**
     * Set Contiguous.
     *
     * @param bool $contiguous
     *
     * @return Csv
     */
    public function setContiguous($contiguous)
    {
        $this->contiguous = (bool) $contiguous;
        if (!$contiguous) {
            $this->contiguousRow = -1;
        }

        return $this;
    }

    /**
     * Get Contiguous.
     *
     * @return bool
     */
    public function getContiguous()
    {
        return $this->contiguous;
    }

    /**
     * Set escape backslashes.
     *
     * @param string $escapeCharacter
     *
     * @return $this
     */
    public function setEscapeCharacter($escapeCharacter)
    {
        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }

    /**
     * Get escape backslashes.
     *
     * @return string
     */
    public function getEscapeCharacter()
    {
        return $this->escapeCharacter;
    }

    /**
     * Can the current IReader read the file?
     *
     * @param string $pFilename
     *
     * @return bool
     */
    public function canRead($pFilename)
    {
        // Check if file exists
        try {
            $this->openFile($pFilename);
        } catch (Exception $e) {
            return false;
        }

        fclose($this->fileHandle);

        // Trust file extension if any
        if (strtolower(pathinfo($pFilename, PATHINFO_EXTENSION)) === 'csv') {
            return true;
        }

        // Attempt to guess mimetype
        $type = mime_content_type($pFilename);
        $supportedTypes = [
            'text/csv',
            'text/plain',
            'inode/x-empty',
        ];

        return in_array($type, $supportedTypes, true);
    }
}
