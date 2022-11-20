<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/../../Header.php';

$helper->log('Searches for a value in the top row of a table or an array of values,
                and then returns a value in the same column from a row you specify
                in the table or array.');

// Create new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();
$worksheet = $spreadsheet->getActiveSheet();

$data = [
    ['ID', 'First Name', 'Last Name', 'Salary'],
    [72, 'Emily', 'Smith', 64901],
    [66, 'James', 'Anderson', 70855],
    [14, 'Mia', 'Clark', 188657],
    [30, 'John', 'Lewis', 97566],
    [53, 'Jessica', 'Walker', 58339],
    [56, 'Mark', 'Reed', 125180],
    [79, 'Richard', 'Lopez', 91632],
];

$worksheet->fromArray($data, null, 'B2');

$lookupFields = [
    ['ID', 53, 66, 56],
    ['Name'],
    ['Salary'],
];

$worksheet->fromArray($lookupFields, null, 'G3');

$worksheet->getCell('H4')->setValue('=VLOOKUP(H3, B3:E9, 2, FALSE) & " " & VLOOKUP(H3, B3:E9, 3, FALSE)');
$worksheet->getCell('I4')->setValue('=VLOOKUP(I3, B3:E9, 2, FALSE) & " " & VLOOKUP(I3, B3:E9, 3, FALSE)');
$worksheet->getCell('J4')->setValue('=VLOOKUP(J3, B3:E9, 2, FALSE) & " " & VLOOKUP(J3, B3:E9, 3, FALSE)');
$worksheet->getCell('H5')->setValue('=VLOOKUP(H3, B3:E9, 4, FALSE)');
$worksheet->getCell('I5')->setValue('=VLOOKUP(I3, B3:E9, 4, FALSE)');
$worksheet->getCell('J5')->setValue('=VLOOKUP(J3, B3:E9, 4, FALSE)');

for ($column = 'H'; $column !== 'K'; ++$column) {
    for ($row = 4; $row <= 5; ++$row) {
        $cell = $worksheet->getCell("{$column}{$row}");
        $helper->log("{$column}{$row}: {$cell->getValue()} => {$cell->getCalculatedValue()}");
    }
}
