# Migration from PHPExcel

PhpSpreadsheet introduced many breaking changes by introducing
namespaces and renaming some classes. To help you migrate existing
project, a tool was written to replace all references to PHPExcel
classes to their new names. But they are also manual changes that
need to be done.

## Automated tool

The tool is included in PhpSpreadsheet. It scans recursively all files
and directories, starting from the current directory. Assuming it was
installed with composer, it can be run like so:

``` sh
cd /project/to/migrate/src
/project/to/migrate/vendor/phpoffice/phpspreadsheet/bin/migrate-from-phpexcel
```

**Important** The tool will irreversibly modify your sources, be sure to
backup everything, and double check the result before committing.

## Manual changes

In addition to automated changes, a few things need to be migrated manually.

### Renamed readers and writers

When using `IOFactory::createReader()`, `IOFactory::createWriter()` and
`IOFactory::identify()`, the reader/writer short names are used. Those were
changed, along as their corresponding class, to remove ambiguity:

Before           | After
-----------------|---------
`'CSV'`          | `'Csv'`
`'Excel2003XML'` | `'Xml'`
`'Excel2007'`    | `'Xlsx'`
`'Excel5'`       | `'Xls'`
`'Gnumeric'`     | `'Gnumeric'`
`'HTML'`         | `'Html'`
`'OOCalc'`       | `'Ods'`
`'OpenDocument'` | `'Ods'`
`'PDF'`          | `'Pdf'`
`'SYLK'`         | `'Slk'`

### Simplified IOFactory

The following methods :

- `PHPExcel_IOFactory::getSearchLocations()`
- `PHPExcel_IOFactory::setSearchLocations()`
- `PHPExcel_IOFactory::addSearchLocation()`

were replaced by `IOFactory::registerReader()` and `IOFactory::registerWriter()`. That means
IOFactory now relies on classes autoloading.

Before:

```php
\PHPExcel_IOFactory::addSearchLocation($type, $location, $classname);
```

After:

```php
\PhpOffice\PhpSpreadsheet\IOFactory::registerReader($type, $classname);
```

### Removed deprecated things

#### Worksheet::duplicateStyleArray()

``` php
// Before
$worksheet->duplicateStyleArray($styles, $range, $advanced);

// After
$worksheet->getStyle($range)->applyFromArray($styles, $advanced);
```

#### DataType::dataTypeForValue()

``` php
// Before
DataType::dataTypeForValue($value);

// After
DefaultValueBinder::dataTypeForValue($value);
```

#### Conditional::getCondition()

``` php
// Before
$conditional->getCondition();

// After
$conditional->getConditions()[0];
```

#### Conditional::setCondition()

``` php
// Before
$conditional->setCondition($value);

// After
$conditional->setConditions($value);
```

#### Worksheet::getDefaultStyle()

``` php
// Before
$worksheet->getDefaultStyle();

// After
$worksheet->getParent()->getDefaultStyle();
```

#### Worksheet::setDefaultStyle()

``` php
// Before
$worksheet->setDefaultStyle($value);

// After
$worksheet->getParent()->getDefaultStyle()->applyFromArray([
    'font' => [
        'name' => $pValue->getFont()->getName(),
        'size' => $pValue->getFont()->getSize(),
    ],
]);

```

#### Worksheet::setSharedStyle()

``` php
// Before
$worksheet->setSharedStyle($sharedStyle, $range);

// After
$worksheet->duplicateStyle($sharedStyle, $range);
```

#### Worksheet::getSelectedCell()

``` php
// Before
$worksheet->getSelectedCell();

// After
$worksheet->getSelectedCells();
```

#### Writer\Xls::setTempDir()

``` php
// Before
$writer->setTempDir();

// After, there is no way to set temporary storage directory anymore
```

### Autoloader

The class `PHPExcel_Autoloader` was removed entirely and is replaced by composer
autoloading mechanism.

### Writing PDF

PDF libraries must be installed via composer. And the following methods were removed
and are replaced by `IOFactory::registerWriter()` instead:

- `PHPExcel_Settings::getPdfRenderer()`
- `PHPExcel_Settings::setPdfRenderer()`
- `PHPExcel_Settings::getPdfRendererName()`
- `PHPExcel_Settings::setPdfRendererName()`

Before:

```php
\PHPExcel_Settings::setPdfRendererName(PHPExcel_Settings::PDF_RENDERER_MPDF);
\PHPExcel_Settings::setPdfRenderer($somePath);
$writer = \PHPExcel_IOFactory::createWriter($spreadsheet, 'PDF');
```

After:

```php
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');

// Or alternatively
\PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf::class);
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');

// Or alternatively
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
```

### Rendering charts

When rendering charts for HTML or PDF outputs, the process was also simplified. And while
JpGraph support is still available, it is unfortunately not up to date for latest PHP versions
and it will generate various warnings.

If you rely on this feature, please consider
contributing either patches to JpGraph or another `IRenderer` implementation (a good
candidate might be [CpChart](https://github.com/szymach/c-pchart)).

Before:

```php
$rendererName = \PHPExcel_Settings::CHART_RENDERER_JPGRAPH;
$rendererLibrary = 'jpgraph3.5.0b1/src/';
$rendererLibraryPath = '/php/libraries/Charts/' . $rendererLibrary;

\PHPExcel_Settings::setChartRenderer($rendererName, $rendererLibraryPath);
```

After:

Require the dependency via composer:

```sh
composer require jpgraph/jpgraph
```

And then:

```php
Settings::setChartRenderer(\PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph::class);
```

### PclZip and ZipArchive

Support for PclZip were dropped in favor of the more complete and modern
[PHP extension ZipArchive](http://php.net/manual/en/book.zip.php).
So the following were removed:

- `PclZip`
- `PHPExcel_Settings::setZipClass()`
- `PHPExcel_Settings::getZipClass()`
- `PHPExcel_Shared_ZipArchive`
- `PHPExcel_Shared_ZipStreamWrapper`

### Cell caching

Cell caching was heavily refactored to leverage
[PSR-16](http://www.php-fig.org/psr/psr-16/). That means most classes
related to that feature were removed:

- `PHPExcel_CachedObjectStorage_APC`
- `PHPExcel_CachedObjectStorage_DiscISAM`
- `PHPExcel_CachedObjectStorage_ICache`
- `PHPExcel_CachedObjectStorage_Igbinary`
- `PHPExcel_CachedObjectStorage_Memcache`
- `PHPExcel_CachedObjectStorage_Memory`
- `PHPExcel_CachedObjectStorage_MemoryGZip`
- `PHPExcel_CachedObjectStorage_MemorySerialized`
- `PHPExcel_CachedObjectStorage_PHPTemp`
- `PHPExcel_CachedObjectStorage_SQLite`
- `PHPExcel_CachedObjectStorage_SQLite3`
- `PHPExcel_CachedObjectStorage_Wincache`

In addition to that, `\PhpOffice\PhpSpreadsheet::getCellCollection()` was renamed
to `\PhpOffice\PhpSpreadsheet::getCoordinates()` and
`\PhpOffice\PhpSpreadsheet::getCellCacheController()` to
`\PhpOffice\PhpSpreadsheet::getCellCollection()` for clarity.

Refer to [the new documentation](./memory_saving.md) to see how to migrate.

### Dropped conditionally returned cell

For all the following methods, it is no more possible to change the type of
returned value. It always return the Worksheet and never the Cell or Rule:

- Worksheet::setCellValue()
- Worksheet::setCellValueByColumnAndRow()
- Worksheet::setCellValueExplicit()
- Worksheet::setCellValueExplicitByColumnAndRow()
- Worksheet::addRule()

Migration would be similar to:

``` php
// Before
$cell = $worksheet->setCellValue('A1', 'value', true);

// After
$cell = $worksheet->getCell('A1')->setValue('value');
```

### Standardized keys for styling

Array keys used for styling have been standardized for a more coherent experience.
It now uses the same wording and casing as the getter and setter:

```php
// Before
$style = [
    'numberformat' => [
        'code' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
    ],
    'font' => [
        'strike' => true,
        'superScript' => true,
        'subScript' => true,
    ],
    'alignment' => [
        'rotation' => 90,
        'readorder' => Alignment::READORDER_RTL,
        'wrap' => true,
    ],
    'borders' => [
        'diagonaldirection' => Borders::DIAGONAL_BOTH,
        'allborders' => [
            'style' => Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'type' => Fill::FILL_GRADIENT_LINEAR,
        'startcolor' => [
            'argb' => 'FFA0A0A0',
        ],
        'endcolor' => [
            'argb' => 'FFFFFFFF',
        ],
    ],
];

// After
$style = [
    'numberFormat' => [
        'formatCode' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
    ],
    'font' => [
        'strikethrough' => true,
        'superscript' => true,
        'subscript' => true,
    ],
    'alignment' => [
        'textRotation' => 90,
        'readOrder' => Alignment::READORDER_RTL,
        'wrapText' => true,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => Fill::FILL_GRADIENT_LINEAR,
        'startColor' => [
            'argb' => 'FFA0A0A0',
        ],
        'endColor' => [
            'argb' => 'FFFFFFFF',
        ],
    ],
];
```

### Dedicated class to manipulate coordinates

Methods to manipulate coordinates that used to exists in `PHPExcel_Cell` were extracted
to a dedicated new class `\PhpOffice\PhpSpreadsheet\Cell\Coordinate`. The methods are:

- `absoluteCoordinate()`
- `absoluteReference()`
- `buildRange()`
- `columnIndexFromString()`
- `coordinateFromString()`
- `extractAllCellReferencesInRange()`
- `getRangeBoundaries()`
- `mergeRangesInCollection()`
- `rangeBoundaries()`
- `rangeDimension()`
- `splitRange()`
- `stringFromColumnIndex()`

### Column index based on 1

Column indexes are now based on 1. So column `A` is the index `1`. This is consistent
with rows starting at 1 and Excel function `COLUMN()` that returns `1` for column `A`.
So the code must be adapted with something like:

```php
// Before
$cell = $worksheet->getCellByColumnAndRow($column, $row);

for ($column = 0; $column < $max; $column++) {
    $worksheet->setCellValueByColumnAndRow($column, $row, 'value ' . $column);
}

// After
$cell = $worksheet->getCellByColumnAndRow($column + 1, $row);

for ($column = 1; $column <= $max; $column++) {
    $worksheet->setCellValueByColumnAndRow($column, $row, 'value ' . $column);
}
```

All the following methods are affected:

- `PHPExcel_Worksheet::cellExistsByColumnAndRow()`
- `PHPExcel_Worksheet::freezePaneByColumnAndRow()`
- `PHPExcel_Worksheet::getCellByColumnAndRow()`
- `PHPExcel_Worksheet::getColumnDimensionByColumn()`
- `PHPExcel_Worksheet::getCommentByColumnAndRow()`
- `PHPExcel_Worksheet::getStyleByColumnAndRow()`
- `PHPExcel_Worksheet::insertNewColumnBeforeByIndex()`
- `PHPExcel_Worksheet::mergeCellsByColumnAndRow()`
- `PHPExcel_Worksheet::protectCellsByColumnAndRow()`
- `PHPExcel_Worksheet::removeColumnByIndex()`
- `PHPExcel_Worksheet::setAutoFilterByColumnAndRow()`
- `PHPExcel_Worksheet::setBreakByColumnAndRow()`
- `PHPExcel_Worksheet::setCellValueByColumnAndRow()`
- `PHPExcel_Worksheet::setCellValueExplicitByColumnAndRow()`
- `PHPExcel_Worksheet::setSelectedCellByColumnAndRow()`
- `PHPExcel_Worksheet::stringFromColumnIndex()`
- `PHPExcel_Worksheet::unmergeCellsByColumnAndRow()`
- `PHPExcel_Worksheet::unprotectCellsByColumnAndRow()`
- `PHPExcel_Worksheet_PageSetup::addPrintAreaByColumnAndRow()`
- `PHPExcel_Worksheet_PageSetup::setPrintAreaByColumnAndRow()`

### Removed default values

Default values for many methods were removed when it did not make sense. Typically,
setter methods should not have default values. For a complete list of methods and
their original default values, see [that commit](https://github.com/PHPOffice/PhpSpreadsheet/commit/033a4bdad56340795a5bf7ec3c8a2fde005cda24).
