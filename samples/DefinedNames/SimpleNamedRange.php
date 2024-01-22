<?php

use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require_once __DIR__ . '/../Header.php';

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->setActiveSheetIndex(0);

// Set up some basic data
$worksheet
    ->setCellValue('A1', 'Tax Rate:')
    ->setCellValue('B1', '=19%')
    ->setCellValue('A3', 'Net Price:')
    ->setCellValue('B3', 12.99)
    ->setCellValue('A4', 'Tax:')
    ->setCellValue('A5', 'Price including Tax:');

// Define named ranges
$spreadsheet->addNamedRange(new NamedRange('TAX_RATE', $worksheet, '=$B$1'));
$spreadsheet->addNamedRange(new NamedRange('PRICE', $worksheet, '=$B$3'));

// Reference that defined name in a formula
$worksheet
    ->setCellValue('B4', '=PRICE*TAX_RATE')
    ->setCellValue('B5', '=PRICE*(1+TAX_RATE)');

/** @var float */
$calc1 = $worksheet->getCell('B1')->getCalculatedValue();
/** @var float */
$value = $worksheet->getCell('B3')->getValue();
/** @var float */
$calc2 = $worksheet->getCell('B4')->getCalculatedValue();
/** @var float */
$calc3 = $worksheet->getCell('B5')->getCalculatedValue();
$helper->log(sprintf(
    'With a Tax Rate of %.2f and a net price of %.2f, Tax is %.2f and the gross price is %.2f',
    $calc1,
    $value,
    $calc2,
    $calc3
));

$helper->write($spreadsheet, __FILE__, ['Xlsx']);
