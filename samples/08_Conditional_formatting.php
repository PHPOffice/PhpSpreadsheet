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
$spreadsheet->getActiveSheet()->setCellValue('A1', 'Description')
        ->setCellValue('B1', 'Amount');

$spreadsheet->getActiveSheet()->setCellValue('A2', 'Paycheck received')
        ->setCellValue('B2', 100);

$spreadsheet->getActiveSheet()->setCellValue('A3', 'Cup of coffee bought')
        ->setCellValue('B3', -1.5);

$spreadsheet->getActiveSheet()->setCellValue('A4', 'Cup of coffee bought')
        ->setCellValue('B4', -1.5);

$spreadsheet->getActiveSheet()->setCellValue('A5', 'Cup of tea bought')
        ->setCellValue('B5', -1.2);

$spreadsheet->getActiveSheet()->setCellValue('A6', 'Found some money')
        ->setCellValue('B6', 8);

$spreadsheet->getActiveSheet()->setCellValue('A7', 'Total:')
        ->setCellValue('B7', '=SUM(B2:B6)');

// Set column widths
$helper->log('Set column widths');
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);

// Add conditional formatting
$helper->log('Add conditional formatting');
$conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
$conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS)
        ->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_BETWEEN)
        ->addCondition('200')
        ->addCondition('400');
$conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW);
$conditional1->getStyle()->getFont()->setBold(true);
$conditional1->getStyle()->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

$conditional2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
$conditional2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS)
        ->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN)
        ->addCondition('0');
$conditional2->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
$conditional2->getStyle()->getFont()->setItalic(true);
$conditional2->getStyle()->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

$conditional3 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
$conditional3->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS)
        ->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHANOREQUAL)
        ->addCondition('0');
$conditional3->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN);
$conditional3->getStyle()->getFont()->setItalic(true);
$conditional3->getStyle()->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

$conditionalStyles = $spreadsheet->getActiveSheet()->getStyle('B2')->getConditionalStyles();
array_push($conditionalStyles, $conditional1);
array_push($conditionalStyles, $conditional2);
array_push($conditionalStyles, $conditional3);
$spreadsheet->getActiveSheet()->getStyle('B2')->setConditionalStyles($conditionalStyles);

//	duplicate the conditional styles across a range of cells
$helper->log('Duplicate the conditional formatting across a range of cells');
$spreadsheet->getActiveSheet()->duplicateConditionalStyle(
    $spreadsheet->getActiveSheet()->getStyle('B2')->getConditionalStyles(),
    'B3:B7'
);

// Set fonts
$helper->log('Set fonts');
$spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
//$spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A7:B7')->getFont()->setBold(true);
//$spreadsheet->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
// Set header and footer. When no different headers for odd/even are used, odd header is assumed.
$helper->log('Set header/footer');
$spreadsheet->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BPersonal cash register&RPrinted on &D');
$spreadsheet->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $spreadsheet->getProperties()->getTitle() . '&RPage &P of &N');

// Set page orientation and size
$helper->log('Set page orientation and size');
$spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

// Rename worksheet
$helper->log('Rename worksheet');
$spreadsheet->getActiveSheet()->setTitle('Invoice');

// Save
$helper->write($spreadsheet, __FILE__);
