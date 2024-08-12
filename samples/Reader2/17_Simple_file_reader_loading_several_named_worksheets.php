<?php

use PhpOffice\PhpSpreadsheet\Reader\Xls;

require __DIR__ . '/../Header.php';

$inputFileName = __DIR__ . '/sampleData/example1.xls';

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using Xls reader');
$reader = new Xls();

// Read the list of Worksheet Names from the Workbook file
$helper->log('Read the list of Worksheets in the WorkBook');
$worksheetNames = $reader->listWorksheetNames($inputFileName);

$helper->log('There are ' . count($worksheetNames) . ' worksheet' . ((count($worksheetNames) == 1) ? '' : 's') . ' in the workbook');
foreach ($worksheetNames as $worksheetName) {
    $helper->log($worksheetName);
}
