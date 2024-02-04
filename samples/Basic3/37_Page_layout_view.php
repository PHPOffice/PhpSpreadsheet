<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('PHPOffice')
    ->setLastModifiedBy('PHPOffice')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('Office PhpSpreadsheet php')
    ->setCategory('Test result file');

// Add some data
$helper->log('Add some data');
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Hello')
    ->setCellValue('B2', 'world!');

// Set the page layout view as page layout
$spreadsheet->getActiveSheet()->getSheetView()->setView(SheetView::SHEETVIEW_PAGE_LAYOUT);

// Save
$helper->write($spreadsheet, __FILE__);
