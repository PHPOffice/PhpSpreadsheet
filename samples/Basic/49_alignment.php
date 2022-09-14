<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

require __DIR__ . '/../Header.php';

$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setTitle('Alignment');
$sheet = $spreadsheet->getActiveSheet();
$hi = 'Hi There';
$ju = 'This is a longer than normal sentence';
$sheet->fromArray([
    ['', 'default', 'bottom', 'top', 'center', 'justify', 'distributed'],
    ['default', $hi, $hi, $hi, $hi, $hi, $hi],
    ['left', $hi, $hi, $hi, $hi, $hi, $hi],
    ['right', $hi, $hi, $hi, $hi, $hi, $hi],
    ['center', $hi, $hi, $hi, $hi, $hi, $hi],
    ['justify', $ju, $ju, $ju, $ju, $ju, $ju],
    ['distributed', $ju, $ju, $ju, $ju, $ju, $ju],
]);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(20);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(20);
$sheet->getRowDimension(2)->setRowHeight(30);
$sheet->getRowDimension(3)->setRowHeight(30);
$sheet->getRowDimension(4)->setRowHeight(30);
$sheet->getRowDimension(5)->setRowHeight(30);
$sheet->getRowDimension(6)->setRowHeight(40);
$sheet->getRowDimension(7)->setRowHeight(40);
$minRow = 2;
$maxRow = 7;
$minCol = 'B';
$maxCol = 'g';
$sheet->getStyle("C$minRow:C$maxRow")
    ->getAlignment()
    ->setVertical(Alignment::VERTICAL_BOTTOM);
$sheet->getStyle("D$minRow:D$maxRow")
    ->getAlignment()
    ->setVertical(Alignment::VERTICAL_TOP);
$sheet->getStyle("E$minRow:E$maxRow")
    ->getAlignment()
    ->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle("F$minRow:F$maxRow")
    ->getAlignment()
    ->setVertical(Alignment::VERTICAL_JUSTIFY);
$sheet->getStyle("G$minRow:G$maxRow")
    ->getAlignment()
    ->setVertical(Alignment::VERTICAL_DISTRIBUTED);
$sheet->getStyle("{$minCol}3:{$maxCol}3")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
$sheet->getStyle("{$minCol}4:{$maxCol}4")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle("{$minCol}5:{$maxCol}5")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("{$minCol}6:{$maxCol}6")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_JUSTIFY);
$sheet->getStyle("{$minCol}7:{$maxCol}7")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_DISTRIBUTED);

$sheet->getCell('A9')->setValue('Center Continuous A9-C9');
$sheet->getStyle('A9:C9')
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER_CONTINUOUS);
$sheet->getCell('A10')->setValue('Fill');
$sheet->getStyle('A10')
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_FILL);
$sheet->setSelectedCells('A1');

$helper->write($spreadsheet, __FILE__, ['Xlsx', 'Html', 'Xls']);
