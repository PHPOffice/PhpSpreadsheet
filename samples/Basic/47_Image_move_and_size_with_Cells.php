<?php

// Create new Spreadsheet object
use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

require __DIR__ . '/../Header.php';

$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Add a drawing to the worksheet
$helper->log('Add a drawing to the worksheet');
$drawing = new Drawing();
$drawing->setName('PhpSpreadsheet');
$drawing->setDescription('PhpSpreadsheet');
$drawing->setPath(__DIR__ . '/../images/PhpSpreadsheet_logo.png');
$drawing->setAnchorType(Drawing::ANCHORTYPE_TWOCELL);
$drawing->setCoordinates('A1');
$drawing->setCoordinates2('A1');
$drawing->setOffsetX2($drawing->getImageWidth());
$drawing->setOffsetY2($drawing->getImageHeight());
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$helper->log('Default row height: ' . $spreadsheet->getActiveSheet()->getRowDimension(1)->getRowHeight());

$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth($drawing->getImageWidth(), Dimension::UOM_PIXELS);
$spreadsheet->getActiveSheet()->getRowDimension(1)->setRowHeight($drawing->getImageHeight(), Dimension::UOM_PIXELS);

$helper->write($spreadsheet, __FILE__);
