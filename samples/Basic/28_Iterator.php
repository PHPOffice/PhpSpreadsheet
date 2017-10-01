<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require __DIR__ . '/../Header.php';

$sampleSpreadsheet = require __DIR__ . '/../templates/sampleSpreadsheet.php';
$filename = $helper->getTemporaryFilename();
$writer = new Xlsx($sampleSpreadsheet);
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

$callStartTime = microtime(true);
$reader = IOFactory::createReader('Xlsx');
$spreadsheet = $reader->load($filename);
$helper->logRead('Xlsx', $filename, $callStartTime);
$helper->log('Iterate worksheets');
foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
    $helper->log('Worksheet - ' . $worksheet->getTitle());

    foreach ($worksheet->getRowIterator() as $row) {
        $helper->log('    Row number - ' . $row->getRowIndex());

        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
        foreach ($cellIterator as $cell) {
            if ($cell !== null) {
                $helper->log('        Cell - ' . $cell->getCoordinate() . ' - ' . $cell->getCalculatedValue());
            }
        }
    }
}
