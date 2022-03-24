<?php

use PhpOffice\PhpSpreadsheet\Reader\Xls;

require __DIR__ . '/../Header.php';

$inputFileName = __DIR__ . '/sampleData/example1.xls';

$helper->log('Loading file ' . /** @scrutinizer ignore-type */ pathinfo($inputFileName, PATHINFO_BASENAME) . ' information using Xls reader');

$reader = new Xls();
$worksheetNames = $reader->listWorksheetNames($inputFileName);

$helper->log('<h3>Worksheet Names</h3>');
$helper->log('<ol>');
foreach ($worksheetNames as $worksheetName) {
    $helper->log('<li>' . $worksheetName . '</li>');
}
$helper->log('</ol>');
