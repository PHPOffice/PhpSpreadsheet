<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Mark Baker')
        ->setLastModifiedBy('Mark Baker')
        ->setTitle('Office 2007 XLSX Test Document')
        ->setSubject('Office 2007 XLSX Test Document')
        ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
        ->setKeywords('office 2007 openxml php')
        ->setCategory('Test result file');

// Add some data
$helper->log('Add some data');
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Crouching');
$spreadsheet->getActiveSheet()->setCellValue('B1', 'Tiger');
$spreadsheet->getActiveSheet()->setCellValue('A2', 'Hidden');
$spreadsheet->getActiveSheet()->setCellValue('B2', 'Dragon');

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()->setTitle('Simple');

// Set document security
$helper->log('Set cell protection');

// Set sheet security
$helper->log('Set sheet security');
$spreadsheet->getActiveSheet()->getProtection()->setSheet(true);
$spreadsheet->getActiveSheet()
        ->getStyle('A2:B2')
        ->getProtection()->setLocked(
            Protection::PROTECTION_UNPROTECTED
        );

// Save
$helper->write($spreadsheet, __FILE__);
