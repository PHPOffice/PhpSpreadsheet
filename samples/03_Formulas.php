<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/Header.php';

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

// Add some data, we will use some formulas here
$helper->log('Add some data');
$spreadsheet->getActiveSheet()
        ->setCellValue('A5', 'Sum:');

$spreadsheet->getActiveSheet()->setCellValue('B1', 'Range #1')
        ->setCellValue('B2', 3)
        ->setCellValue('B3', 7)
        ->setCellValue('B4', 13)
        ->setCellValue('B5', '=SUM(B2:B4)');
$helper->log('Sum of Range #1 is ' . $spreadsheet->getActiveSheet()->getCell('B5')->getCalculatedValue());

$spreadsheet->getActiveSheet()->setCellValue('C1', 'Range #2')
        ->setCellValue('C2', 5)
        ->setCellValue('C3', 11)
        ->setCellValue('C4', 17)
        ->setCellValue('C5', '=SUM(C2:C4)');
$helper->log('Sum of Range #2 is ', $spreadsheet->getActiveSheet()->getCell('C5')->getCalculatedValue());

$spreadsheet->getActiveSheet()
        ->setCellValue('A7', 'Total of both ranges:');
$spreadsheet->getActiveSheet()
        ->setCellValue('B7', '=SUM(B5:C5)');
$helper->log('Sum of both Ranges is ', $spreadsheet->getActiveSheet()->getCell('B7')->getCalculatedValue());

$spreadsheet->getActiveSheet()
        ->setCellValue('A8', 'Minimum of both ranges:');
$spreadsheet->getActiveSheet()
        ->setCellValue('B8', '=MIN(B2:C4)');
$helper->log('Minimum value in either Range is ', $spreadsheet->getActiveSheet()->getCell('B8')->getCalculatedValue());

$spreadsheet->getActiveSheet()
        ->setCellValue('A9', 'Maximum of both ranges:');
$spreadsheet->getActiveSheet()
        ->setCellValue('B9', '=MAX(B2:C4)');
$helper->log('Maximum value in either Range is ', $spreadsheet->getActiveSheet()->getCell('B9')->getCalculatedValue());

$spreadsheet->getActiveSheet()
        ->setCellValue('A10', 'Average of both ranges:');
$spreadsheet->getActiveSheet()
        ->setCellValue('B10', '=AVERAGE(B2:C4)');
$helper->log('Average value of both Ranges is ', $spreadsheet->getActiveSheet()->getCell('B10')->getCalculatedValue());
$spreadsheet->getActiveSheet()
        ->getColumnDimension('A')
        ->setAutoSize(true);

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()
        ->setTitle('Formulas');

//
//  If we set Pre Calculated Formulas to true then PhpSpreadsheet will calculate all formulae in the
//    workbook before saving. This adds time and memory overhead, and can cause some problems with formulae
//    using functions or features (such as array formulae) that aren't yet supported by the calculation engine
//  If the value is false (the default) for the Xlsx Writer, then MS Excel (or the application used to
//    open the file) will need to recalculate values itself to guarantee that the correct results are available.
//
//$writer->setPreCalculateFormulas(true);
// Save
$helper->write($spreadsheet, __FILE__);
