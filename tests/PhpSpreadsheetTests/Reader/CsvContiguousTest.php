<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CsvContiguousTest extends TestCase
{
    /**
     * @var string
     */
    private $inputFileName = 'samples/Reader/sampleData/example2.csv';

    public function testContiguous(): void
    {
        // Create a new Reader of the type defined in $inputFileType
        $reader = new Csv();

        // Define how many rows we want to read for each "chunk"
        $chunkSize = 100;
        // Create a new Instance of our Read Filter
        $chunkFilter = new CsvContiguousFilter();

        // Tell the Reader that we want to use the Read Filter that we've Instantiated
        // and that we want to store it in contiguous rows/columns
        self::assertFalse($reader->getContiguous());
        $reader->setReadFilter($chunkFilter);
        $reader->setContiguous(true);

        // Instantiate a new PhpSpreadsheet object manually
        $spreadsheet = new Spreadsheet();

        // Set a sheet index
        $sheet = 0;
        // Loop to read our worksheet in "chunk size" blocks
        /**  $startRow is set to 2 initially because we always read the headings in row #1  * */
        for ($startRow = 2; $startRow <= 240; $startRow += $chunkSize) {
            // Tell the Read Filter, the limits on which rows we want to read this iteration
            $chunkFilter->setRows($startRow, $chunkSize);

            // Increment the worksheet index pointer for the Reader
            $reader->setSheetIndex($sheet);
            // Load only the rows that match our filter into a new worksheet in the PhpSpreadsheet Object
            $reader->loadIntoExisting($this->inputFileName, $spreadsheet);
            // Set the worksheet title (to reference the "sheet" of data that we've loaded)
            // and increment the sheet index as well
            $spreadsheet->getActiveSheet()->setTitle('Country Data #' . (++$sheet));
        }

        $sheet = $spreadsheet->getSheetByName('Country Data #1');
        self::assertEquals('Kabul', $sheet->getCell('A2')->getValue());
        $sheet = $spreadsheet->getSheetByName('Country Data #2');
        self::assertEquals('Lesotho', $sheet->getCell('B4')->getValue());
        $sheet = $spreadsheet->getSheetByName('Country Data #3');
        self::assertEquals(-20.1, $sheet->getCell('C6')->getValue());
    }

    public function testContiguous2(): void
    {
        // Create a new Reader of the type defined in $inputFileType
        $reader = new Csv();

        // Create a new Instance of our Read Filter
        $chunkFilter = new CsvContiguousFilter();
        $chunkFilter->setFilterType(1);

        // Tell the Reader that we want to use the Read Filter that we've Instantiated
        // and that we want to store it in contiguous rows/columns
        $reader->setReadFilter($chunkFilter);
        $reader->setContiguous(true);

        // Instantiate a new PhpSpreadsheet object manually
        $spreadsheet = new Spreadsheet();

        // Loop to read our worksheet in "chunk size" blocks
        $reader->loadIntoExisting($this->inputFileName, $spreadsheet);

        $sheet = $spreadsheet->getActiveSheet();
        self::assertEquals('Kabul', $sheet->getCell('A2')->getValue());
        self::assertEquals('Kuwait', $sheet->getCell('B11')->getValue());
    }
}
