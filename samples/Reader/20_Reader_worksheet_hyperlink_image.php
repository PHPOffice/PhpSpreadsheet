<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xlsx';
$inputFileName = __DIR__ . '/sampleData/example3.xlsx';

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' information using IOFactory with a defined reader type of ' . $inputFileType);

$reader = IOFactory::createReader($inputFileType);

$spreadsheet = $reader->load($inputFileName);

$helper->log('Set active sheet index 0');
$aSheet = $spreadsheet->setActiveSheetIndex(0);
$drawings = $aSheet->getDrawingCollection();
/** @var $dr \PhpOffice\PhpSpreadsheet\Worksheet\Drawing */
foreach ($drawings as $dr) {
    $helper->log('links ' . $dr->getHyperlink()->getUrl());
}
$helper->log('End');
