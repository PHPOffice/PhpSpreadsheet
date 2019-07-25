# Welcome to PhpSpreadsheet's documentation

![Logo](./assets/logo.svg)

PhpSpreadsheet is a library written in pure PHP and providing a set of
classes that allow you to read from and to write to different
spreadsheet file formats, like Excel and LibreOffice Calc.

## File formats supported

|Format                                      |Reading|Writing|
|--------------------------------------------|:-----:|:-----:|
|Open Document Format/OASIS (.ods)           |   ✓   |   ✓   |
|Office Open XML (.xlsx) Excel 2007 and above|   ✓   |   ✓   |
|BIFF 8 (.xls) Excel 97 and above            |   ✓   |   ✓   |
|BIFF 5 (.xls) Excel 95                      |   ✓   |       |
|SpreadsheetML (.xml) Excel 2003             |   ✓   |       |
|Gnumeric                                    |   ✓   |       |
|HTML                                        |   ✓   |   ✓   |
|SYLK                                        |   ✓   |       |
|CSV                                         |   ✓   |   ✓   |
|PDF (using either the TCPDF, Dompdf or mPDF libraries, which need to be installed separately)|       |   ✓   |

# Getting started

## Software requirements

PHP version 7.1 or newer to develop using PhpSpreadsheet. Other requirements, such as PHP extensions, are enforced by
composer. See the `require` section of [the composer.json file](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/composer.json)
for details.

### PHP version support

Support for PHP versions will only be maintained for a period of six months beyond the end-of-life of that PHP version

## Installation

Use [composer](https://getcomposer.org) to install PhpSpreadsheet into your project:

```sh
composer require phpoffice/phpspreadsheet
```

## Hello World

This would be the simplest way to write a spreadsheet:

```php
<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');
```

## Learn by example

A good way to get started is to run some of the samples. Serve the samples via
PHP built-in webserver:

```sh
php -S localhost:8000 -t vendor/phpoffice/phpspreadsheet/samples
```

Then point your browser to:

> http://localhost:8000/

The samples may also be run directly from the command line, for example:

```sh
php vendor/phpoffice/phpspreadsheet/samples/Basic/01_Simple.php
```

## Learn by documentation

For more in-depth documentation, you may read about an [overview of the
architecture](./topics/architecture.md),
[creating a spreadsheet](./topics/creating-spreadsheet.md),
[worksheets](./topics/worksheets.md),
[accessing cells](./topics/accessing-cells.md) and
[reading and writing to files](./topics/reading-and-writing-to-file.md).

Or browse the [API documentation](https://phpoffice.github.io/PhpSpreadsheet/master).

# Credits

Please refer to the [contributor
list](https://github.com/PHPOffice/PhpSpreadsheet/graphs/contributors)
for up-to-date credits.
