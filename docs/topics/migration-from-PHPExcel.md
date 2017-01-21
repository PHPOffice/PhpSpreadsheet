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
