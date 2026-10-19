# Pivot Tables

## Introduction

A pivot table summarises a range of source data, letting you group and
aggregate it by placing fields on rows, columns, filters and values.

## Support

PhpSpreadsheet can:

- **read the definition** of pivot tables that already exist in an Xlsx file
  into a read-only object model (name, location, source data, field layout);
- **preserve** those pivot tables through a load/save round-trip (their table,
  cache definition and cache records parts are written back unchanged);
- **create** a new pivot table from a range of source data.

Created pivot tables are written with *refresh on load* set, so the
spreadsheet application computes and lays out the value cells when the file is
opened. PhpSpreadsheet does not itself recalculate or render the pivot, and
does not yet support page/report filters, grouping or modifying an existing
(loaded) pivot table.

## Reading pivot tables

Each worksheet owns a collection of the pivot tables placed on it:

```php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load('with-pivot.xlsx');

$worksheet = $spreadsheet->getSheetByName('Pivot');

foreach ($worksheet->getPivotTableCollection() as $pivotTable) {
    echo $pivotTable->getName(), ' @ ', $pivotTable->getLocation(), PHP_EOL;

    $cache = $pivotTable->getCacheDefinition();
    if ($cache !== null) {
        echo '  source: ', $cache->getSourceWorksheet(), '!', $cache->getSourceRange(), PHP_EOL;
    }

    foreach ($pivotTable->getRowFields() as $field) {
        echo '  row: ', $field->getName(), PHP_EOL;
    }
    foreach ($pivotTable->getColumnFields() as $field) {
        echo '  column: ', $field->getName(), PHP_EOL;
    }
    foreach ($pivotTable->getDataFields() as $field) {
        echo '  value: ', $field->getName(), ' (', $field->getSubtotal(), ')', PHP_EOL;
    }
}
```

You can also look a pivot table up by name:

```php
$pivotTable = $worksheet->getPivotTableByName('PivotTable1');
```

## Creating a pivot table

Use `PivotTableBuilder` to create a pivot table from a source range. The first
row of the range is treated as the header row and provides the field names.

```php
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotField;
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotTableBuilder;

$builder = new PivotTableBuilder($dataSheet, 'A1:C100');
$builder
    ->addRowField('Region')
    ->addColumnField('Product')
    ->addDataField('Amount', PivotField::SUBTOTAL_SUM);

// Place the pivot table at A3 on $pivotSheet and register it.
$pivotTable = $builder->build($pivotSheet, 'A3', 'SalesPivot');
```

When the workbook is saved as Xlsx, the cache definition, cache records and
pivot table parts are generated with *refresh on load* set, so the value cells
are filled in by the spreadsheet application when the file is opened.

Supported aggregation functions are the `PivotField::SUBTOTAL_*` constants
(`SUBTOTAL_SUM`, `SUBTOTAL_COUNT`, `SUBTOTAL_AVERAGE`, `SUBTOTAL_MAX`,
`SUBTOTAL_MIN`, and the statistical variants). At least one data field is
required.

### Page (report filter) fields

Add a field to the page axis to turn it into a report filter:

```php
$builder->addPageField('Region');
```

### Grouping fields

A numeric field can be grouped into fixed-width buckets, and a date field can
be grouped by a calendar unit. Grouping is declared on the builder before
`build()`:

```php
use PhpOffice\PhpSpreadsheet\Worksheet\PivotTable\PivotFieldGroup;

// Buckets of 10 between 20 and 60: <20, 20-30, ..., 50-60, >60
$builder->groupFieldByNumericRange('Age', 10, 20, 60);

// Group order dates by quarter (or years/months/...).
$builder->groupFieldByDate('OrderDate', PivotFieldGroup::GROUP_BY_QUARTERS);
```

The grouping is written into the pivot cache definition; the spreadsheet
application materialises the grouped buckets when it refreshes the pivot on
open. Date grouping uses a single calendar unit per field.

## Object model

- `Worksheet\PivotTable\PivotTable` — a single pivot table: `getName()`,
  `getLocation()`, `getWorksheet()`, `getCacheDefinition()`, `getFields()`, and
  the axis helpers `getRowFields()`, `getColumnFields()`, `getPageFields()` and
  `getDataFields()`.
- `Worksheet\PivotTable\PivotCacheDefinition` — the source of the data:
  `getCacheId()`, `getSourceWorksheet()`, `getSourceRange()` and
  `getCacheFields()`.
- `Worksheet\PivotTable\PivotField` — one field of the pivot table:
  `getName()`, `getIndex()`, `getAxis()` (one of the `PivotField::AXIS_*`
  constants), `isDataField()` and `getSubtotal()`.
