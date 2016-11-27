# PhpSpreadsheet Developer Documentation


## Creating a spreadsheet

### The `Spreadsheet` class

The `Spreadsheet` class is the core of PhpSpreadsheet. It contains references to the contained worksheets, document security settings and document meta data.

To simplify the PhpSpreadsheet concept: the `Spreadsheet` class represents your workbook.

Typically, you will create a workbook in one of two ways, either by loading it from a spreadsheet file, or creating it manually. A third option, though less commonly used, is cloning an existing workbook that has been created using one of the previous two methods.

#### Loading a Workbook from a file

Details of the different spreadsheet formats supported, and the options available to read them into a Spreadsheet object are described fully in the PhpSpreadsheet User Documentation - Reading Spreadsheet Files document.

```php
$inputFileName = './sampleData/example1.xls';

/** Load $inputFileName to a Spreadsheet object **/
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
```

#### Creating a new workbook

If you want to create a new workbook, rather than load one from file, then you simply need to instantiate it as a new Spreadsheet object.

```php
/** Create a new Spreadsheet Object **/
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
```

A new workbook will always be created with a single worksheet.
