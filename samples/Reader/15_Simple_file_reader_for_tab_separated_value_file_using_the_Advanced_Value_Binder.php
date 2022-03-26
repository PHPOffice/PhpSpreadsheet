<?php

use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

require __DIR__ . '/../Header.php';

Cell::setValueBinder(new AdvancedValueBinder());

$inputFileName = __DIR__ . '/sampleData/example1.tsv';

$reader = new Csv();
$helper->log('Loading file ' . /** @scrutinizer ignore-type */ pathinfo($inputFileName, PATHINFO_BASENAME) . ' into WorkSheet #1 using Csv reader');
$reader->setDelimiter("\t");
$spreadsheet = $reader->load($inputFileName);
$spreadsheet->getActiveSheet()->setTitle(/** @scrutinizer ignore-type */ pathinfo($inputFileName, PATHINFO_BASENAME));

$helper->log($spreadsheet->getSheetCount() . ' worksheet' . (($spreadsheet->getSheetCount() == 1) ? '' : 's') . ' loaded');
$loadedSheetNames = $spreadsheet->getSheetNames();
foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    $helper->log('<b>Worksheet #' . $sheetIndex . ' -> ' . $loadedSheetName . ' (Formatted)</b>');
    $spreadsheet->setActiveSheetIndexByName($loadedSheetName);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    var_dump($sheetData);
}

foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    $helper->log('<b>Worksheet #' . $sheetIndex . ' -> ' . $loadedSheetName . ' (Unformatted)</b>');
    $spreadsheet->setActiveSheetIndexByName($loadedSheetName);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, false, true);
    var_dump($sheetData);
}

foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    $helper->log('<b>Worksheet #' . $sheetIndex . ' -> ' . $loadedSheetName . ' (Raw)</b>');
    $spreadsheet->setActiveSheetIndexByName($loadedSheetName);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, false, false, true);
    var_dump($sheetData);
}
