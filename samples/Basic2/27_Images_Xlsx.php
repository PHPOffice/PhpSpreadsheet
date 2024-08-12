<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

require __DIR__ . '/../Header.php';

// Read from Xlsx (.xls) template
$helper->log('Load Xlsx template file');
$reader = IOFactory::createReader('Xlsx');
// Note that Xlsx converts bmp to png, so it needs to be added
//   programmatically rather than in template.
// Also note Xls converts both bmp and gif to png.
$spreadsheet = $reader->load(__DIR__ . '/../templates/27template.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$drawing = new Drawing();
$drawing->setName('Test BMP');
$drawing->setPath(__DIR__ . '/../images/bmp.bmp');
$drawing->setCoordinates('G17');
$drawing->setWorksheet($sheet);

$sheet->getCell('G16')->setValue('BMP');
$sheet->getStyle('G16')->getFont()->setName('Arial Black')->setBold(true);

// Save
$helper->write($spreadsheet, __FILE__);
