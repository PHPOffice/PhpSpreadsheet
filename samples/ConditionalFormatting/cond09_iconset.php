<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormatValueObject;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalIconSet;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\IconSetValues;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('issakujitsuk')
    ->setLastModifiedBy('issakujitsuk')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Test result file');

// Create the worksheet
$helper->log('Add data');
foreach (['A', 'B', 'C'] as $columnIndex) {
    $sheet
        ->setCellValue("{$columnIndex}1", 1)
        ->setCellValue("{$columnIndex}2", 2)
        ->setCellValue("{$columnIndex}3", 8)
        ->setCellValue("{$columnIndex}4", 4)
        ->setCellValue("{$columnIndex}5", 5)
        ->setCellValue("{$columnIndex}6", 6)
        ->setCellValue("{$columnIndex}7", 7)
        ->setCellValue("{$columnIndex}8", 3)
        ->setCellValue("{$columnIndex}9", 9)
        ->setCellValue("{$columnIndex}10", 10);
}

// Set conditional formatting rules and styles
$helper->log('Define conditional formatting using Icon Set');

// 3 icons
$sheet->getStyle('A1:A10')
    ->setConditionalStyles([
        makeConditionalIconSet(
            IconSetValues::ThreeSymbols,
            [
                new ConditionalFormatValueObject('percent', 0),
                new ConditionalFormatValueObject('percent', 33),
                new ConditionalFormatValueObject('percent', 67),
            ]
        ),
    ]);

// 4 icons
$sheet->getStyle('B1:B10')
    ->setConditionalStyles([
        makeConditionalIconSet(
            IconSetValues::FourArrows,
            [
                new ConditionalFormatValueObject('percent', 0),
                new ConditionalFormatValueObject('percent', 25),
                new ConditionalFormatValueObject('percent', 50),
                new ConditionalFormatValueObject('percent', 75),
            ]
        ),
    ]);

// 5 icons
$sheet->getStyle('C1:C10')
    ->setConditionalStyles([
        makeConditionalIconSet(
            IconSetValues::FiveQuarters,
            [
                new ConditionalFormatValueObject('percent', 0),
                new ConditionalFormatValueObject('percent', 20),
                new ConditionalFormatValueObject('percent', 40),
                new ConditionalFormatValueObject('percent', 60),
                new ConditionalFormatValueObject('percent', 80),
            ]
        ),
    ]);

// Save
$sheet->setSelectedCells('A1');
$helper->write($spreadsheet, __FILE__, ['Xlsx']);

/**
 * Helper function to create a Conditional object with an IconSet.
 *
 * @param IconSetValues $type The type of icon set
 * @param ConditionalFormatValueObject[] $cfvos The conditional format value objects
 */
function makeConditionalIconSet(
    IconSetValues $type,
    array $cfvos,
): Conditional {
    $condition = new Conditional();
    $condition->setConditionType(Conditional::CONDITION_ICONSET);
    $iconSet = new ConditionalIconSet();
    $condition->setIconSet($iconSet);
    $iconSet->setIconSetType($type)
        ->setCfvos($cfvos);

    return $condition;
}
