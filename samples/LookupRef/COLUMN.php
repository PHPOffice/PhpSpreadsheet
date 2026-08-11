<?php

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';
/** @var PhpOffice\PhpSpreadsheet\Helper\Sample $helper */
$helper->log('Returns the column index of a cell.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$calculation = Calculation::getInstance($spreadsheet);
$calculation->setInstanceArrayReturnType(
    Calculation::RETURN_ARRAY_AS_VALUE
);
$worksheet = $spreadsheet->getActiveSheet();

$worksheet->getCell('A1')->setValue('=COLUMN(C13)');
$worksheet->getCell('A2')->setValue('=COLUMN(E13:G15)');
$worksheet->getCell('F1')->setValue('=COLUMN()');

for ($row = 1; $row <= 2; ++$row) {
    $cell = $worksheet->getCell("A{$row}");
    $helper->log("A{$row}: " . $cell->getValueString() . ' => ' . $cell->getCalculatedValueString());
}

$cell = $worksheet->getCell('F1');
$helper->log('F1: ' . $cell->getValueString() . ' => ' . $cell->getCalculatedValueString());
