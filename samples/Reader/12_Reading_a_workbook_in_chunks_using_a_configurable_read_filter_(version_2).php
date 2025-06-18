<?php

namespace Samples\Sample12;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example2.xls';

/**  Define a Read Filter class implementing IReadFilter  */
class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    /**
     * Set the list of rows that we want to read.
     *
     * @param mixed $startRow
     * @param mixed $chunkSize
     */
    public function setRows($startRow, $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the rows that are configured in $this->_startRow and $this->_endRow
        if (($row == 1) || ($row >= $this->startRow && $row < $this->endRow)) {
            return true;
        }

        return false;
    }
}

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory with a defined reader type of ' . $inputFileType);
// Create a new Reader of the type defined in $inputFileType
$reader = IOFactory::createReader($inputFileType);

// Define how many rows we want to read for each "chunk"
$chunkSize = 20;
// Create a new Instance of our Read Filter
$chunkFilter = new ChunkReadFilter();

// Tell the Reader that we want to use the Read Filter that we've Instantiated
$reader->setReadFilter($chunkFilter);

// Loop to read our worksheet in "chunk size" blocks
for ($startRow = 2; $startRow <= 240; $startRow += $chunkSize) {
    $helper->log('Loading WorkSheet using configurable filter for headings row 1 and for rows ' . $startRow . ' to ' . ($startRow + $chunkSize - 1));
    // Tell the Read Filter, the limits on which rows we want to read this iteration
    $chunkFilter->setRows($startRow, $chunkSize);
    // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
    $spreadsheet = $reader->load($inputFileName);

    // Do some processing here

    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    var_dump($sheetData);
}
