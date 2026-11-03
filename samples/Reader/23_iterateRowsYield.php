<?php

/**
 * Use rangeToArrayYieldRows() to efficiently iterate over all rows.
 */

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

require __DIR__ . '/../Header.php';

$inputFileName = __DIR__ . '/../Reader/sampleData/example1.xls';

$spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load(
    $inputFileName,
    PhpOffice\PhpSpreadsheet\Reader\IReader::READ_DATA_ONLY
);
$sheet = $spreadsheet->getSheet(0);

$rowGenerator = $sheet->rangeToArrayYieldRows(
    $spreadsheet->getActiveSheet()->calculateWorksheetDataDimension(),
    null,
    false,
    false
);
foreach ($rowGenerator as $row) {
    echo '| ' . StringHelper::convertToString($row[0]) . ' | ' . StringHelper::convertToString($row[1]) . "|\n";
}
