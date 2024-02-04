<?php

// Create new Spreadsheet object
use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

require __DIR__ . '/../Header.php';

$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->getCell('A1')->setValue('twocell');
$sheet->getCell('A2')->setValue('twocell');
$sheet->getCell('A3')->setValue('onecell');
$sheet->getCell('A6')->setValue('absolute');

// Add a drawing to the worksheet
$helper->log('Add a drawing to the worksheet two-cell anchor not resized');
$drawing = new Drawing();
$drawing->setName('PhpSpreadsheet');
$drawing->setDescription('PhpSpreadsheet');
$drawing->setPath(__DIR__ . '/../images/PhpSpreadsheet_logo.png');
// anchor type will be two-cell because Coordinates2 is set
//$drawing->setAnchorType(Drawing::ANCHORTYPE_TWOCELL);
$drawing->setCoordinates('B1');
$drawing->setCoordinates2('B1');
$drawing->setOffsetX2($drawing->getImageWidth());
$drawing->setOffsetY2($drawing->getImageHeight());
$drawing->setWorksheet($spreadsheet->getActiveSheet());

// Add a drawing to the worksheet
$helper->log('Add a drawing to the worksheet two-cell anchor resized');
$drawing2 = new Drawing();
$drawing2->setName('PhpSpreadsheet');
$drawing2->setDescription('PhpSpreadsheet');
$drawing2->setPath(__DIR__ . '/../images/PhpSpreadsheet_logo.png');
// anchor type will be two-cell because Coordinates2 is set
//$drawing->setAnchorType(Drawing::ANCHORTYPE_TWOCELL);
$drawing2->setCoordinates('C2');
$drawing2->setCoordinates2('C2');
$drawing2->setOffsetX2($drawing->getImageWidth());
$drawing2->setOffsetY2($drawing->getImageHeight());
$drawing2->setWorksheet($spreadsheet->getActiveSheet());

$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth($drawing->getImageWidth(), Dimension::UOM_PIXELS);
$spreadsheet->getActiveSheet()->getRowDimension(2)->setRowHeight($drawing->getImageHeight(), Dimension::UOM_PIXELS);

// Add a drawing to the worksheet one cell anchor
$helper->log('Add a drawing to the worksheet one-cell anchor');
$drawing3 = new Drawing();
$drawing3->setName('PhpSpreadsheet');
$drawing3->setDescription('PhpSpreadsheet');
$drawing3->setPath(__DIR__ . '/../images/PhpSpreadsheet_logo.png');
// anchor type will be one-cell because Coordinates2 is not set
//$drawing->setAnchorType(Drawing::ANCHORTYPE_ONECELL);
$drawing3->setCoordinates('D3');
$drawing3->setWorksheet($spreadsheet->getActiveSheet());

// Add a drawing to the worksheet
$helper->log('Add a drawing to the worksheet two-cell anchor resized absolute');
$drawing4 = new Drawing();
$drawing4->setName('PhpSpreadsheet');
$drawing4->setDescription('PhpSpreadsheet');
$drawing4->setPath(__DIR__ . '/../images/PhpSpreadsheet_logo.png');
// anchor type will be two-cell because Coordinates2 is set
//$drawing->setAnchorType(Drawing::ANCHORTYPE_TWOCELL);
$drawing4->setCoordinates('C6');
$drawing4->setCoordinates2('C6');
$drawing4->setOffsetX2($drawing->getImageWidth());
$drawing4->setOffsetY2($drawing->getImageHeight());
$drawing4->setWorksheet($spreadsheet->getActiveSheet());
$drawing4->setEditAs(Drawing::EDIT_AS_ABSOLUTE);

//$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth($drawing->getImageWidth(), Dimension::UOM_PIXELS);
$spreadsheet->getActiveSheet()->getRowDimension(6)->setRowHeight($drawing->getImageHeight(), Dimension::UOM_PIXELS);

$helper->write($spreadsheet, __FILE__, ['Xlsx']);
