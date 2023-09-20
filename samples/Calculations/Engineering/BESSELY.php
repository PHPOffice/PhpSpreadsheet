<?php

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$category = 'Engineering';
$functionName = 'BESSELY';
$description = 'Returns the Bessel function, which is also called the Weber function or the Neumann function';

$helper->titles($category, $functionName, $description);

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

for ($n = 0; $n <= 5; ++$n) {
    for ($x = 0; $x <= 5; $x = $x + 0.25) {
        Calculation::getInstance($spreadsheet)->flushInstance();
        $worksheet->setCellValue('A1', "=BESSELY({$x}, {$n})");

        $helper->log(sprintf(
            '%s = %f',
            $worksheet->getCell('A1')->getValue(),
            $worksheet->getCell('A1')->getCalculatedValue()
        ));
    }
}
