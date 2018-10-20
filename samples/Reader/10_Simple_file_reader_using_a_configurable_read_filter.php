<?php

namespace Samples\Sample10;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example1.xls';
$sheetname = 'Data Sheet #3';

class MyReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    private $columns = [];

    public function __construct($startRow, $endRow, $columns)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        $this->columns = $columns;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if ($row >= $this->startRow && $row <= $this->endRow) {
            if (in_array($column, $this->columns)) {
                return true;
            }
        }

        return false;
    }
}

$filterSubset = new MyReadFilter(9, 15, range('G', 'K'));

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory with a defined reader type of ' . $inputFileType);
$reader = IOFactory::createReader($inputFileType);
$helper->log('Loading Sheet "' . $sheetname . '" only');
$reader->setLoadSheetsOnly($sheetname);
$helper->log('Loading Sheet using configurable filter');
$reader->setReadFilter($filterSubset);
$spreadsheet = $reader->load($inputFileName);

$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
var_dump($sheetData);
