<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$inputFileName = __DIR__ . '/sampleData/example1.xls';

$inputFileType = IOFactory::identify($inputFileName);
$helper->log('File ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' has been identified as an ' . $inputFileType . ' file');

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory with the identified reader type');
$reader = IOFactory::createReader($inputFileType);
$spreadsheet = $reader->load($inputFileName);

$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
var_dump($sheetData);
