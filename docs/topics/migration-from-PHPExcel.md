# Migration from PHPExcel

PhpSpreadsheet introduced many breaking changes by introducing
namespaces and renaming some classes. To help you migrate existing
project, a tool was written to replace all references to PHPExcel
classes to their new names.

The tool is included in PhpSpreadsheet. It scans recursively all files
and directories, starting from the current directory. Assuming it was
installed with composer, it can be run like so:

``` sh
cd /project/to/migrate/src
/project/to/migrate/vendor/phpoffice/phpspreadsheet/bin/migrate-from-phpexcel
```

**Important** The tool will irreversibly modify your sources, be sure to
backup everything, and double check the result before committing.

## Removed deprecated things

In addition to automated changes, usage of deprecated methods must be migrated
manually.

### Worksheet::duplicateStyleArray()

``` php
// Before
$worksheet->duplicateStyleArray($styles, $range, $advanced);

// After
$worksheet->getStyle($range)->applyFromArray($styles, $advanced);
```

### DataType::dataTypeForValue()

``` php
// Before
DataType::dataTypeForValue($value);

// After
DefaultValueBinder::dataTypeForValue($value);
```

### Conditional::getCondition()

``` php
// Before
$conditional->getCondition();

// After
$conditional->getConditions()[0];
```

### Conditional::setCondition()

``` php
// Before
$conditional->setCondition($value);

// After
$conditional->setConditions($value);
```

### Worksheet::getDefaultStyle()

``` php
// Before
$worksheet->getDefaultStyle();

// After
$worksheet->getParent()->getDefaultStyle();
```

### Worksheet::setDefaultStyle()

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

### Worksheet::setSharedStyle()

``` php
// Before
$worksheet->setSharedStyle($sharedStyle, $range);

// After
$worksheet->duplicateStyle($sharedStyle, $range);
```

### Worksheet::getSelectedCell()

``` php
// Before
$worksheet->getSelectedCell();

// After
$worksheet->getSelectedCells();
```

### Writer\Xls::setTempDir()

``` php
// Before
$writer->setTempDir();

// After, there is no way to set temporary storage directory anymore
```

## Autoloader

The class `PHPExcel_Autoloader` was removed entirely and is replaced by composer
autoloading mechanism.

## Writing PDF

`PHPExcel_Settings::setPdfRenderer()` and `PHPExcel_Settings::setPdfRenderer()`
were removed and PDF libraries must be installed via composer. So the only thing
to do is to specify a renderer like so:

```php
$rendererName = \PhpOffice\PhpSpreadsheet\Settings::PDF_RENDERER_MPDF;
\PhpOffice\PhpSpreadsheet\Settings::setPdfRendererName($rendererName);
```

## PclZip and ZipArchive

Support for PclZip were dropped in favor of the more complete and modern
[PHP extension ZipArchive](http://php.net/manual/en/book.zip.php).
So the following were removed:

- `PclZip`
- `PHPExcel_Settings::setZipClass()`
- `PHPExcel_Settings::getZipClass()`
- `PHPExcel_Shared_ZipArchive`
- `PHPExcel_Shared_ZipStreamWrapper`


## Cell caching

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

## Dropped conditionally returned cell

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
