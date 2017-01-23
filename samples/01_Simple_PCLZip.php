<?php

require __DIR__ . '/Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Maarten Balliauw')
        ->setLastModifiedBy('Maarten Balliauw')
        ->setTitle('PhpSpreadsheet Test Document')
        ->setSubject('PhpSpreadsheet Test Document')
        ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
        ->setKeywords('office PhpSpreadsheet php')
        ->setCategory('Test result file');

// Add some data
$helper->log('Add some data');
$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Hello')
        ->setCellValue('B2', 'world!')
        ->setCellValue('C1', 'Hello')
        ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
$spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A4', 'Miscellaneous glyphs')
        ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

$spreadsheet->getActiveSheet()->setCellValue('A8', "Hello\nWorld");
$spreadsheet->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
$spreadsheet->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Save Excel 2007 file
$helper->log('Write to Xlsx format');

// Use PCLZip rather than ZipArchive to create the Xlsx OfficeOpenXML file
\PhpOffice\PhpSpreadsheet\Settings::setZipClass(\PhpOffice\PhpSpreadsheet\Settings::PCLZIP);

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
