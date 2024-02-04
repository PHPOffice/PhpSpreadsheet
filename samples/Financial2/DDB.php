<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$helper->log('Returns the depreciation of an asset, using the Double Declining Balance Method,');
$helper->log('for each period of the asset\'s lifetime.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

// Add some data
$arguments = [
    ['Cost Value', 10000],
    ['Salvage', 1000],
    ['Life', 5, 'Years'],
];

// Some basic formatting for the data
$worksheet->fromArray($arguments, null, 'A1');
$worksheet->getStyle('B1:B2')->getNumberFormat()->setFormatCode('$#,##0.00');

// Now the formula
$baseRow = 5;
for ($year = 1; $year <= 5; ++$year) {
    $row = (string) ($baseRow + $year);

    $worksheet->setCellValue("A{$row}", "Depreciation after Yr {$year}");
    $worksheet->setCellValue("B{$row}", "=DDB(\$B\$1, \$B\$2, \$B\$3, {$year})");
    $worksheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('$#,##0.00;-$#,##0.00');

    $helper->log($worksheet->getCell("B{$row}")->getValue());
    $helper->log("DDB() Year {$year} Result is " . $worksheet->getCell("B{$row}")->getFormattedValue());
}
