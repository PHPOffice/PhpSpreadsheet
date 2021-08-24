<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Csv\Delimiter;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Csv extends BaseReader
{
    const DEFAULT_FALLBACK_ENCODING = 'CP1252';
    const GUESS_ENCODING = 'guess';
    const UTF8_BOM = "\xEF\xBB\xBF";
    const UTF8_BOM_LEN = 3;
    const UTF16BE_BOM = "\xfe\xff";
    const UTF16BE_BOM_LEN = 2;
    const UTF16BE_LF = "\x00\x0a";
    const UTF16LE_BOM = "\xff\xfe";
    const UTF16LE_BOM_LEN = 2;
    const UTF16LE_LF = "\x0a\x00";
    const UTF32BE_BOM = "\x00\x00\xfe\xff";
    const UTF32BE_BOM_LEN = 4;
    const UTF32BE_LF = "\x00\x00\x00\x0a";
    const UTF32LE_BOM = "\xff\xfe\x00\x00";
    const UTF32LE_BOM_LEN = 4;
    const UTF32LE_LF = "\x0a\x00\x00\x00";

    /**
     * Input encoding.
     *
     * @var string
     */
    private $inputEncoding = 'UTF-8';

    /**
     * Fallback encoding if guess strikes out.
     *
     * @var string
     */
    private $fallbackEncoding = self::DEFAULT_FALLBACK_ENCODING;

    /**
     * Delimiter.
     *
     * @var ?string
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
     * The character that can escape the enclosure.
     *
     * @var string
     */
    private $escapeCharacter = '\\';

    /**
     * Callback for setting defaults in construction.
     *
     * @var ?callable
     */
    private static $constructorCallback;

    /**
     * Create a new CSV Reader instance.
     */
    public function __construct()
    {
        parent::__construct();
        $callback = self::$constructorCallback;
        if ($callback !== null) {
            $callback($this);
        }
    }

    /**
     * Set a callback to change the defaults.
     *
     * The callback must accept the Csv Reader object as the first parameter,
     * and it should return void.
     */
    public static function setConstructorCallback(?callable $callback): void
    {
        self::$constructorCallback = $callback;
    }

    public static function getConstructorCallback(): ?callable
    {
        return self::$constructorCallback;
    }

    public function setInputEncoding(string $pValue): self
    {
        $this->inputEncoding = $pValue;

        return $this;
    }

    public function getInputEncoding(): string
    {
        return $this->inputEncoding;
    }

    public function setFallbackEncoding(string $pValue): self
    {
        $this->fallbackEncoding = $pValue;

        return $this;
    }

    public function getFallbackEncoding(): string
    {
        return $this->fallbackEncoding;
    }

    /**
     * Move filepointer past any BOM marker.
     */
    protected function skipBOM(): void
    {
        rewind($this->fileHandle);

        if (fgets($this->fileHandle, self::UTF8_BOM_LEN + 1) !== self::UTF8_BOM) {
            rewind($this->fileHandle);
        }
    }

    /**
     * Identify any separator that is explicitly set in the file.
     */
    protected function checkSeparator(): void
    {
        $line = fgets($this->fileHandle);
        if ($line === false) {
            return;
        }

        if ((strlen(trim($line, "\r\n")) == 5) && (stripos($line, 'sep=') === 0)) {
            $this->delimiter = substr($line, 4, 1);

            return;
        }

        $this->skipBOM();
    }

    /**
     * Infer the separator if it isn't explicitly set in the file or specified by the user.
     */
    protected function inferSeparator(): void
    {
        if ($this->delimiter !== null) {
            return;
        }

        $inferenceEngine = new Delimiter($this->fileHandle, $this->escapeCharacter, $this->enclosure);

        // If number of lines is 0, nothing to infer : fall back to the default
        if ($inferenceEngine->linesCounted() === 0) {
            $this->delimiter = $inferenceEngine->getDefaultDelimiter();
            $this->skipBOM();

            return;
        }

        $this->delimiter = $inferenceEngine->infer();

        // If no delimiter could be detected, fall back to the default
        if ($this->delimiter === null) {
            $this->delimiter = $inferenceEngine->getDefaultDelimiter();
        }

        $this->skipBOM();
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     */
    public function listWorksheetInfo(string $pFilename): array
    {
        // Open file
        $this->openFileOrMemory($pFilename);
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
        $rowData = fgetcsv($fileHandle, 0, $this->delimiter ?? '', $this->enclosure, $this->escapeCharacter);
        while (is_array($rowData)) {
            ++$worksheetInfo[0]['totalRows'];
            $worksheetInfo[0]['lastColumnIndex'] = max($worksheetInfo[0]['lastColumnIndex'], count($rowData) - 1);
            $rowData = fgetcsv($fileHandle, 0, $this->delimiter ?? '', $this->enclosure, $this->escapeCharacter);
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
     * @return Spreadsheet
     */
    public function load(string $pFilename, int $flags = 0)
    {
        $this->processFlags($flags);

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Load into this instance
        return $this->loadIntoExisting($pFilename, $spreadsheet);
    }

    private function openFileOrMemory(string $pFilename): void
    {
        // Open file
        $fhandle = $this->canRead($pFilename);
        if (!$fhandle) {
            throw new Exception($pFilename . ' is an Invalid Spreadsheet file.');
        }
        if ($this->inputEncoding === self::GUESS_ENCODING) {
            $this->inputEncoding = self::guessEncoding($pFilename, $this->fallbackEncoding);
        }
        $this->openFile($pFilename);
        if ($this->inputEncoding !== 'UTF-8') {
            fclose($this->fileHandle);
            $entireFile = file_get_contents($pFilename);
            $this->fileHandle = fopen('php://memory', 'r+b');
            if ($this->fileHandle !== false && $entireFile !== false) {
                $data = StringHelper::convertEncoding($entireFile, 'UTF-8', $this->inputEncoding);
                fwrite($this->fileHandle, $data);
                $this->skipBOM();
            }
        }
    }

    private static function setAutoDetect(?string $value): ?string
    {
        $retVal = null;
        if ($value !== null) {
            $retVal2 = @ini_set('auto_detect_line_endings', $value);
            if (is_string($retVal2)) {
                $retVal = $retVal2;
            }
        }

        return $retVal;
    }

    /**
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance.
     */
    public function loadIntoExisting(string $pFilename, Spreadsheet $spreadsheet): Spreadsheet
    {
        // Deprecated in Php8.1
        $iniset = self::setAutoDetect('1');

        // Open file
        $this->openFileOrMemory($pFilename);
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
        $outRow = 0;

        // Loop through each line of the file in turn
        $rowData = fgetcsv($fileHandle, 0, $this->delimiter ?? '', $this->enclosure, $this->escapeCharacter);
        while (is_array($rowData)) {
            $noOutputYet = true;
            $columnLetter = 'A';
            foreach ($rowData as $rowDatum) {
                self::convertBoolean($rowDatum);
                if ($rowDatum !== '' && $this->readFilter->readCell($columnLetter, $currentRow)) {
                    if ($this->contiguous) {
                        if ($noOutputYet) {
                            $noOutputYet = false;
                            ++$outRow;
                        }
                    } else {
                        $outRow = $currentRow;
                    }
                    // Set cell value
                    $sheet->getCell($columnLetter . $outRow)->setValue($rowDatum);
                }
                ++$columnLetter;
            }
            $rowData = fgetcsv($fileHandle, 0, $this->delimiter ?? '', $this->enclosure, $this->escapeCharacter);
            ++$currentRow;
        }

        // Close file
        fclose($fileHandle);

        self::setAutoDetect($iniset);

        // Return
        return $spreadsheet;
    }

    /**
     * Convert string true/false to boolean, and null to null-string.
     *
     * @param mixed $rowDatum
     */
    private static function convertBoolean(&$rowDatum): void
    {
        if (is_string($rowDatum)) {
            if (strcasecmp('true', $rowDatum) === 0) {
                $rowDatum = true;
            } elseif (strcasecmp('false', $rowDatum) === 0) {
                $rowDatum = false;
            }
        } elseif ($rowDatum === null) {
            $rowDatum = '';
        }
    }

    public function getDelimiter(): ?string
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

    public function setEnclosure(string $enclosure): self
    {
        if ($enclosure == '') {
            $enclosure = '"';
        }
        $this->enclosure = $enclosure;

        return $this;
    }

    public function getSheetIndex(): int
    {
        return $this->sheetIndex;
    }

    public function setSheetIndex(int $pValue): self
    {
        $this->sheetIndex = $pValue;

        return $this;
    }

    public function setContiguous(bool $contiguous): self
    {
        $this->contiguous = (bool) $contiguous;

        return $this;
    }

    public function getContiguous(): bool
    {
        return $this->contiguous;
    }

    public function setEscapeCharacter(string $escapeCharacter): self
    {
        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }

    public function getEscapeCharacter(): string
    {
        return $this->escapeCharacter;
    }

    /**
     * Scrutinizer believes, incorrectly, that the specific pathinfo
     * call in canRead can return something other than an array.
     * Phpstan knows better.
     * This function satisfies both.
     *
     * @param mixed $extension
     */
    private static function extractStringLower($extension): string
    {
        return is_string($extension) ? strtolower($extension) : '';
    }

    /**
     * Can the current IReader read the file?
     */
    public function canRead(string $pFilename): bool
    {
        // Check if file exists
        try {
            $this->openFile($pFilename);
        } catch (ReaderException $e) {
            return false;
        }

        fclose($this->fileHandle);

        // Trust file extension if any
        $extension = self::extractStringLower(pathinfo($pFilename, PATHINFO_EXTENSION));
        if (in_array($extension, ['csv', 'tsv'])) {
            return true;
        }

        // Attempt to guess mimetype
        $type = mime_content_type($pFilename);
        $supportedTypes = [
            'application/csv',
            'text/csv',
            'text/plain',
            'inode/x-empty',
        ];

        return in_array($type, $supportedTypes, true);
    }

    private static function guessEncodingTestNoBom(string &$encoding, string &$contents, string $compare, string $setEncoding): void
    {
        if ($encoding === '') {
            $pos = strpos($contents, $compare);
            if ($pos !== false && $pos % strlen($compare) === 0) {
                $encoding = $setEncoding;
            }
        }
    }

    private static function guessEncodingNoBom(string $filename): string
    {
        $encoding = '';
        $contents = file_get_contents($filename);
        self::guessEncodingTestNoBom($encoding, $contents, self::UTF32BE_LF, 'UTF-32BE');
        self::guessEncodingTestNoBom($encoding, $contents, self::UTF32LE_LF, 'UTF-32LE');
        self::guessEncodingTestNoBom($encoding, $contents, self::UTF16BE_LF, 'UTF-16BE');
        self::guessEncodingTestNoBom($encoding, $contents, self::UTF16LE_LF, 'UTF-16LE');
        if ($encoding === '' && preg_match('//u', $contents) === 1) {
            $encoding = 'UTF-8';
        }

        return $encoding;
    }

    private static function guessEncodingTestBom(string &$encoding, string $first4, string $compare, string $setEncoding): void
    {
        if ($encoding === '') {
            if ($compare === substr($first4, 0, strlen($compare))) {
                $encoding = $setEncoding;
            }
        }
    }

    private static function guessEncodingBom(string $filename): string
    {
        $encoding = '';
        $first4 = file_get_contents($filename, false, null, 0, 4);
        if ($first4 !== false) {
            self::guessEncodingTestBom($encoding, $first4, self::UTF8_BOM, 'UTF-8');
            self::guessEncodingTestBom($encoding, $first4, self::UTF16BE_BOM, 'UTF-16BE');
            self::guessEncodingTestBom($encoding, $first4, self::UTF32BE_BOM, 'UTF-32BE');
            self::guessEncodingTestBom($encoding, $first4, self::UTF32LE_BOM, 'UTF-32LE');
            self::guessEncodingTestBom($encoding, $first4, self::UTF16LE_BOM, 'UTF-16LE');
        }

        return $encoding;
    }

    public static function guessEncoding(string $filename, string $dflt = self::DEFAULT_FALLBACK_ENCODING): string
    {
        $encoding = self::guessEncodingBom($filename);
        if ($encoding === '') {
            $encoding = self::guessEncodingNoBom($filename);
        }

        return ($encoding === '') ? $dflt : $encoding;
    }
}
