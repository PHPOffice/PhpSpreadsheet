<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\NamedFormula;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('UTC');

// Adjust the path as required to reference the PHPSpreadsheet Bootstrap file
require_once __DIR__ . '/../Bootstrap.php';

$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->setActiveSheetIndex(0);

// Add some Named Formulae
// The first to store our tax rate
$spreadsheet->addNamedFormula(new NamedFormula('TAX_RATE', $worksheet, '=19%'));
// The second to calculate the Tax on a Price value (Note that `PRICE` is defined later as a Named Range)
$spreadsheet->addNamedFormula(new NamedFormula('TAX', $worksheet, '=PRICE*TAX_RATE'));

// Set up some basic data
$worksheet
    ->setCellValue('A1', 'Tax Rate:')
    ->setCellValue('B1', '=TAX_RATE')
    ->setCellValue('A3', 'Net Price:')
    ->setCellValue('B3', 19.99)
    ->setCellValue('A4', 'Tax:')
    ->setCellValue('A5', 'Price including Tax:');

// Define a named range that we can use in our formulae
$spreadsheet->addNamedRange(new NamedRange('PRICE', $worksheet, '=$B$3'));

// Reference the defined formulae in worksheet formulae
$worksheet
    ->setCellValue('B4', '=TAX')
    ->setCellValue('B5', '=PRICE+TAX');

echo sprintf(
    'With a Tax Rate of %.2f and a net price of %.2f, Tax is %.2f and the gross price is %.2f',
    $worksheet->getCell('B1')->getCalculatedValue(),
    $worksheet->getCell('B3')->getValue(),
    $worksheet->getCell('B4')->getCalculatedValue(),
    $worksheet->getCell('B5')->getCalculatedValue()
), PHP_EOL;

$outputFileName = 'SimpleNamedFormula.xlsx';
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save($outputFileName);
