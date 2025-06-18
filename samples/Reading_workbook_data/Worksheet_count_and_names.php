<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example2.xls';

// Create a new Reader of the type defined in $inputFileType
$reader = IOFactory::createReader($inputFileType);
// Load $inputFileName to a PhpSpreadsheet Object
$spreadsheet = $reader->load($inputFileName);

// Use the PhpSpreadsheet object's getSheetCount() method to get a count of the number of WorkSheets in the WorkBook
$sheetCount = $spreadsheet->getSheetCount();
$helper->log('There ' . (($sheetCount == 1) ? 'is' : 'are') . ' ' . $sheetCount . ' WorkSheet' . (($sheetCount == 1) ? '' : 's') . ' in the WorkBook');

$helper->log('Reading the names of Worksheets in the WorkBook');
// Use the PhpSpreadsheet object's getSheetNames() method to get an array listing the names/titles of the WorkSheets in the WorkBook
$sheetNames = $spreadsheet->getSheetNames();
foreach ($sheetNames as $sheetIndex => $sheetName) {
    $helper->log('WorkSheet #' . $sheetIndex . ' is named "' . $sheetName . '"');
}
