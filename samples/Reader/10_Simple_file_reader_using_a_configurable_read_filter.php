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
    public function __construct(
        private int $startRow,
        private int $endRow,
        private array $columns
    ) {
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        if ($row >= $this->startRow && $row <= $this->endRow) {
            if (in_array($columnAddress, $this->columns)) {
                return true;
            }
        }

        return false;
    }
}

$filterSubset = new MyReadFilter(9, 15, range('G', 'K'));

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory with a defined reader type of ' . $inputFileType);
$helper->log('Filter range is G9:K15');
$reader = IOFactory::createReader($inputFileType);
$helper->log('Loading Sheet "' . $sheetname . '" only');
$reader->setLoadSheetsOnly($sheetname);
$helper->log('Loading Sheet using configurable filter');
$reader->setReadFilter($filterSubset);
$spreadsheet = $reader->load($inputFileName);

$activeRange = $spreadsheet->getActiveSheet()->calculateWorksheetDataDimension();
$sheetData = $spreadsheet->getActiveSheet()->rangeToArray($activeRange, null, true, true, true);
$helper->displayGrid($sheetData);
