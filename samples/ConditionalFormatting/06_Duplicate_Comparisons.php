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
    ->setCellValue('A1', 'Duplicates Comparison');

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
    [10, 34, 3],
    [11, 55, 5],
];

$spreadsheet->getActiveSheet()
    ->fromArray($dataArray, null, 'A2', true);

// Set title row bold
$helper->log('Set title row bold');
$spreadsheet->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);

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

// Set conditional formatting rules and styles
$helper->log('Define conditional formatting and set styles');

// Set rules for Duplicates Comparison
$cellRange = 'A2:C12';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\Duplicates $duplicatesWizard */
$duplicatesWizard = $wizardFactory->newRule(Wizard::DUPLICATES);

$duplicatesWizard->setStyle($yellowStyle);
$conditionalStyles[] = $duplicatesWizard->getConditional();

$duplicatesWizard->unique()
    ->setStyle($greenStyle);
$conditionalStyles[] = $duplicatesWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($duplicatesWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Save
$helper->write($spreadsheet, __FILE__);
