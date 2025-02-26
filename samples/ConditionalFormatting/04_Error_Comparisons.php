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
    ->setCellValue('A1', 'Error Comparison');

$dataArray = [
    [5, -2, '=A2/B2'],
    [5, -1, '=A3/B3'],
    [5, 0, '=A4/B4'],
    [5, 1, '=A5/B5'],
    [5, 2, '=A6/B6'],
];

$spreadsheet->getActiveSheet()
    ->fromArray($dataArray, null, 'A2', true);

// Set title row bold
$helper->log('Set title row bold');
$spreadsheet->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);

// Define some styles for our Conditionals
$helper->log('Define some styles for our Conditionals');
$greenStyle = new Style(false, true);
$greenStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB(Color::COLOR_GREEN);
$greenStyle->getFont()->setColor(new Color(Color::COLOR_DARKRED));
$redStyle = new Style(false, true);
$redStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB(Color::COLOR_RED);
$redStyle->getFont()->setColor(new Color(Color::COLOR_GREEN));

// Set conditional formatting rules and styles
$helper->log('Define conditional formatting and set styles');

// Set rules for Blank Comparison
$cellRange = 'C2:C6';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\Errors $errorsWizard */
$errorsWizard = $wizardFactory->newRule(Wizard::ERRORS);

$errorsWizard->setStyle($redStyle);
$conditionalStyles[] = $errorsWizard->getConditional();

$errorsWizard->notError()
    ->setStyle($greenStyle);
$conditionalStyles[] = $errorsWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($errorsWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Save
$helper->write($spreadsheet, __FILE__);
