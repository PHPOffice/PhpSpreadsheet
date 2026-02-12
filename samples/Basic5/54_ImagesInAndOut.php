<?php

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

$path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'images/blue_square.png';
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setTitle('53_ImageOpacity');

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Images in-cell and out');
$sheet->setShowGridLines(false);

$helper->log('Add in-cell image to spreadsheet');
$sheet->setCellValue('B1', 'inside');
$drawing = new Drawing();
$drawing->setName('Blue Square in-cell');
$drawing->setPath($path);
$sheet->getCell('B2')->setValue($drawing);

$helper->log('Add image over cell to spreadsheet');
$sheet->setCellValue('C1', 'outside');
$drawing = new Drawing();
$drawing->setName('Blue Square out-of-cell');
$drawing->setPath($path);
$drawing->setCoordinates('C2');
$drawing->setCoordinates2('D6');
$drawing->setWorksheet($sheet);

$helper->log('In-cell supported only for Xlsx');
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
$spreadsheet->disconnectWorksheets();
