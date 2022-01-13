<?php

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
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
    ->setCellValue('A1', 'Literal Value Comparison')
    ->setCellValue('A9', 'Value Comparison with Absolute Cell Reference $H$9')
    ->setCellValue('A17', 'Value Comparison with Relative Cell References')
    ->setCellValue('A23', 'Value Comparison with Formula based on AVERAGE() ± STDEV()');

$dataArray = [
    [-2, -1, 0, 1, 2],
    [-1, 0, 1, 2, 3],
    [0, 1, 2, 3, 4],
    [1, 2, 3, 4, 5],
];

$betweenDataArray = [
    [2, 7, 6],
    [9, 5, 1],
    [4, 3, 8],
];

$spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A2', true);
$spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A10', true);
$spreadsheet->getActiveSheet()->fromArray($betweenDataArray, null, 'A18', true);
$spreadsheet->getActiveSheet()->fromArray($dataArray, null, 'A24', true);
$spreadsheet->getActiveSheet()->setCellValue('H9', 1);

// Set title row bold
$helper->log('Set title row bold');
$spreadsheet->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A9:D9')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A17:D17')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A23:D23')->getFont()->setBold(true);

// Define some styles
$helper->log('Define some styles');
$yellowStyle = new Style();
$yellowStyle->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getEndColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_YELLOW);
$greenStyle = new Style();
$greenStyle->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getEndColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_GREEN);
$redStyle = new Style();
$redStyle->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getEndColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

// Set conditional formatting rules and styles
$helper->log('Define conditional formatting and set styles');

// Set rules for Literal Value Comparison
$cellRange = 'A2:E5';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
$wizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$wizard->equals(0)
    ->setStyle($yellowStyle);
$conditionalStyles[] = $wizard->getConditional();

$wizard->greaterThan(0)
    ->setStyle($greenStyle);
$conditionalStyles[] = $wizard->getConditional();

$wizard->lessThan(0)
    ->setStyle($redStyle);
$conditionalStyles[] = $wizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($wizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Comparison with Absolute Cell Reference $H$9
$cellRange = 'A10:E13';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
$wizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$wizard->equals('$H$9', Wizard::VALUE_TYPE_CELL)
    ->setStyle($yellowStyle);
$conditionalStyles[] = $wizard->getConditional();

$wizard->greaterThan('$H$9', Wizard::VALUE_TYPE_CELL)
    ->setStyle($greenStyle);
$conditionalStyles[] = $wizard->getConditional();

$wizard->lessThan('$H$9', Wizard::VALUE_TYPE_CELL)
    ->setStyle($redStyle);
$conditionalStyles[] = $wizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($wizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Comparison with Relative Cell References
$cellRange = 'A18:A20';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
$wizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$wizard->between('$B1', Wizard::VALUE_TYPE_CELL)
    ->and('$C1', Wizard::VALUE_TYPE_CELL)
    ->setStyle($greenStyle);
$conditionalStyles[] = $wizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($wizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Comparison with Formula
$cellRange = 'A24:E27';
$formulaRange = implode(
    ':',
    array_map(
        [Coordinate::class, 'absoluteCoordinate'],
        Coordinate::splitRange($cellRange)[0]
    )
);
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
$wizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$wizard->between('AVERAGE(' . $formulaRange . ')-STDEV(' . $formulaRange . ')', Wizard::VALUE_TYPE_FORMULA)
    ->and('AVERAGE(' . $formulaRange . ')+STDEV(' . $formulaRange . ')', Wizard::VALUE_TYPE_FORMULA)
    ->setStyle($yellowStyle);
$conditionalStyles[] = $wizard->getConditional();

$wizard->greaterThan('AVERAGE(' . $formulaRange . ')+STDEV(' . $formulaRange . ')', Wizard::VALUE_TYPE_FORMULA)
    ->setStyle($greenStyle);
$conditionalStyles[] = $wizard->getConditional();

$wizard->lessThan('AVERAGE(' . $formulaRange . ')-STDEV(' . $formulaRange . ')', Wizard::VALUE_TYPE_FORMULA)
    ->setStyle($redStyle);
$conditionalStyles[] = $wizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($wizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Save
$helper->write($spreadsheet, __FILE__);
