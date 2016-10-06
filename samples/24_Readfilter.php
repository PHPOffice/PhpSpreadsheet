<?php

namespace PhpOffice\PhpSpreadsheet;

require __DIR__ . '/Header.php';

// Write temporary file
$largeSpreadsheet = require __DIR__ . '/templates/largeSpreadsheet.php';
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($largeSpreadsheet);
$filename = $helper->getTemporaryFilename();
$callStartTime = microtime(true);
$writer->save($filename);
$helper->logWrite($writer, $filename, $callStartTime);

class MyReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($column, $row, $worksheetName = '')
    {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }

        return false;
    }
}

$helper->log('Load from Xlsx file');
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$reader->setReadFilter(new MyReadFilter());
$callStartTime = microtime(true);
$spreadsheet = $reader->load($filename);
$helper->logRead('Xlsx', $filename, $callStartTime);
$helper->log('Remove unnecessary rows');
$spreadsheet->getActiveSheet()->removeRow(2, 18);

// Save
$helper->write($spreadsheet, __FILE__);
