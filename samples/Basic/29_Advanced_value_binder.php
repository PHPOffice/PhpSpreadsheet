<?php

use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

// Set timezone
$helper->log('Set timezone');
date_default_timezone_set('UTC');

// Set value binder
$helper->log('Set value binder');
Cell::setValueBinder(new AdvancedValueBinder());

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

// Set default font
$helper->log('Set default font');
$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

// Set column widths
$helper->log('Set column widths');
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14);

// Add some data, resembling some different data types
$helper->log('Add some data');
$spreadsheet->getActiveSheet()->setCellValue('A1', 'String value:')
        ->setCellValue('B1', 'Mark Baker');

$spreadsheet->getActiveSheet()->setCellValue('A2', 'Numeric value #1:')
        ->setCellValue('B2', 12345);

$spreadsheet->getActiveSheet()->setCellValue('A3', 'Numeric value #2:')
        ->setCellValue('B3', -12.345);

$spreadsheet->getActiveSheet()->setCellValue('A4', 'Numeric value #3:')
        ->setCellValue('B4', .12345);

$spreadsheet->getActiveSheet()->setCellValue('A5', 'Numeric value #4:')
        ->setCellValue('B5', '12345');

$spreadsheet->getActiveSheet()->setCellValue('A6', 'Numeric value #5:')
        ->setCellValue('B6', '1.2345');

$spreadsheet->getActiveSheet()->setCellValue('A7', 'Numeric value #6:')
        ->setCellValue('B7', '.12345');

$spreadsheet->getActiveSheet()->setCellValue('A8', 'Numeric value #7:')
        ->setCellValue('B8', '1.234e-5');

$spreadsheet->getActiveSheet()->setCellValue('A9', 'Numeric value #8:')
        ->setCellValue('B9', '-1.234e+5');

$spreadsheet->getActiveSheet()->setCellValue('A10', 'Boolean value:')
        ->setCellValue('B10', 'TRUE');

$spreadsheet->getActiveSheet()->setCellValue('A11', 'Percentage value #1:')
        ->setCellValue('B11', '10%');

$spreadsheet->getActiveSheet()->setCellValue('A12', 'Percentage value #2:')
        ->setCellValue('B12', '12.5%');

$spreadsheet->getActiveSheet()->setCellValue('A13', 'Fraction value #1:')
        ->setCellValue('B13', '-1/2');

$spreadsheet->getActiveSheet()->setCellValue('A14', 'Fraction value #2:')
        ->setCellValue('B14', '3 1/2');

$spreadsheet->getActiveSheet()->setCellValue('A15', 'Fraction value #3:')
        ->setCellValue('B15', '-12 3/4');

$spreadsheet->getActiveSheet()->setCellValue('A16', 'Fraction value #4:')
        ->setCellValue('B16', '13/4');

$spreadsheet->getActiveSheet()->setCellValue('A17', 'Currency value #1:')
        ->setCellValue('B17', '$12345');

$spreadsheet->getActiveSheet()->setCellValue('A18', 'Currency value #2:')
        ->setCellValue('B18', '$12345.67');

$spreadsheet->getActiveSheet()->setCellValue('A19', 'Currency value #3:')
        ->setCellValue('B19', '$12,345.67');

$spreadsheet->getActiveSheet()->setCellValue('A20', 'Date value #1:')
        ->setCellValue('B20', '21 December 1983');

$spreadsheet->getActiveSheet()->setCellValue('A21', 'Date value #2:')
        ->setCellValue('B21', '19-Dec-1960');

$spreadsheet->getActiveSheet()->setCellValue('A22', 'Date value #3:')
        ->setCellValue('B22', '07/12/1982');

$spreadsheet->getActiveSheet()->setCellValue('A23', 'Date value #4:')
        ->setCellValue('B23', '24-11-1950');

$spreadsheet->getActiveSheet()->setCellValue('A24', 'Date value #5:')
        ->setCellValue('B24', '17-Mar');

$spreadsheet->getActiveSheet()->setCellValue('A25', 'Time value #1:')
        ->setCellValue('B25', '01:30');

$spreadsheet->getActiveSheet()->setCellValue('A26', 'Time value #2:')
        ->setCellValue('B26', '01:30:15');

$spreadsheet->getActiveSheet()->setCellValue('A27', 'Date/Time value:')
        ->setCellValue('B27', '19-Dec-1960 01:30');

$spreadsheet->getActiveSheet()->setCellValue('A28', 'Formula:')
        ->setCellValue('B28', '=SUM(B2:B9)');

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()->setTitle('Advanced value binder');

// Save
$helper->write($spreadsheet, __FILE__);
