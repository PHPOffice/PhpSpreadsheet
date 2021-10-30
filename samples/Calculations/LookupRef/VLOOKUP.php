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
    [72, 'Emily', 'Smith', 64901, null, 'ID', 53, 66, 56],
    [66, 'James', 'Anderson', 70855, null, 'Salary'],
    [14, 'Mia', 'Clark', 188657],
    [30, 'John', 'Lewis', 97566],
    [53, 'Jessica', 'Walker', 58339],
    [56, 'Mark', 'Reed', 125180],
    [79, 'Richard', 'Lopez', 91632],
];

$worksheet->fromArray($data, null, 'B2');

$worksheet->getCell('H4')->setValue('=VLOOKUP(H3, B3:E9, 4, FALSE)');
$worksheet->getCell('I4')->setValue('=VLOOKUP(I3, B3:E9, 4, FALSE)');
$worksheet->getCell('J4')->setValue('=VLOOKUP(J3, B3:E9, 4, FALSE)');

for ($column = 'H'; $column !== 'K'; ++$column) {
    $cell = $worksheet->getCell("{$column}4");
    $helper->log("{$column}4: {$cell->getValue()} => {$cell->getCalculatedValue()}");
}
