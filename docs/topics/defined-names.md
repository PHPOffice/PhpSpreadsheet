# Defined Names

There are two types of Defined Names in MS Excel and other Spreadsheet formats: Named Ranges and Named Formulae. Between them, they can add a lot of power to your Spreadsheets, but they need to be used correctly.

## Named Ranges

A Named Range provides a name reference to a cell or a range of cells. You can then reference that cell or cells by that name within a formula.

As an example, I'll create a simple Calculator that adds Tax to a Price.

```php
// Set up some basic data
$worksheet
    ->setCellValue('A1', 'Tax Rate:')
    ->setCellValue('B1', '=19%')
    ->setCellValue('A3', 'Net Price:')
    ->setCellValue('B3', 12.99)
    ->setCellValue('A4', 'Tax:')
    ->setCellValue('A5', 'Price including Tax:');

// Define named ranges
$spreadsheet->addNamedRange( new \PhpOffice\PhpSpreadsheet\NamedRange('TAX_RATE', $worksheet, '=$B$1'));
$spreadsheet->addNamedRange( new \PhpOffice\PhpSpreadsheet\NamedRange('PRICE', $worksheet, '=$B$3'));

// Reference that defined name in a formula
$worksheet
    ->setCellValue('B4', '=PRICE*TAX_RATE')
    ->setCellValue('B5', '=PRICE*(1+TAX_RATE)');

echo sprintf(
    'With a Tax Rate of %.2f and a net price of %.2f, Tax is %.2f and the gross price is %.2f',
    $worksheet->getCell('B1')->getCalculatedValue(),
    $worksheet->getCell('B3')->getValue(),
    $worksheet->getCell('B4')->getCalculatedValue(),
    $worksheet->getCell('B5')->getCalculatedValue()
), PHP_EOL;
```

Using these Named Ranges (providing meaningful human-readable names for cells) makes the purpose of the formula immediately clear.

And, if the Tax Rate changes to 16%, then we only need to change the value in cell `B1` to the new Tax rate (`=16%`), or if we want to calculate the Tax Charges for a different net price, that will immediately be reflected in all the calculations that reference those Named Ranges. No matter whereabouts in the worksheet I used that Named Range, it always references the value in cell `B1`.

### Absolute Named Ranges

In the above example, when I define the Named Range values (e.g. `'=$B$1'`), I used a `$` before the row and the column. This made the Named Range an Absolute Reference.

```php
// Set up some basic data for a timesheet
$worksheet
    ->setCellValue('A1', 'Pay Rate/hour:')
    ->setCellValue('B1', '7.50')
    ->setCellValue('A3', 'Date')
    ->setCellValue('B3', 'Hours')
    ->setCellValue('C3', 'Charge');

// Define named range using an absolute cell reference
$spreadsheet->addNamedRange( new NamedRange('PAY_RATE', $worksheet, '=$B$1'));

$workHours = [
    '2020-0-06' => 7.5,
    '2020-0-07' => 7.25,
    '2020-0-08' => 6.5,
    '2020-0-09' => 7.0,
    '2020-0-10' => 5.5,
];

//Populate the Timesheet
$startRow = 4;
$row = $startRow;
foreach ($workHours as $date => $hours) {
    $worksheet
        ->setCellValue("A{$row}", $date)
        ->setCellValue("B{$row}", $hours)
        ->setCellValue("C{$row}", "=B{$row}*PAY_RATE");
    $row++;
}
$endRow = $row - 1;

$worksheet
    ->setCellValue("B{$row}", "=SUM(B{$startRow}:B{$endRow})")
    ->setCellValue("C{$row}", "=SUM(C{$startRow}:C{$endRow})");


echo sprintf(
    'Worked %.2f hours at a rate of %.2f - Charge to the client is %.2f',
    $worksheet->getCell("B{$row}")->getCalculatedValue(),
    $worksheet->getCell('B1')->getValue(),
    $worksheet->getCell("C{$row}")->getCalculatedValue()
), PHP_EOL;
```

### Relative Named Ranges

### Named Range Scope

A Named Range can be locally scoped so that it is only available when referenced from a specific worksheet, or it can be globally scoped. This means that you can use the same Named Range name with different values on different worksheets.

## Named Formulae

## Combining Named Ranges and Formulae

