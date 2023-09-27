<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalColorScale;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormatValueObject;

require __DIR__ . '/../Header.php';

// Create new Spreadsheet object
$helper->log('Create new Spreadsheet object');
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$helper->log('Set document properties');
$spreadsheet->getProperties()->setCreator('Owen Leibman')
    ->setLastModifiedBy('Owen Leibman')
    ->setTitle('PhpSpreadsheet Test Document')
    ->setSubject('PhpSpreadsheet Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Test result file');

// Create the worksheet
$helper->log('Add data');
$sheet
    ->setCellValue('A1', 1)
    ->setCellValue('A2', 2)
    ->setCellValue('A3', 8)
    ->setCellValue('A4', 4)
    ->setCellValue('A5', 5)
    ->setCellValue('A6', 6)
    ->setCellValue('A7', 7)
    ->setCellValue('A8', 3)
    ->setCellValue('A9', 9)
    ->setCellValue('A10', 10);

// Set conditional formatting rules and styles
$helper->log('Define conditional formatting using Color Scales');

$cellRange = 'A1:A10';
$condition1 = new Conditional();
$condition1->setConditionType(Conditional::CONDITION_COLORSCALE);
$colorScale = new ConditionalColorScale();
$condition1->setColorScale($colorScale);
$colorScale
    ->setMinimumConditionalFormatValueObject(new ConditionalFormatValueObject('min'))
    ->setMidpointConditionalFormatValueObject(new ConditionalFormatValueObject('percentile', '40'))
    ->setMaximumConditionalFormatValueObject(new ConditionalFormatValueObject('max'))
    ->setMinimumColor(new Color('FFF8696B'))
    ->setMidpointColor(new Color('FFFFEB84'))
    ->setMaximumColor(new Color('FF63BE7B'));

$conditionalStyles = [$condition1];

$sheet
    ->getStyle($cellRange)
    ->setConditionalStyles($conditionalStyles);
$sheet->setSelectedCells('B1');

// Save
$helper->write($spreadsheet, __FILE__, ['Xlsx']);
