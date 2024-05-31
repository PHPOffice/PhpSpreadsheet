<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Mark Baker')
    ->setLastModifiedBy('Mark Baker')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Test result file');

// Create the worksheet
$helper->log('Add data');
$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A1', 'Odd/Even Expression Comparison')
    ->setCellValue('A4', 'Note that these functions are not available for Xls files')
    ->setCellValue('A15', 'Sales Grid Expression Comparison')
    ->setCellValue('A25', 'Sales Grid Multiple Expression Comparison');

$dataArray = [
    [1, 0, 3],
    [2, 1, 1],
    [3, 1, 4],
    [4, 2, 1],
    [5, 3, 5],
    [6, 5, 9],
    [7, 8, 2],
    [8, 13, 6],
    [9, 21, 5],
    [10, 34, 4],
];

$salesGrid = [
    ['Name', 'Sales', 'Country', 'Quarter'],
    ['Smith', 16753, 'UK', 'Q3'],
    ['Johnson', 14808, 'USA', 'Q4'],
    ['Williams', 10644, 'UK', 'Q2'],
    ['Jones', 1390, 'USA', 'Q3'],
    ['Brown', 4865, 'USA', 'Q4'],
    ['Williams', 12438, 'UK', 'Q2'],
];

$spreadsheet->getActiveSheet()
    ->fromArray($dataArray, null, 'A2', true);
$spreadsheet->getActiveSheet()
    ->fromArray($salesGrid, null, 'A16', true);
$spreadsheet->getActiveSheet()
    ->fromArray($salesGrid, null, 'A26', true);

// Set title row bold
$helper->log('Set title row bold');
$spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A15:D16')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A25:D26')->getFont()->setBold(true);

// Define some styles for our Conditionals
$helper->log('Define some styles for our Conditionals');
$yellowStyle = new Style(false, true);
$yellowStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB(Color::COLOR_YELLOW);
$yellowStyle->getFont()->setColor(new Color(Color::COLOR_BLUE));
$greenStyle = new Style(false, true);
$greenStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB(Color::COLOR_GREEN);
$greenStyle->getFont()->setColor(new Color(Color::COLOR_DARKRED));

$greenStyleMoney = clone $greenStyle;
$greenStyleMoney->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_ACCOUNTING_USD);

// Set conditional formatting rules and styles
$helper->log('Define conditional formatting and set styles');

// Set rules for Odd/Even Expression Comparison
$cellRange = 'A2:C11';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\Expression $expressionWizard */
$expressionWizard = $wizardFactory->newRule(Wizard::EXPRESSION);

$expressionWizard->expression('ISODD(A1)')
    ->setStyle($greenStyle);
$conditionalStyles[] = $expressionWizard->getConditional();

$expressionWizard->expression('ISEVEN(A1)')
    ->setStyle($yellowStyle);
$conditionalStyles[] = $expressionWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($expressionWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Sales Grid Row match against Country Comparison
$cellRange = 'A17:D22';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\Expression $expressionWizard */
$expressionWizard = $wizardFactory->newRule(Wizard::EXPRESSION);

$expressionWizard->expression('$C1="USA"')
    ->setStyle($greenStyleMoney);
$conditionalStyles[] = $expressionWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($expressionWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Sales Grid Row match against Country and Quarter Comparison
$cellRange = 'A27:D32';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\Expression $expressionWizard */
$expressionWizard = $wizardFactory->newRule(Wizard::EXPRESSION);

$expressionWizard->expression('AND($C1="USA",$D1="Q4")')
    ->setStyle($greenStyleMoney);
$conditionalStyles[] = $expressionWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($expressionWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set conditional formatting rules and styles
$helper->log('Set some additional styling for money formats');

$spreadsheet->getActiveSheet()->getStyle('B17:B22')
    ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_ACCOUNTING_USD);
$spreadsheet->getActiveSheet()->getStyle('B27:B32')
    ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_ACCOUNTING_USD);
$spreadsheet->getActiveSheet()->getColumnDimension('B')
    ->setAutoSize(true);

// Save
$helper->write($spreadsheet, __FILE__);
