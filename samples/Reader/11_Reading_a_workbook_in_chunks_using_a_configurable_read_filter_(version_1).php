<?php

namespace Samples\Sample09;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

require __DIR__ . '/../Header.php';
/** @var \PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example2.xls';

/**  Define a Read Filter class implementing IReadFilter  */
class ChunkReadFilter implements IReadFilter
{
    private int $startRow;

    private int $endRow;

    /**
     * We expect a list of the rows that we want to read to be passed into the constructor.
     */
    public function __construct(int $startRow, int $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        //  Only read the heading row, and the rows that were configured in the constructor
        if (($row == 1) || ($row >= $this->startRow && $row < $this->endRow)) {
            return true;
        }

        return false;
    }
}

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory with a defined reader type of ' . $inputFileType);
// Create a new Reader of the type defined in $inputFileType
$reader = IOFactory::createReader($inputFileType);

// Define how many rows we want for each "chunk"
$chunkSize = 20;

// Loop to read our worksheet in "chunk size" blocks
for ($startRow = 2; $startRow <= 240; $startRow += $chunkSize) {
    $helper->log('Loading WorkSheet using configurable filter for headings row 1 and for rows ' . $startRow . ' to ' . ($startRow + $chunkSize - 1));
    // Create a new Instance of our Read Filter, passing in the limits on which rows we want to read
    $chunkFilter = new ChunkReadFilter($startRow, $chunkSize);
    // Tell the Reader that we want to use the new Read Filter that we've just Instantiated
    $reader->setReadFilter($chunkFilter);
    // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
    $spreadsheet = $reader->load($inputFileName);

    // Do some processing here

    $activeRange = $spreadsheet->getActiveSheet()->calculateWorksheetDataDimension();
    $sheetData = $spreadsheet->getActiveSheet()->rangeToArray($activeRange, null, true, true, true);
    $helper->displayGrid($sheetData);
}
