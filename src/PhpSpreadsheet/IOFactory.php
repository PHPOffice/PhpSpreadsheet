<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;

/**
 * Factory to create readers and writers easily.
 *
 * It is not required to use this class, but it should make it easier to read and write files.
 * Especially for reading files with an unknown format.
 */
abstract class IOFactory
{
    public const READER_XLSX = 'Xlsx';
    public const READER_XLS = 'Xls';
    public const READER_XML = 'Xml';
    public const READER_ODS = 'Ods';
    public const READER_SYLK = 'Slk';
    public const READER_SLK = 'Slk';
    public const READER_GNUMERIC = 'Gnumeric';
    public const READER_HTML = 'Html';
    public const READER_CSV = 'Csv';

    public const WRITER_XLSX = 'Xlsx';
    public const WRITER_XLS = 'Xls';
    public const WRITER_ODS = 'Ods';
    public const WRITER_CSV = 'Csv';
    public const WRITER_HTML = 'Html';

    /** @var array<string, class-string<IReader>> */
    private static array $readers = [
        self::READER_XLSX => Reader\Xlsx::class,
        self::READER_XLS => Reader\Xls::class,
        self::READER_XML => Reader\Xml::class,
        self::READER_ODS => Reader\Ods::class,
        self::READER_SLK => Reader\Slk::class,
        self::READER_GNUMERIC => Reader\Gnumeric::class,
        self::READER_HTML => Reader\Html::class,
        self::READER_CSV => Reader\Csv::class,
    ];

    /** @var array<string, class-string<IWriter>> */
    private static array $writers = [
        self::WRITER_XLS => Writer\Xls::class,
        self::WRITER_XLSX => Writer\Xlsx::class,
        self::WRITER_ODS => Writer\Ods::class,
        self::WRITER_CSV => Writer\Csv::class,
        self::WRITER_HTML => Writer\Html::class,
        'Tcpdf' => Writer\Pdf\Tcpdf::class,
        'Dompdf' => Writer\Pdf\Dompdf::class,
        'Mpdf' => Writer\Pdf\Mpdf::class,
    ];

    /**
     * Create Writer\IWriter.
     */
    public static function createWriter(Spreadsheet $spreadsheet, string $writerType): IWriter
    {
        if (!isset(self::$writers[$writerType])) {
            throw new Writer\Exception("No writer found for type $writerType");
        }

        // Instantiate writer
        $className = self::$writers[$writerType];

        return new $className($spreadsheet);
    }

    /**
     * Create IReader.
     */
    public static function createReader(string $readerType): IReader
    {
        if (!isset(self::$readers[$readerType])) {
            throw new Reader\Exception("No reader found for type $readerType");
        }

        // Instantiate reader
        $className = self::$readers[$readerType];

        return new $className();
    }

    /**
     * Loads Spreadsheet from file using automatic Reader\IReader resolution.
     *
     * @param string $filename The name of the spreadsheet file
     * @param int $flags the optional second parameter flags may be used to identify specific elements
     *                       that should be loaded, but which won't be loaded by default, using these values:
     *                            IReader::LOAD_WITH_CHARTS - Include any charts that are defined in the loaded file.
     *                            IReader::READ_DATA_ONLY - Read cell values only, not formatting or merge structure.
     *                            IReader::IGNORE_EMPTY_CELLS - Don't load empty cells into the model.
     * @param string[] $readers An array of Readers to use to identify the file type. By default, load() will try
     *                             all possible Readers until it finds a match; but this allows you to pass in a
     *                             list of Readers so it will only try the subset that you specify here.
     *                          Values in this list can be any of the constant values defined in the set
     *                                 IOFactory::READER_*.
     */
    public static function load(string $filename, int $flags = 0, ?array $readers = null): Spreadsheet
    {
        $reader = self::createReaderForFile($filename, $readers);

        return $reader->load($filename, $flags);
    }

    /**
     * Identify file type using automatic IReader resolution.
     */
    public static function identify(string $filename, ?array $readers = null): string
    {
        $reader = self::createReaderForFile($filename, $readers);
        $className = $reader::class;
        $classType = explode('\\', $className);
        unset($reader);

        return array_pop($classType);
    }

    /**
     * Create Reader\IReader for file using automatic IReader resolution.
     *
     * @param string[] $readers An array of Readers to use to identify the file type. By default, load() will try
     *                             all possible Readers until it finds a match; but this allows you to pass in a
     *                             list of Readers so it will only try the subset that you specify here.
     *                          Values in this list can be any of the constant values defined in the set
     *                                 IOFactory::READER_*.
     */
    public static function createReaderForFile(string $filename, ?array $readers = null): IReader
    {
        File::assertFile($filename);

        $testReaders = self::$readers;
        if ($readers !== null) {
            $readers = array_map('strtoupper', $readers);
            $testReaders = array_filter(
                self::$readers,
                fn (string $readerType): bool => in_array(strtoupper($readerType), $readers, true),
                ARRAY_FILTER_USE_KEY
            );
        }

        // First, lucky guess by inspecting file extension
        $guessedReader = self::getReaderTypeFromExtension($filename);
        if (($guessedReader !== null) && array_key_exists($guessedReader, $testReaders)) {
            $reader = self::createReader($guessedReader);

            // Let's see if we are lucky
            if ($reader->canRead($filename)) {
                return $reader;
            }
        }

        // If we reach here then "lucky guess" didn't give any result
        // Try walking through all the options in self::$readers (or the selected subset)
        foreach ($testReaders as $readerType => $class) {
            //    Ignore our original guess, we know that won't work
            if ($readerType !== $guessedReader) {
                $reader = self::createReader($readerType);
                if ($reader->canRead($filename)) {
                    return $reader;
                }
            }
        }

        throw new Reader\Exception('Unable to identify a reader for this file');
    }

    /**
     * Guess a reader type from the file extension, if any.
     */
    private static function getReaderTypeFromExtension(string $filename): ?string
    {
        $pathinfo = pathinfo($filename);
        if (!isset($pathinfo['extension'])) {
            return null;
        }

        return match (strtolower($pathinfo['extension'])) {
            // Excel (OfficeOpenXML) Spreadsheet
            'xlsx',
            // Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
            'xlsm',
            // Excel (OfficeOpenXML) Template
            'xltx',
            // Excel (OfficeOpenXML) Macro Template (macros will be discarded)
            'xltm' => 'Xlsx',
            // Excel (BIFF) Spreadsheet
            'xls',
            // Excel (BIFF) Template
            'xlt' => 'Xls',
            // Open/Libre Offic Calc
            'ods',
            // Open/Libre Offic Calc Template
            'ots' => 'Ods',
            'slk' => 'Slk',
            // Excel 2003 SpreadSheetML
            'xml' => 'Xml',
            'gnumeric' => 'Gnumeric',
            'htm', 'html' => 'Html',
            // Do nothing
            // We must not try to use CSV reader since it loads
            // all files including Excel files etc.
            'csv' => null,
            default => null,
        };
    }

    /**
     * Register a writer with its type and class name.
     *
     * @param class-string<IWriter> $writerClass
     */
    public static function registerWriter(string $writerType, string $writerClass): void
    {
        if (!is_a($writerClass, IWriter::class, true)) {
            throw new Writer\Exception('Registered writers must implement ' . IWriter::class);
        }

        self::$writers[$writerType] = $writerClass;
    }

    /**
     * Register a reader with its type and class name.
     */
    public static function registerReader(string $readerType, string $readerClass): void
    {
        if (!is_a($readerClass, IReader::class, true)) {
            throw new Reader\Exception('Registered readers must implement ' . IReader::class);
        }

        self::$readers[$readerType] = $readerClass;
    }
}
