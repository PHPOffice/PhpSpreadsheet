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
            \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00
        );

// Add conditional formatting
$helper->log('Add conditional formatting');
$conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
$conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS)
        ->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN)
        ->addCondition('0');
$conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

$conditional3 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
$conditional3->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS)
        ->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHANOREQUAL)
        ->addCondition('1');
$conditional3->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN);

$conditionalStyles = $spreadsheet->getActiveSheet()->getStyle('A1')->getConditionalStyles();
array_push($conditionalStyles, $conditional1);
array_push($conditionalStyles, $conditional3);
$spreadsheet->getActiveSheet()->getStyle('A1')->setConditionalStyles($conditionalStyles);

//	duplicate the conditional styles across a range of cells
$helper->log('Duplicate the conditional formatting across a range of cells');
$spreadsheet->getActiveSheet()->duplicateConditionalStyle(
    $spreadsheet->getActiveSheet()->getStyle('A1')->getConditionalStyles(),
    'A2:A8'
);

// Save
$helper->write($spreadsheet, __FILE__);
