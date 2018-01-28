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
    ->setTitle('Office 2007 XLSX Test Document')
    ->setSubject('Office 2007 XLSX Test Document')
    ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
    ->setKeywords('office 2007 openxml php')
    ->setCategory('Test result file');

// Add some data
$helper->log('Add some data');
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Hello');
$spreadsheet->getActiveSheet()->setCellValue('B2', 'world!');
$spreadsheet->getActiveSheet()->setCellValue('C1', 'Hello');
$spreadsheet->getActiveSheet()->setCellValue('D2', 'world!');

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set document security
$helper->log('Set document security');
$spreadsheet->getSecurity()->setLockWindows(true);
$spreadsheet->getSecurity()->setLockStructure(true);
$spreadsheet->getSecurity()->setWorkbookPassword('PhpSpreadsheet');

// Set sheet security
$helper->log('Set sheet security');
$spreadsheet->getActiveSheet()->getProtection()->setPassword('PhpSpreadsheet');
$spreadsheet->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
$spreadsheet->getActiveSheet()->getProtection()->setSort(true);
$spreadsheet->getActiveSheet()->getProtection()->setInsertRows(true);
$spreadsheet->getActiveSheet()->getProtection()->setFormatCells(true);

// Save
$helper->write($spreadsheet, __FILE__);
