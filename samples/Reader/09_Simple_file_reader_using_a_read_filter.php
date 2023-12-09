<?php

namespace Samples\Sample12;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example1.xls';
$sheetname = 'Data Sheet #3';

class MyReadFilter implements IReadFilter
{
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        // Read rows 9 to 15 and columns A to E only
        if ($row >= 9 && $row <= 15) {
            if (in_array($columnAddress, range('A', 'E'))) {
                return true;
            }
        }

        return false;
    }
}

$filterSubset = new MyReadFilter();

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory with a defined reader type of ' . $inputFileType);
$helper->log('Filter range is A9:E15');
$reader = IOFactory::createReader($inputFileType);
$helper->log('Loading Sheet "' . $sheetname . '" only');
$reader->setLoadSheetsOnly($sheetname);
$helper->log('Loading Sheet using filter');
$reader->setReadFilter($filterSubset);
$spreadsheet = $reader->load($inputFileName);

$activeRange = $spreadsheet->getActiveSheet()->calculateWorksheetDataDimension();
$sheetData = $spreadsheet->getActiveSheet()->rangeToArray($activeRange, null, true, true, true);
$helper->displayGrid($sheetData);
