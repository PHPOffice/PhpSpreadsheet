<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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

// Create a first sheet, representing sales data
$helper->log('Add some data');
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
        ->setCellValue('A1', '-0.5')
        ->setCellValue('A2', '-0.25')
        ->setCellValue('A3', '0.0')
        ->setCellValue('A4', '0.25')
        ->setCellValue('A5', '0.5')
        ->setCellValue('A6', '0.75')
        ->setCellValue('A7', '1.0')
        ->setCellValue('A8', '1.25');

$spreadsheet->getActiveSheet()->getStyle('A1:A8')
        ->getNumberFormat()
        ->setFormatCode(
            NumberFormat::FORMAT_PERCENTAGE_00
        );

// Add conditional formatting
$helper->log('Add conditional formatting');
$conditional1 = new Conditional();
$conditional1->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_LESSTHAN)
        ->addCondition('0');
$conditional1->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);

$conditional3 = new Conditional();
$conditional3->setConditionType(Conditional::CONDITION_CELLIS)
        ->setOperatorType(Conditional::OPERATOR_GREATERTHANOREQUAL)
        ->addCondition('1');
$conditional3->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_GREEN);

$conditionalStyles = $spreadsheet->getActiveSheet()->getStyle('A1')->getConditionalStyles();
$conditionalStyles[] = $conditional1;
$conditionalStyles[] = $conditional3;
$spreadsheet->getActiveSheet()->getStyle('A1')->setConditionalStyles($conditionalStyles);

//	duplicate the conditional styles across a range of cells
$helper->log('Duplicate the conditional formatting across a range of cells');
$spreadsheet->getActiveSheet()->duplicateConditionalStyle(
    $spreadsheet->getActiveSheet()->getStyle('A1')->getConditionalStyles(),
    'A2:A8'
);

// Save
$helper->write($spreadsheet, __FILE__);
