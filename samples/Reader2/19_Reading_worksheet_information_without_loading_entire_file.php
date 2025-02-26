<?php

use PhpOffice\PhpSpreadsheet\Reader\Xls;

require __DIR__ . '/../Header.php';

$inputFileType = 'Xls';
$inputFileName = __DIR__ . '/sampleData/example1.xls';

$helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' information using Xls reader');

$reader = new Xls();
$worksheetData = $reader->listWorksheetInfo($inputFileName);

$helper->log('<h3>Worksheet Information</h3>');
$helper->log('<ol>');
foreach ($worksheetData as $worksheet) {
    $helper->log('<li>' . $worksheet['worksheetName']);
    $helper->log('Rows: ' . $worksheet['totalRows'] . ' Columns: ' . $worksheet['totalColumns']);
    $helper->log('Cell Range: A1:' . $worksheet['lastColumnLetter'] . $worksheet['totalRows']);
    $helper->log('</li>');
}
$helper->log('</ol>');
