<?php

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../Header.php';

$category = 'Engineering';
$functionName = 'BESSELK';
$description = 'Returns the modified Bessel function, which is equivalent to the Bessel functions evaluated for purely imaginary arguments';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

for ($n = 0; $n <= 5; ++$n) {
    for ($x = 0; $x <= 5; $x = $x + 0.25) {
        Calculation::getInstance($spreadsheet)->flushInstance();
        $formula = "BESSELK({$x}, {$n})";
        $worksheet->setCellValue('A1', "=$formula");

        $helper->log("$formula = " . $worksheet->getCell('A1')->getCalculatedValue());
    }
}
