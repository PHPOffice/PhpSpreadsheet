<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example1.xls';

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' information using IOFactory with a defined reader type of ' . $inputFileType);

$reader = IOFactory::createReader($inputFileType);
$worksheetNames = $reader->listWorksheetNames($inputFileName);

$helper->log('<h3>Worksheet Names</h3>');
$helper->log('<ol>');
foreach ($worksheetNames as $worksheetName) {
    $helper->log('<li>' . $worksheetName . '</li>');
}
$helper->log('</ol>');
