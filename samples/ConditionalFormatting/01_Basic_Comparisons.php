<?php

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
    ->setCellValue('A1', 'Literal Value Comparison')
    ->setCellValue('A9', 'Value Comparison with Absolute Cell Reference $H$9')
    ->setCellValue('A17', 'Value Comparison with Relative Cell References')
    ->setCellValue('A23', 'Value Comparison with Formula based on AVERAGE() ± STDEV()')
    ->setCellValue('A30', 'Literal String Value Comparison');

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

$stringArray = [
    ['I'],
    ['Love'],
    ['PHP'],
];

$spreadsheet->getActiveSheet()
    ->fromArray($dataArray, null, 'A2', true)
    ->fromArray($dataArray, null, 'A10', true)
    ->fromArray($betweenDataArray, null, 'A18', true)
    ->fromArray($dataArray, null, 'A24', true)
    ->fromArray($stringArray, null, 'A31', true)
    ->setCellValue('H9', 1);

// Set title row bold
$helper->log('Set title row bold');
$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A9:E9')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A17:E17')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A23:E23')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('A30:E30')->getFont()->setBold(true);

// Define some styles for our Conditionals
$helper->log('Define some styles for our Conditionals');
$yellowStyle = new Style(false, true);
$yellowStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB(Color::COLOR_YELLOW);
$yellowStyle->getFill()
    ->getEndColor()->setARGB(Color::COLOR_YELLOW);
$yellowStyle->getFont()->setColor(new Color(Color::COLOR_BLUE));
$greenStyle = new Style(false, true);
$greenStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB(Color::COLOR_GREEN);
$greenStyle->getFill()
    ->getEndColor()->setARGB(Color::COLOR_GREEN);
$greenStyle->getFont()->setColor(new Color(Color::COLOR_DARKRED));
$redStyle = new Style(false, true);
$redStyle->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB(Color::COLOR_RED);
$redStyle->getFill()
    ->getEndColor()->setARGB(Color::COLOR_RED);
$redStyle->getFont()->setColor(new Color(Color::COLOR_GREEN));

// Set conditional formatting rules and styles
$helper->log('Define conditional formatting and set styles');

// Set rules for Literal Value Comparison
$cellRange = 'A2:E5';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\CellValue $cellWizard */
$cellWizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$cellWizard->equals(0)
    ->setStyle($yellowStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$cellWizard->greaterThan(0)
    ->setStyle($greenStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$cellWizard->lessThan(0)
    ->setStyle($redStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($cellWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Comparison with Absolute Cell Reference $H$9
$cellRange = 'A10:E13';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\CellValue $cellWizard */
$cellWizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$cellWizard->equals('$H$9', Wizard::VALUE_TYPE_CELL)
    ->setStyle($yellowStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$cellWizard->greaterThan('$H$9', Wizard::VALUE_TYPE_CELL)
    ->setStyle($greenStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$cellWizard->lessThan('$H$9', Wizard::VALUE_TYPE_CELL)
    ->setStyle($redStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($cellWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Comparison with Relative Cell References
$cellRange = 'A18:A20';
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\CellValue $cellWizard */
$cellWizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$cellWizard->between('$B1', Wizard::VALUE_TYPE_CELL)
    ->and('$C1', Wizard::VALUE_TYPE_CELL)
    ->setStyle($greenStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($cellWizard->getCellRange())
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
/** @var Wizard\CellValue $cellWizard */
$cellWizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$cellWizard->between('AVERAGE(' . $formulaRange . ')-STDEV(' . $formulaRange . ')', Wizard::VALUE_TYPE_FORMULA)
    ->and('AVERAGE(' . $formulaRange . ')+STDEV(' . $formulaRange . ')', Wizard::VALUE_TYPE_FORMULA)
    ->setStyle($yellowStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$cellWizard->greaterThan('AVERAGE(' . $formulaRange . ')+STDEV(' . $formulaRange . ')', Wizard::VALUE_TYPE_FORMULA)
    ->setStyle($greenStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$cellWizard->lessThan('AVERAGE(' . $formulaRange . ')-STDEV(' . $formulaRange . ')', Wizard::VALUE_TYPE_FORMULA)
    ->setStyle($redStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($cellWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Set rules for Value Comparison with String Literal
$cellRange = 'A31:A33';
$formulaRange = implode(
    ':',
    array_map(
        [Coordinate::class, 'absoluteCoordinate'],
        Coordinate::splitRange($cellRange)[0]
    )
);
$conditionalStyles = [];
$wizardFactory = new Wizard($cellRange);
/** @var Wizard\CellValue $cellWizard */
$cellWizard = $wizardFactory->newRule(Wizard::CELL_VALUE);

$cellWizard->equals('LOVE')
    ->setStyle($redStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$cellWizard->equals('PHP')
    ->setStyle($greenStyle);
$conditionalStyles[] = $cellWizard->getConditional();

$spreadsheet->getActiveSheet()
    ->getStyle($cellWizard->getCellRange())
    ->setConditionalStyles($conditionalStyles);

// Save
$helper->write($spreadsheet, __FILE__);
