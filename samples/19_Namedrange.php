<?php

require __DIR__ . '/Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

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
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Firstname:')
        ->setCellValue('A2', 'Lastname:')
        ->setCellValue('A3', 'Fullname:')
        ->setCellValue('B1', 'Maarten')
        ->setCellValue('B2', 'Balliauw')
        ->setCellValue('B3', '=B1 & " " & B2');

// Define named ranges
$helper->log('Define named ranges');
$spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('PersonName', $spreadsheet->getActiveSheet(), 'B1'));
$spreadsheet->addNamedRange(new \PhpOffice\PhpSpreadsheet\NamedRange('PersonLN', $spreadsheet->getActiveSheet(), 'B2'));

// Rename named ranges
$helper->log('Rename named ranges');
$spreadsheet->getNamedRange('PersonName')->setName('PersonFN');

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()->setTitle('Person');

// Create a new worksheet, after the default sheet
$helper->log('Create new Worksheet object');
$spreadsheet->createSheet();

// Add some data to the second sheet, resembling some different data types
$helper->log('Add some data');
$spreadsheet->setActiveSheetIndex(1);
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Firstname:')
        ->setCellValue('A2', 'Lastname:')
        ->setCellValue('A3', 'Fullname:')
        ->setCellValue('B1', '=PersonFN')
        ->setCellValue('B2', '=PersonLN')
        ->setCellValue('B3', '=PersonFN & " " & PersonLN');

// Resolve range
$helper->log('Resolve range');
$helper->log('Cell B1 {=PersonFN}: ' . $spreadsheet->getActiveSheet()->getCell('B1')->getCalculatedValue());
$helper->log('Cell B3 {=PersonFN & " " & PersonLN}: ' . $spreadsheet->getActiveSheet()->getCell('B3')->getCalculatedValue());
$helper->log('Cell Person!B1: ' . $spreadsheet->getActiveSheet()->getCell('Person!B1')->getCalculatedValue());

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()->setTitle('Person (cloned)');

// Save
$helper->write($spreadsheet, __FILE__);
