<?php

use PhpOffice\PhpSpreadsheet\Reader\Csv;

require __DIR__ . '/../Header.php';

$inputFileNames = [__DIR__ . '/sampleData/example1.csv', __DIR__ . '/sampleData/example2.csv'];

$reader = new Csv();
$inputFileName = array_shift($inputFileNames);
$helper->log('Loading file ' . /** @scrutinizer ignore-type */ pathinfo($inputFileName, PATHINFO_BASENAME) . ' into WorkSheet #1 using Csv Reader');
$spreadsheet = $reader->load($inputFileName);
$spreadsheet->getActiveSheet()->setTitle(/** @scrutinizer ignore-type */ pathinfo($inputFileName, PATHINFO_BASENAME));
foreach ($inputFileNames as $sheet => $inputFileName) {
    $helper->log('Loading file ' . /** @scrutinizer ignore-type */ pathinfo($inputFileName, PATHINFO_BASENAME) . ' into WorkSheet #' . ($sheet + 2) . ' using Csv Reader');
    $reader->setSheetIndex($sheet + 1);
    $reader->loadIntoExisting($inputFileName, $spreadsheet);
    $spreadsheet->getActiveSheet()->setTitle(/** @scrutinizer ignore-type */ pathinfo($inputFileName, PATHINFO_BASENAME));
}

$helper->log($spreadsheet->getSheetCount() . ' worksheet' . (($spreadsheet->getSheetCount() == 1) ? '' : 's') . ' loaded');
$loadedSheetNames = $spreadsheet->getSheetNames();
foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    $helper->log('<b>Worksheet #' . $sheetIndex . ' -> ' . $loadedSheetName . '</b>');
    $spreadsheet->setActiveSheetIndexByName($loadedSheetName);

    $activeRange = $spreadsheet->getActiveSheet()->calculateWorksheetDataDimension();
    $sheetData = $spreadsheet->getActiveSheet()->rangeToArray($activeRange, null, true, true, true);
    $helper->displayGrid($sheetData);
}
