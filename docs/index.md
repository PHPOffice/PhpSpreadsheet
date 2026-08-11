# Welcome to PhpSpreadsheet's documentation

![Logo](./assets/logo.svg)

PhpSpreadsheet is a library written in pure PHP and offers a set of classes that
allow you to read and write various spreadsheet file formats such as Excel and LibreOffice Calc.

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

Note - reading or writing certain aspects of a spreadsheet may not be supported in all formats. For more details, please consult
[Features Cross-reference](./references/features-cross-reference.md).

# Getting started

## Software requirements

PHP version 8.1 or newer to develop using PhpSpreadsheet. Other requirements, such as PHP extensions, are enforced by
composer. See the `require` section of [the composer.json file](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/composer.json)
for details.

### PHP version support

LTS: Support for PHP versions will only be maintained for a period of six months beyond the
[end of life of that PHP version](https://www.php.net/eol.php).

Currently, the required PHP minimum version is __PHP 8.1__, and we [will support that version](https://www.php.net/eol.php) until June 2026.

Support for PHP versions will only be maintained for a period of six months beyond the
[end of life](https://www.php.net/supported-versions) of that PHP version.

See the `composer.json` for other requirements.

## Installation

Use [composer](https://getcomposer.org) to install PhpSpreadsheet into your project:

```sh
composer require phpoffice/phpspreadsheet
```

Or also download the documentation and samples if you plan to use them (note that `git` must be in your path for this to work):

```sh
composer require phpoffice/phpspreadsheet --prefer-source
```

If you are building your installation on a development machine that is on a different PHP version to the server where it
will be deployed, or if your PHP CLI version is different from your run-time such as `php-fpm` or Apache's `mod_php`,
then you might want to configure composer for that.
See [composer documentation](https://getcomposer.org/doc/06-config.md#platform)
on how to edit your `composer.json` to ensure that the correct dependencies are retrieved to match your deployment
environment.

See [CLI vs Application run-time](https://php.watch/articles/composer-platform-check) for more details.

### Additional Installation Options

If you want to write to PDF, or to include Charts when you write to HTML or PDF, then you will need to install additional libraries:

#### PDF

For PDF Generation, you can install any of the following, and then configure PhpSpreadsheet to indicate which library you are going to use:
- mpdf/mpdf
- dompdf/dompdf
- tecnickcom/tcpdf

and configure PhpSpreadsheet using:

```php
// Dompdf, Mpdf or Tcpdf (as appropriate)
$className = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf::class;
IOFactory::registerWriter('Pdf', $className);
```
or the appropriate PDF Writer wrapper for the library that you have chosen to install.

#### Chart Export

For Chart export, we support following packages, which you will also need to install yourself using `composer require`
- [jpgraph/jpgraph](https://packagist.org/packages/jpgraph/jpgraph) (this package was abandoned at version 4.0.
  You can manually download the latest version that supports PHP 8 and above from [jpgraph.net](https://jpgraph.net/))
- [mitoteam/jpgraph](https://packagist.org/packages/mitoteam/jpgraph) - up to date fork with modern PHP versions support and some bugs fixed.

and then configure PhpSpreadsheet using:
```php
// to use jpgraph/jpgraph
Settings::setChartRenderer(\PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph::class);
//or
// to use mitoteam/jpgraph
Settings::setChartRenderer(\PhpOffice\PhpSpreadsheet\Chart\Renderer\MtJpGraphRenderer::class);
```

One or the other of these libraries is necessary if you want to generate HTML or PDF files that include charts; or to render a Chart to an Image format from within your code.
They are not necessary to define charts for writing to `Xlsx` files.
Other file formats don't support writing Charts.

## Hello World

This would be the simplest way to write a spreadsheet:

```php
<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');
```

## Learn by example

A good way to get started is to run some of the samples. Don't forget to download them via `--prefer-source` composer
flag. And then serve them via PHP built-in webserver:

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

For more documentation in depth, you may read about an [overview of the
architecture](./topics/architecture.md),
[creating a spreadsheet](./topics/creating-spreadsheet.md),
[worksheets](./topics/worksheets.md),
[accessing cells](./topics/accessing-cells.md) and
[reading and writing to files](./topics/reading-and-writing-to-file.md).

Or browse the [API documentation](https://phpoffice.github.io/PhpSpreadsheet).

# Credits

Please refer to the [contributor
list](https://github.com/PHPOffice/PhpSpreadsheet/graphs/contributors)
for up-to-date credits.
