# Defined Names

There are two types of Defined Names in MS Excel and other Spreadsheet formats: Named Ranges and Named Formulae. Between them, they can add a lot of power to your Spreadsheets, but they need to be used correctly.

Working examples for all the code shown in this document can be found in the `/samples/DefinedNames` folder.

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
$spreadsheet->addNamedRange( new \PhpOffice\PhpSpreadsheet\NamedRange('TAX_RATE', $worksheet, '=$B$1') );
$spreadsheet->addNamedRange( new \PhpOffice\PhpSpreadsheet\NamedRange('PRICE', $worksheet, '=$B$3') );

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
`/samples/DefinedNames/SimpleNamedRange.php`

This makes formulae in the generated spreadsheet easier to understand when viewing it them MS Excel. Using these Named Ranges (providing meaningful human-readable names for cells) makes the purpose of the formula immediately clear. We don't need to look for cell `B2` to see what it is, the name tells us.

And, if the Tax Rate changes to 16%, then we only need to change the value in cell `B1` to the new Tax rate (`=16%`), or if we want to calculate the Tax Charges for a different net price, that will immediately be reflected in all the calculations that reference those Named Ranges. No matter whereabouts in the worksheet I used that Named Range, it always references the value in cell `B1`.

In fact, because we were required to specify a worksheet when we defined the name, that name is available from any worksheet within the spreadsheet, and always means cell `B2` in this worksheet (but see the notes on Named Range Scope below).

### Absolute Named Ranges

In the above example, when I define the Named Range values (e.g. `'=$B$1'`), I used a `$` before both the row and the column. This made the Named Range an Absolute Reference.

Another example:
```php
// Set up some basic data for a timesheet
$worksheet
    ->setCellValue('A1', 'Charge Rate/hour:')
    ->setCellValue('B1', '7.50')
    ->setCellValue('A3', 'Date')
    ->setCellValue('B3', 'Hours')
    ->setCellValue('C3', 'Charge');

// Define named range using an absolute cell reference
$spreadsheet->addNamedRange( new NamedRange('CHARGE_RATE', $worksheet, '=$B$1') );

$workHours = [
    '2020-0-06' => 7.5,
    '2020-0-07' => 7.25,
    '2020-0-08' => 6.5,
    '2020-0-09' => 7.0,
    '2020-0-10' => 5.5,
];

// Populate the Timesheet
$startRow = 4;
$row = $startRow;
foreach ($workHours as $date => $hours) {
    $worksheet
        ->setCellValue("A{$row}", $date)
        ->setCellValue("B{$row}", $hours)
        ->setCellValue("C{$row}", "=B{$row}*CHARGE_RATE");
    $row++;
}
$endRow = $row - 1;

++$row;
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
`/samples/DefinedNames/AbsoluteNamedRange.php`

Because the Named Range `CHARGE_RATE` is defined as an Absolute cell reference, then it always references cell `B2` no matter where it is referenced in a formula in the spreadsheet.

### Relative Named Ranges

The previous example showed a simple timesheet using an Absolute Reference for the Charge Rate, used to calculate our billed charges to client.

The use of `B{$row}` in our formula (at least it will appear as an actual cell reference in MS Excel if we save the file and open it) requires a bit of mental agility to remember that column `B` is our hours for that day. Why can't we use another Named Range called something like `HOURS_PER_DAY` to make the formula more easily readable and meaningful.

But if we used an Absolute Named Range for `HOURS_PER_DAY`, then we'd need a different Named Range for each day (`MONDAY_HOURS_PER_DAY`, `TUESDAY_HOURS_PER_DAY`, etc), and a different formula for each day of the week; if we kept a monthly timesheet, we would have to defined a different Named Range for every day of the month... and that's a lot more trouble than it's worth, and quickly becomes unmanageable.

This is where Relative Named Ranges are very useful.

```php
// Set up some basic data for a timesheet
$worksheet
    ->setCellValue('A1', 'Charge Rate/hour:')
    ->setCellValue('B1', '7.50')
    ->setCellValue('A3', 'Date')
    ->setCellValue('B3', 'Hours')
    ->setCellValue('C3', 'Charge');

// Define named ranges
// CHARGE_RATE is an absolute cell reference that always points to cell B1
$spreadsheet->addNamedRange( new NamedRange('CHARGE_RATE', $worksheet, '=$B$1') );
// HOURS_PER_DAY is a relative cell reference that always points to column B, but to a cell in the row where it is used 
$spreadsheet->addNamedRange( new NamedRange('HOURS_PER_DAY', $worksheet, '=$B1') );

$workHours = [
    '2020-0-06' => 7.5,
    '2020-0-07' => 7.25,
    '2020-0-08' => 6.5,
    '2020-0-09' => 7.0,
    '2020-0-10' => 5.5,
];

// Populate the Timesheet
$startRow = 4;
$row = $startRow;
foreach ($workHours as $date => $hours) {
    $worksheet
        ->setCellValue("A{$row}", $date)
        ->setCellValue("B{$row}", $hours)
        ->setCellValue("C{$row}", "=HOURS_PER_DAY*CHARGE_RATE");
    $row++;
}
$endRow = $row - 1;

++$row;
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
`/samples/DefinedNames/RelativeNamedRange.php`

The difference in the cell definition for `HOURS_PER_DAY` (`'=$B1'`) is that we have a `$` in front of the column `B`, but not in front of the row number. The `$` makes the column absolute: no matter where in the worksheet we use this name, it always references column `B`. Without a `$`in front of the row number, we make the row number relative, relative to the row where the name appears in a formula, so it effectively replaces the `1` with its own row number when it executes the calculation.

When it is used in the formula in row 4, then it references cell `B4`, when it appears in row 5, it references cell `B5`, and so on. Using a Relative Named Range, we can use the same Named Range to refer to cells in different rows (and/or different columns), so we can re-use the same Named Range to refer to different cells relative to the row (or column) where we use them.

Named Ranges aren't limited to a single cell, but can point to a range of cells. A common use case might be to provide a series of column totals at the bottom of a dataset. Let's take our timesheet, and modify it just slightly to use a Relative column range for that purpose.

I won't replicate the entire code from the previous example, because I'm only changing a few lines; but we just replace the block:
```php
++$row;
$worksheet
    ->setCellValue("B{$row}", "=SUM(B{$startRow}:B{$endRow})")
    ->setCellValue("C{$row}", "=SUM(C{$startRow}:C{$endRow})");
```
with:
```php
// COLUMN_TOTAL is another relative cell reference that always points to the same range of rows but to cell in the column where it is used
$spreadsheet->addNamedRange( new NamedRange('COLUMN_DATA_VALUES', $worksheet, "=A\${$startRow}:A\${$endRow}") );

++$row;
$worksheet
    ->setCellValue("B{$row}", "=SUM(COLUMN_DATA_VALUES)")
    ->setCellValue("C{$row}", "=SUM(COLUMN_DATA_VALUES)");
```
`/samples/DefinedNames/RelativeNamedRange2.php`

Now that I've specified column as relative in the definition of `COLUMN_DATA_VALUES` with an address of column `A`, and the rows are absolute. When the same Relative Named Range is used in column `B`,it references cells in column `B` rather than `A`; and when it is used in column `C`, it references cells in column `C`.

While we still have a piece of code (`"=A\${$startRow}:A\${$endRow}"`) that isn't easily human-readable, when we open the generated spreadsheet in MS Excel, the displayed formula in for the cells for the totals is immediately understandable.

### Named Range Scope

Whenever we define a Named Range, we are required to specify a worksheet, and that name is then available from any worksheet within the spreadsheet, and always means that cell or cell range in the specified worksheet.

```php
// Set up some basic data for a timesheet
$worksheet
    ->setCellValue('A1', 'Charge Rate/hour:')
    ->setCellValue('B1', '7.50');

// Define a global named range on the first worksheet for our Charge Rate
// CHARGE_RATE is an absolute cell reference that always points to cell B1
// Because it is defined globally, it will still be usable from any worksheet in the spreadsheet
$spreadsheet->addNamedRange( new NamedRange('CHARGE_RATE', $worksheet, '=$B$1') );

// Create a second worksheet as our client timesheet
$worksheet = $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Client Timesheet'));

// Define named ranges
// HOURS_PER_DAY is a relative cell reference that always points to column B, but to a cell in the row where it is used
$spreadsheet->addNamedRange( new NamedRange('HOURS_PER_DAY', $worksheet, '=$B1') );

// Set up some basic data for a timesheet
$worksheet
    ->setCellValue('A1', 'Date')
    ->setCellValue('B1', 'Hours')
    ->setCellValue('C1', 'Charge');

$workHours = [
    '2020-0-06' => 7.5,
    '2020-0-07' => 7.25,
    '2020-0-08' => 6.5,
    '2020-0-09' => 7.0,
    '2020-0-10' => 5.5,
];

// Populate the Timesheet
$startRow = 2;
$row = $startRow;
foreach ($workHours as $date => $hours) {
    $worksheet
        ->setCellValue("A{$row}", $date)
        ->setCellValue("B{$row}", $hours)
        ->setCellValue("C{$row}", "=HOURS_PER_DAY*CHARGE_RATE");
    $row++;
}
$endRow = $row - 1;

// COLUMN_TOTAL is another relative cell reference that always points to the same range of rows but to cell in the column where it is used
$spreadsheet->addNamedRange( new NamedRange('COLUMN_DATA_VALUES', $worksheet, "=A\${$startRow}:A\${$endRow}") );

++$row;
$worksheet
    ->setCellValue("B{$row}", "=SUM(COLUMN_DATA_VALUES)")
    ->setCellValue("C{$row}", "=SUM(COLUMN_DATA_VALUES)");

echo sprintf(
    'Worked %.2f hours at a rate of %s - Charge to the client is %.2f',
    $worksheet->getCell("B{$row}")->getCalculatedValue(),
    $chargeRateCellValue = $spreadsheet
        ->getSheetByName($spreadsheet->getNamedRange('CHARGE_RATE')->getWorksheet()->getTitle())
        ->getCell($spreadsheet->getNamedRange('CHARGE_RATE')->getCellsInRange()[0])->getValue(),
    $worksheet->getCell("C{$row}")->getCalculatedValue()
), PHP_EOL;
```
`/samples/DefinedNames/ScopedNamedRange.php`

Even though `CHARGE_RATE` references a cell on a different worksheet, because is set as global (the default) it is accessible from any worksheet in the spreadsheet. so when we reference it in formulae on the second timesheet worksheet, we are able to access the value from that first worksheet and use it in our calculations.

However, a Named Range can be locally scoped so that it is only available when referenced from a specific worksheet, or it can be globally scoped. This means that you can use the same Named Range name with different values on different worksheets.

Building further on our timesheet, perhaps we use a different worksheet for each client, and we use the same hourly rate when billing most of our clients; but for one particular client (perhaps doing work for a a friend) we use a lower rate.

```php
$clients = [
    'Client #1 - Full Hourly Rate' => [
        '2020-0-06' => 2.5,
        '2020-0-07' => 2.25,
        '2020-0-08' => 6.0,
        '2020-0-09' => 3.0,
        '2020-0-10' => 2.25,
    ],
    'Client #2 - Full Hourly Rate' => [
        '2020-0-06' => 1.5,
        '2020-0-07' => 2.75,
        '2020-0-08' => 0.0,
        '2020-0-09' => 4.5,
        '2020-0-10' => 3.5,
    ],
    'Client #3 - Reduced Hourly Rate' => [
        '2020-0-06' => 3.5,
        '2020-0-07' => 2.5,
        '2020-0-08' => 1.5,
        '2020-0-09' => 0.0,
        '2020-0-10' => 1.25,
    ],
];

foreach ($clients as $clientName => $workHours) {
    $worksheet = $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $clientName));

    // Set up some basic data for a timesheet
    $worksheet
        ->setCellValue('A1', 'Charge Rate/hour:')
        ->setCellValue('B1', '7.50')
        ->setCellValue('A3', 'Date')
        ->setCellValue('B3', 'Hours')
        ->setCellValue('C3', 'Charge');
    ;

    // Define named ranges
    // CHARGE_RATE is an absolute cell reference that always points to cell B1
    $spreadsheet->addNamedRange( new NamedRange('CHARGE_RATE', $worksheet, '=$B$1', true) );
    // HOURS_PER_DAY is a relative cell reference that always points to column B, but to a cell in the row where it is used
    $spreadsheet->addNamedRange( new NamedRange('HOURS_PER_DAY', $worksheet, '=$B1', true) );

    // Populate the Timesheet
    $startRow = 4;
    $row = $startRow;
    foreach ($workHours as $date => $hours) {
        $worksheet
            ->setCellValue("A{$row}", $date)
            ->setCellValue("B{$row}", $hours)
            ->setCellValue("C{$row}", "=HOURS_PER_DAY*CHARGE_RATE");
        $row++;
    }
    $endRow = $row - 1;

    // COLUMN_TOTAL is another relative cell reference that always points to the same range of rows but to cell in the column where it is used
    $spreadsheet->addNamedRange( new NamedRange('COLUMN_TOTAL', $worksheet, "=A\${$startRow}:A\${$endRow}", true) );

    ++$row;
    $worksheet
        ->setCellValue("B{$row}", "=SUM(COLUMN_TOTAL)")
        ->setCellValue("C{$row}", "=SUM(COLUMN_TOTAL)");
}
$spreadsheet->removeSheetByIndex(0);

// Set the reduced charge rate for our special client
$worksheet
    ->setCellValue("B1", 4.5);

foreach ($spreadsheet->getAllSheets() as $worksheet) {
    echo sprintf(
        'Worked %.2f hours for "%s" at a rate of %.2f - Charge to the client is %.2f',
        $worksheet->getCell("B{$row}")->getCalculatedValue(),
        $worksheet->getTitle(),
        $worksheet->getCell('B1')->getValue(),
        $worksheet->getCell("C{$row}")->getCalculatedValue()
    ), PHP_EOL;
}
```
`/samples/DefinedNames/ScopedNamedRange2.php`

Now we are creating three worksheets for each of three different clients. Because each Named Range is linked to a worksheet, we need to create three sets of Named Ranges, so that we don't simply reference the cells on only one of the worksheets; but because we are locally scoping them (note the extra boolean argument used when we define the Named Ranges) we can use the same names on each worksheet, and they will reference the correct cells when we use them in our formulae on that worksheet.

When Named Ranges are being evaluated, the logic looks first to see if there is a locally scoped Named Range defined for the current worksheet. If there is, then that is the Named Range that will be used in the calculation. If no locally scoped Named Range with that name is found, the logic then looks to see if there is a globally scoped Named Range definition, and will use that if it is found. If no Named Range of the required name is found scoped to the current worksheet, or globally scoped, then a `#NAME` error will be returned.

## Named Formulae

A Named Formula is a stored formula, or part of a formula, that can be referenced in cells by name, and re-used in many different places within the spreadsheet.

As an example, I'll modify the simple Tax Calculator that I created as my example for Named Ranges.

```php
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
```
`/samples/DefinedNames/SimpleNamedFormula.php`

There are a few points to note here:

Firstly. we are actually storing the tax rate in a named formula (`TAX_RATE`) rather than as a cell value. When we display the tax rate in cell `B1`, we are actually storing an instruction for MS Excel to evaluate the formula and display the result in that cell.

Then we are using a formula `TAX` that references both another formula (`TAX_RATE`) and a Named Range (`PRICE`) and executes a calculation using them both (`PRICE * TAX_RATE`).

Finally, we are using the formula `TAX` in two different contexts. Once to display the tax value (in cell `B4`); and a second time as part of another formula (`PRICE + TAX`) in cell `B5`.

## Combining Named Ranges and Formulae

## Additional Comments

### Naming Names

The names that you assign to Defined Name must follow the following set of rules:
 - The first character of a name must be one of the following characters:
   - letter (including UTF-8 letters)
   - underscore (_)
   - backslash (\).
 - Remaining characters in the name can be
   - letters (including UTF-8 letters)
   - numbers (including UTF-8 numbers)
   - periods
   - underscore characters
 - The following are not allowed:
   - Space characters are not allowed as part of a name.
   - Names can't look like cell addresses, such as A35 or R2C2
 - Names are not case sensitive. For example, `North` and `NORTH` are treated as the same name.

PHPSpreadsheet doesn't yet fully validate the names that you use, so it is possible to create a spreadsheet in PHPSpreadsheet that will break when you try to open it in MS Excel; or that will break PHPSpreadsheet when they are referenced in a cell.

There is nothing to stop you creating a Defined Name that matches an existing Function name
```php
$spreadsheet->addNamedFormula(new NamedFormula('SUM', $worksheet, '=SUM(A1:E5)'));
```
And this will work without problems in MS Excel. However, it is not guaranteed to work correctly in PHPSpreadsheet; and will certainly cause confusion for anybody reading it, so it is not recommended.
