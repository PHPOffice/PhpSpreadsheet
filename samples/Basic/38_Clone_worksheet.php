<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

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

// Clone worksheet
$helper->log('Clone worksheet');
$clonedSheet = clone $spreadsheet->getActiveSheet();
$clonedSheet
        ->setCellValue('A1', 'Goodbye')
        ->setCellValue('A2', 'cruel')
        ->setCellValue('C1', 'Goodbye')
        ->setCellValue('C2', 'cruel');

// Rename cloned worksheet
$helper->log('Rename cloned worksheet');
$clonedSheet->setTitle('Simple Clone');
$spreadsheet->addSheet($clonedSheet);

// Save
$helper->write($spreadsheet, __FILE__);
