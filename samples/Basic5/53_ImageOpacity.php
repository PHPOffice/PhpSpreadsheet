<?php

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

$path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'images/blue_square.png';
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setTitle('53_ImageOpacity');

$helper->log('Add image to spreadsheet 6 times with different opacities');
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Squares different opacities');
$sheet->setShowGridLines(false);

$drawing = new Drawing();
$drawing->setName('Blue Square opacity not specified');
$drawing->setPath($path);
$drawing->setCoordinates('A1');
$drawing->setCoordinates2('B5');
$drawing->setWorksheet($sheet);

$drawing = new Drawing();
$drawing->setName('Blue Square opacity 80%');
$drawing->setPath($path);
$drawing->setCoordinates('C1');
$drawing->setCoordinates2('D5');
$drawing->setOpacity(80000);
$drawing->setWorksheet($sheet);

$drawing = new Drawing();
$drawing->setWorksheet($sheet);
$drawing->setName('Blue Square opacity 60%');
$drawing->setPath($path);
$drawing->setCoordinates('E1');
$drawing->setCoordinates2('F5');
$drawing->setOpacity(60000);

$drawing = new Drawing();
$drawing->setName('Blue Square opacity 40%');
$drawing->setPath($path);
$drawing->setCoordinates('A8');
$drawing->setCoordinates2('B12');
$drawing->setOpacity(40000);
$drawing->setWorksheet($sheet);

$drawing = new Drawing();
$drawing->setName('Blue Square opacity 20%');
$drawing->setPath($path);
$drawing->setCoordinates('C8');
$drawing->setCoordinates2('D12');
$drawing->setOpacity(20000);
$drawing->setWorksheet($sheet);

$drawing = new Drawing();
$drawing->setWorksheet($sheet);
$drawing->setName('Blue Square opacity 0%');
$drawing->setPath($path);
$drawing->setCoordinates('E8');
$drawing->setCoordinates2('F12');
$drawing->setOpacity(0);

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx', 'Html', 'Dompdf', 'Mpdf']);
$spreadsheet->disconnectWorksheets();
