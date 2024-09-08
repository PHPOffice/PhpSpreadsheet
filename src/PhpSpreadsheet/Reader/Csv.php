<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Csv\Delimiter;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
     * @var ?string
     */
    private $escapeCharacter;

    /**
     * The character that will be supplied to fgetcsv
     * when escapeCharacter is null.
     * It is anticipated that it will conditionally be set
     * to null-string for Php9 and above.
     */
    private static string $defaultEscapeCharacter = '\\';

    /**
     * Callback for setting defaults in construction.
     *
     * @var ?callable
     */
    private static $constructorCallback;

    /**
     * Attempt autodetect line endings (deprecated after PHP8.1)?
     *
     * @var bool
     */
    private $testAutodetect = true;

    /**
     * @var bool
     */
    protected $castFormattedNumberToNumeric = false;

    /**
     * @var bool
     */
    protected $preserveNumericFormatting = false;

    /** @var bool */
    private $preserveNullString = false;

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

    public function setInputEncoding(string $encoding): self
    {
        $this->inputEncoding = $encoding;

        return $this;
    }

    public function getInputEncoding(): string
    {
        return $this->inputEncoding;
    }

    public function setFallbackEncoding(string $fallbackEncoding): self
    {
        $this->fallbackEncoding = $fallbackEncoding;

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

        $inferenceEngine = new Delimiter($this->fileHandle, $this->escapeCharacter ?? self::$defaultEscapeCharacter, $this->enclosure);

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
    public function listWorksheetInfo(string $filename): array
    {
        // Open file
        $this->openFileOrMemory($filename);
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
        $rowData = self::getCsv($fileHandle, 0, $this->delimiter ?? '', $this->enclosure, $this->escapeCharacter);
        while (is_array($rowData)) {
            ++$worksheetInfo[0]['totalRows'];
            $worksheetInfo[0]['lastColumnIndex'] = max($worksheetInfo[0]['lastColumnIndex'], count($rowData) - 1);
            $rowData = self::getCsv($fileHandle, 0, $this->delimiter ?? '', $this->enclosure, $this->escapeCharacter);
        }

        $worksheetInfo[0]['lastColumnLetter'] = Coordinate::stringFromColumnIndex($worksheetInfo[0]['lastColumnIndex'] + 1);
        $worksheetInfo[0]['totalColumns'] = $worksheetInfo[0]['lastColumnIndex'] + 1;

        // Close file
        fclose($fileHandle);

        return $worksheetInfo;
    }

    /**
     * Loads Spreadsheet from file.
     */
    protected function loadSpreadsheetFromFile(string $filename): Spreadsheet
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Load into this instance
        return $this->loadIntoExisting($filename, $spreadsheet);
    }

    /**
     * Loads Spreadsheet from string.
     */
    public function loadSpreadsheetFromString(string $contents): Spreadsheet
    {
        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Load into this instance
        return $this->loadStringOrFile('data://text/plain,' . urlencode($contents), $spreadsheet, true);
    }

    private function openFileOrMemory(string $filename): void
    {
        // Open file
        $fhandle = $this->canRead($filename);
        if (!$fhandle) {
            throw new Exception($filename . ' is an Invalid Spreadsheet file.');
        }
        if ($this->inputEncoding === self::GUESS_ENCODING) {
            $this->inputEncoding = self::guessEncoding($filename, $this->fallbackEncoding);
        }
        $this->openFile($filename);
        if ($this->inputEncoding !== 'UTF-8') {
            fclose($this->fileHandle);
            $entireFile = file_get_contents($filename);
            $fileHandle = fopen('php://memory', 'r+b');
            if ($fileHandle !== false && $entireFile !== false) {
                $this->fileHandle = $fileHandle;
                $data = StringHelper::convertEncoding($entireFile, 'UTF-8', $this->inputEncoding);
                fwrite($this->fileHandle, $data);
                $this->skipBOM();
            }
        }
    }

    public function setTestAutoDetect(bool $value): self
    {
        $this->testAutodetect = $value;

        return $this;
    }

    private function setAutoDetect(?string $value): ?string
    {
        $retVal = null;
        if ($value !== null && $this->testAutodetect) {
            $retVal2 = @ini_set('auto_detect_line_endings', $value);
            if (is_string($retVal2)) {
                $retVal = $retVal2;
            }
        }

        return $retVal;
    }

    public function castFormattedNumberToNumeric(
        bool $castFormattedNumberToNumeric,
        bool $preserveNumericFormatting = false
    ): void {
        $this->castFormattedNumberToNumeric = $castFormattedNumberToNumeric;
        $this->preserveNumericFormatting = $preserveNumericFormatting;
    }

    /**
     * Open data uri for reading.
     */
    private function openDataUri(string $filename): void
    {
        $fileHandle = fopen($filename, 'rb');
        if ($fileHandle === false) {
            // @codeCoverageIgnoreStart
            throw new ReaderException('Could not open file ' . $filename . ' for reading.');
            // @codeCoverageIgnoreEnd
        }

        $this->fileHandle = $fileHandle;
    }

    /**
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance.
     */
    public function loadIntoExisting(string $filename, Spreadsheet $spreadsheet): Spreadsheet
    {
        return $this->loadStringOrFile($filename, $spreadsheet, false);
    }

    /**
     * Loads PhpSpreadsheet from file into PhpSpreadsheet instance.
     */
    private function loadStringOrFile(string $filename, Spreadsheet $spreadsheet, bool $dataUri): Spreadsheet
    {
        // Deprecated in Php8.1
        $iniset = $this->setAutoDetect('1');

        // Open file
        if ($dataUri) {
            $this->openDataUri($filename);
        } else {
            $this->openFileOrMemory($filename);
        }
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
        $rowData = self::getCsv($fileHandle, 0, $this->delimiter ?? '', $this->enclosure, $this->escapeCharacter);
        $valueBinder = Cell::getValueBinder();
        $preserveBooleanString = method_exists($valueBinder, 'getBooleanConversion') && $valueBinder->getBooleanConversion();
        while (is_array($rowData)) {
            $noOutputYet = true;
            $columnLetter = 'A';
            foreach ($rowData as $rowDatum) {
                $this->convertBoolean($rowDatum, $preserveBooleanString);
                $numberFormatMask = $this->convertFormattedNumber($rowDatum);
                if (($rowDatum !== '' || $this->preserveNullString) && $this->readFilter->readCell($columnLetter, $currentRow)) {
                    if ($this->contiguous) {
                        if ($noOutputYet) {
                            $noOutputYet = false;
                            ++$outRow;
                        }
                    } else {
                        $outRow = $currentRow;
                    }
                    // Set basic styling for the value (Note that this could be overloaded by styling in a value binder)
                    $sheet->getCell($columnLetter . $outRow)->getStyle()
                        ->getNumberFormat()
                        ->setFormatCode($numberFormatMask);
                    // Set cell value
                    $sheet->getCell($columnLetter . $outRow)->setValue($rowDatum);
                }
                ++$columnLetter;
            }
            $rowData = self::getCsv($fileHandle, 0, $this->delimiter ?? '', $this->enclosure, $this->escapeCharacter);
            ++$currentRow;
        }

        // Close file
        fclose($fileHandle);

        $this->setAutoDetect($iniset);

        // Return
        return $spreadsheet;
    }

    /**
     * Convert string true/false to boolean, and null to null-string.
     *
     * @param mixed $rowDatum
     */
    private function convertBoolean(&$rowDatum, bool $preserveBooleanString): void
    {
        if (is_string($rowDatum) && !$preserveBooleanString) {
            if (strcasecmp(Calculation::getTRUE(), $rowDatum) === 0 || strcasecmp('true', $rowDatum) === 0) {
                $rowDatum = true;
            } elseif (strcasecmp(Calculation::getFALSE(), $rowDatum) === 0 || strcasecmp('false', $rowDatum) === 0) {
                $rowDatum = false;
            }
        } else {
            $rowDatum = $rowDatum ?? '';
        }
    }

    /**
     * Convert numeric strings to int or float values.
     *
     * @param mixed $rowDatum
     */
    private function convertFormattedNumber(&$rowDatum): string
    {
        $numberFormatMask = NumberFormat::FORMAT_GENERAL;
        if ($this->castFormattedNumberToNumeric === true && is_string($rowDatum)) {
            $numeric = str_replace(
                [StringHelper::getThousandsSeparator(), StringHelper::getDecimalSeparator()],
                ['', '.'],
                $rowDatum
            );

            if (is_numeric($numeric)) {
                $decimalPos = strpos($rowDatum, StringHelper::getDecimalSeparator());
                if ($this->preserveNumericFormatting === true) {
                    $numberFormatMask = (strpos($rowDatum, StringHelper::getThousandsSeparator()) !== false)
                        ? '#,##0' : '0';
                    if ($decimalPos !== false) {
                        $decimals = strlen($rowDatum) - $decimalPos - 1;
                        $numberFormatMask .= '.' . str_repeat('0', min($decimals, 6));
                    }
                }

                $rowDatum = ($decimalPos !== false) ? (float) $numeric : (int) $numeric;
            }
        }

        return $numberFormatMask;
    }

    public function getDelimiter(): ?string
    {
        return $this->delimiter;
    }

    public function setDelimiter(?string $delimiter): self
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

    public function setSheetIndex(int $indexValue): self
    {
        $this->sheetIndex = $indexValue;

        return $this;
    }

    public function setContiguous(bool $contiguous): self
    {
        $this->contiguous = $contiguous;

        return $this;
    }

    public function getContiguous(): bool
    {
        return $this->contiguous;
    }

    /**
     * Php9 intends to drop support for this parameter in fgetcsv.
     * Not yet ready to mark deprecated in order to give users
     * a migration path.
     */
    public function setEscapeCharacter(string $escapeCharacter): self
    {
        $this->escapeCharacter = $escapeCharacter;

        return $this;
    }

    public function getEscapeCharacter(): string
    {
        return $this->escapeCharacter ?? self::$defaultEscapeCharacter;
    }

    /**
     * Can the current IReader read the file?
     */
    public function canRead(string $filename): bool
    {
        // Check if file exists
        try {
            $this->openFile($filename);
        } catch (ReaderException $e) {
            return false;
        }

        fclose($this->fileHandle);

        // Trust file extension if any
        $extension = strtolower(/** @scrutinizer ignore-type */ pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($extension, ['csv', 'tsv'])) {
            return true;
        }

        // Attempt to guess mimetype
        $type = mime_content_type($filename);
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

    public function setPreserveNullString(bool $value): self
    {
        $this->preserveNullString = $value;

        return $this;
    }

    public function getPreserveNullString(): bool
    {
        return $this->preserveNullString;
    }

    /**
     * Php8.4 deprecates use of anything other than null string
     * as escape Character.
     *
     * @param resource $stream
     *
     * @return array<int,?string>|false
     */
    private static function getCsv(
        $stream,
        ?int $length = null,
        string $separator = ',',
        string $enclosure = '"',
        ?string $escape = null
    ) {
        $escape = $escape ?? self::$defaultEscapeCharacter;
        if (PHP_VERSION_ID >= 80400 && $escape !== '') {
            return @fgetcsv($stream, $length, $separator, $enclosure, $escape);
        }

        return fgetcsv($stream, $length, $separator, $enclosure, $escape);
    }
}
