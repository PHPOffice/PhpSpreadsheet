<?php

use PhpOffice\PhpSpreadsheet\Reader\Xls;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$inputFileName = __DIR__ . '/sampleData/example1.xls';

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' information using Xls reader');

$reader = new Xls();
$worksheetNames = $reader->listWorksheetNames($inputFileName);

$helper->log('<h3>Worksheet Names</h3>');
$helper->log('<ol>');
foreach ($worksheetNames as $worksheetName) {
    $helper->log('<li>' . $worksheetName . '</li>');
}
$helper->log('</ol>');
