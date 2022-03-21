<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example1.xls';
$sheetnames = ['Data Sheet #1', 'Data Sheet #3'];

$helper->log('Loading file ' . /** @scrutinizer ignore-type */ pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory with a defined reader type of ' . $inputFileType);
$reader = IOFactory::createReader($inputFileType);
$helper->log('Loading Sheet' . ((count($sheetnames) == 1) ? '' : 's') . ' "' . implode('" and "', $sheetnames) . '" only');
$reader->setLoadSheetsOnly($sheetnames);
$spreadsheet = $reader->load($inputFileName);

$helper->log($spreadsheet->getSheetCount() . ' worksheet' . (($spreadsheet->getSheetCount() == 1) ? '' : 's') . ' loaded');
$loadedSheetNames = $spreadsheet->getSheetNames();
foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
    $helper->log($sheetIndex . ' -> ' . $loadedSheetName);
}
