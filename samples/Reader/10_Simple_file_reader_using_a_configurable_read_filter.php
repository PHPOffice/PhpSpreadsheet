<?php

namespace Sample;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example1.xls';
$sheetname = 'Data Sheet #3';

class MyReadFilter implements IReadFilter
{
    private $_startRow = 0;
    private $_endRow = 0;
    private $_columns = [];

    public function __construct($startRow, $endRow, $columns)
    {
        $this->_startRow = $startRow;
        $this->_endRow = $endRow;
        $this->_columns = $columns;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        if ($row >= $this->_startRow && $row <= $this->_endRow) {
            if (in_array($column, $this->_columns)) {
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
