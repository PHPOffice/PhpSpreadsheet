# Sparklines

## Introduction

Sparklines are tiny charts drawn inside a single cell, giving a compact visual
representation of a row (or column) of data. Excel supports three kinds of
sparkline: line, column, and win/loss.

## Support

Sparklines are supported by the Xlsx reader and writer. Sparklines found when
reading an Xlsx file are exposed as model objects, and are preserved when the
file is written back out.

## Adding a sparkline

The quickest way to add a single sparkline is `Worksheet::addSparkline()`, which
wraps the sparkline in its own group and returns that group so you can adjust its
formatting:

```php
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\Sparkline;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineType;

$worksheet->addSparkline(
    new Sparkline(location: 'G2', dataRange: 'Sheet1!B2:F2'),
    SparklineType::Line
);
```

## Sparkline groups

Sparklines are stored in groups; every sparkline in a group shares the same
formatting (type, colours, markers, axis behaviour). Build a group explicitly
when you want several sparklines to share formatting, or when you need to
customise it:

```php
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineGroup;
use PhpOffice\PhpSpreadsheet\Worksheet\Sparkline\SparklineType;

$group = new SparklineGroup();
$group->setType(SparklineType::Column)
    ->setDisplayMarkers(true)
    ->setDisplayHigh(true)
    ->setColorSeries('FF00B050')
    ->createSparkline('G3', 'Sheet1!B3:F3')
    ->createSparkline('G4', 'Sheet1!B4:F4');

$worksheet->addSparklineGroup($group);
```

## Sparkline types

`SparklineType` is an enum with three cases:

| Case                      | Excel type   | OOXML value |
|---------------------------|--------------|-------------|
| `SparklineType::Line`     | Line         | `line`      |
| `SparklineType::Column`   | Column       | `column`    |
| `SparklineType::WinLoss`  | Win/Loss     | `stacked`   |

## Group options

`SparklineGroup` exposes getters and setters for the common Excel options,
including:

- `setDisplayMarkers()`, `setDisplayHigh()`, `setDisplayLow()`,
  `setDisplayFirst()`, `setDisplayLast()`, `setDisplayNegative()`
- `setDisplayXAxis()`, `setDisplayHidden()`, `setRightToLeft()`
- `setLineWeight()`
- `setDisplayEmptyCellsAs()` (`gap`, `zero`, or `span`)
- `setMinAxisType()` / `setMaxAxisType()` (`individual`, `group`, or `custom`)
  together with `setManualMin()` / `setManualMax()`
- Colour setters (ARGB hex strings) for the series, negative points, axis,
  markers, and the first/last/high/low points

## Reading sparklines

```php
foreach ($worksheet->getSparklineGroupCollection() as $group) {
    echo $group->getType()->value, "\n";
    foreach ($group->getSparklines() as $sparkline) {
        echo $sparkline->getLocation(), ' <- ', $sparkline->getDataRange(), "\n";
    }
}
```
