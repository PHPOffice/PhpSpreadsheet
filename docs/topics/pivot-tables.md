# Pivot Tables

## Introduction

A pivot table summarises a range of source data, letting you group and
aggregate it by placing fields on rows, columns, filters and values.

## Support

PhpSpreadsheet can currently **read the definition** of pivot tables that
already exist in an Xlsx file into a read-only object model. It exposes the
pivot table's name, where it is placed on the sheet, the source data it draws
from, and how its fields are laid out.

It does not yet recalculate, render, create or modify pivot tables. When a
loaded spreadsheet is saved, existing pivot tables are still handled by the
writer's normal round-trip behaviour; the read model is purely for inspection.

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
