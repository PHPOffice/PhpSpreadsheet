<?php

/**
 * Use rangeToArrayYieldRows() to efficiently iterate over all rows.
 */

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
    echo '| ' . $row[0] . ' | ' . $row[1] . "|\n";
}
