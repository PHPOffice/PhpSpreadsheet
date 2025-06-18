# Creating a spreadsheet

## The `Spreadsheet` class

The `Spreadsheet` class is the core of PhpSpreadsheet. It contains
references to the contained worksheets, document security settings and
document meta data.

To simplify the PhpSpreadsheet concept: the `Spreadsheet` class
represents your workbook.

Typically, you will create a workbook in one of two ways, either by
loading it from a spreadsheet file, or creating it manually. A third
option, though less commonly used, is cloning an existing workbook that
has been created using one of the previous two methods.

### Loading a Workbook from a file

Details of the different spreadsheet formats supported, and the options
available to read them into a Spreadsheet object are described fully in
the [Reading Files](./reading-files.md) document.

``` php
$inputFileName = './sampleData/example1.xls';

/** Load $inputFileName to a Spreadsheet object **/
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
```

### Creating a new workbook

If you want to create a new workbook, rather than load one from file,
then you simply need to instantiate it as a new Spreadsheet object.

``` php
/** Create a new Spreadsheet Object **/
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
```

A new workbook will always be created with a single worksheet.

## Clearing a Workbook from memory

The PhpSpreadsheet object contains cyclic references (e.g. the workbook
is linked to the worksheets, and the worksheets are linked to their
parent workbook) which cause problems when PHP tries to clear the
objects from memory when they are `unset()`, or at the end of a function
when they are in local scope. The result of this is "memory leaks",
which can easily use a large amount of PHP's limited memory.

This can only be resolved manually: if you need to unset a workbook,
then you also need to "break" these cyclic references before doing so.
PhpSpreadsheet provides the `disconnectWorksheets()` method for this
purpose.

``` php
$spreadsheet->disconnectWorksheets();
unset($spreadsheet);
```
