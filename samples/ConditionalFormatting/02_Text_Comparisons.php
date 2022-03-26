<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
    ->setCellValue('A1', 'Value Begins With Literal')
    ->setCellValue('A7', 'Value Ends With Literal')
    ->setCellValue('A13', 'Value Contains Literal')
    ->setCellValue('A19', "Value Doesn't Contain Literal")
    ->setCellValue('E1', 'Value Begins With using Cell Reference')
    ->setCellValue('E7', 'Value Ends With using Cell Reference')
    ->setCellValue('E13', 'Value Contains using Cell Reference')
    ->setCellValue('E19', "Value Doesn't Contain using Cell Reference")
    ->setCellValue('A25', 'Simple Comparison using Concatenation Formula');

$dataArray = [
    ['HELLO', 'WORLD'],
    ['MELLOW', 'YELLOW'],
    ['SLEEPY', 'HOLLOW'],
];

$spreadsheet->getActiveSheet()
    ->fromArray($dataArray, null, 'A2', true)
    ->fromArray($dataArray, null, 'A8', true)
    ->fromArray($dataArray, null, 'A14', true)
    ->fromArray($dataArray, null, 'A20', true)
    ->fromArray($dataArray, null, 'E2', true)
    ->fromArray($dataArray, null, 'E8', true)
    ->fromArray($dataArray, null, 'E14', true)
    ->fromArray($dataArray, null, 'E20', true)
    ->fromArray($dataArray, null, 'A26', true)
    ->setCellValue('D1', 'H')
    ->setCellValue('D7', 'OW')
    ->setCellValue('D13', 'LL')
    ->setCellValue('D19', 'EL')
    ->setCellValue('C26', 'HELLO WORLD')
    ->setCellValue('C27', 'SOYLENT GREEN')
    ->setCellValue('C28', 'SLEEPY HOLLOW');

// Set title row bold
$helper->log('Set title row bold');
$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A7:G7')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A13:G13')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A19:G19')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A25:C25')->getFont()->setBold(true);

// Define some styles for our Conditionals
$helper->log('Define some styles for our Conditionals');
$yellowStyle = new Style(false, true);
$yellowStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getEndColor()->setARGB(Color::COLOR_YELLOW);
$yellowStyle->getFont()->setColor(new Color(Color::COLOR_BLUE));
$greenStyle = new Style(false, true);
$greenStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getEndColor()->setARGB(Color::COLOR_GREEN);
$greenStyle->getFont()->setColor(new Color(Color::COLOR_DARKRED));
$redStyle = new Style(false, true);
$redStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getEndColor()->setARGB(Color::COLOR_RED);
$redStyle->getFont()->setColor(new Color(Color::COLOR_GREEN));

// Set conditional formatting rules and styles
$helper->log('Define conditional formatting and set styles');

// Set rules for Literal Value Begins With
$cellRange = 'A2:B4';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\TextValue $textWizard */
$textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

$textWizard->beginsWith('H')
    ->setStyle($yellowStyle);
$conditionalStyles[] = $textWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($textWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Begins With using Cell Reference
$cellRange = 'E2:F4';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\TextValue $textWizard */
$textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

$textWizard->beginsWith('$D$1', Wizard::VALUE_TYPE_CELL)
    ->setStyle($yellowStyle);
$conditionalStyles[] = $textWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($textWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Literal Value Ends With
$cellRange = 'A8:B10';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\TextValue $textWizard */
$textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

$textWizard->endsWith('OW')
    ->setStyle($yellowStyle);
$conditionalStyles[] = $textWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($textWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Ends With using Cell Reference
$cellRange = 'E8:F10';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\TextValue $textWizard */
$textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

$textWizard->endsWith('$D$7', Wizard::VALUE_TYPE_CELL)
    ->setStyle($yellowStyle);
$conditionalStyles[] = $textWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($textWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Literal Value Contains
$cellRange = 'A14:B16';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\TextValue $textWizard */
$textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

$textWizard->contains('LL')
    ->setStyle($greenStyle);
$conditionalStyles[] = $textWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($textWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Contains using Cell Reference
$cellRange = 'E14:F16';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\TextValue $textWizard */
$textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

$textWizard->contains('$D$13', Wizard::VALUE_TYPE_CELL)
    ->setStyle($greenStyle);
$conditionalStyles[] = $textWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($textWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Literal Value Does Not Contain
$cellRange = 'A20:B22';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\TextValue $textWizard */
$textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

$textWizard->doesNotContain('EL')
    ->setStyle($redStyle);
$conditionalStyles[] = $textWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($textWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Contains using Cell Reference
$cellRange = 'E20:F22';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\TextValue $textWizard */
$textWizard = $wizardFactory->newRule(Wizard::TEXT_VALUE);

$textWizard->doesNotContain('$D$19', Wizard::VALUE_TYPE_CELL)
    ->setStyle($redStyle);
$conditionalStyles[] = $textWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($textWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Simple Comparison using Concatenation Formula
$cellRange = 'C26:C28';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\CellValue $cellWizard */
$cellWizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$cellWizard->equals('CONCATENATE($A1," ",$B1)', Wizard::VALUE_TYPE_FORMULA)
    ->setStyle($yellowStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($cellWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

// Save
$helper->write($spreadsheet, __FILE__);
