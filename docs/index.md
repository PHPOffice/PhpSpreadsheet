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

# Getting started

## Software requirements

PHP version 7.4 or newer to develop using PhpSpreadsheet. Other requirements, such as PHP extensions, are enforced by
composer. See the `require` section of [the composer.json file](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/composer.json)
for details.

### PHP version support

LTS: Support for PHP versions will only be maintained for a period of six months beyond the
[end of life of that PHP version](https://www.php.net/eol.php).

Currently the required PHP minimum version is PHP 7.4: the last release was 7.4.32 on 29th September 2022, and security support ends on 28th November 2022, so PhpSpreadsheet will support PHP 7.4 until 28th May 2023.
PHP 8.0 is officially [End of Life](https://www.php.net/supported-versions.php) on 26th November 2023, and PhpSpreadsheet will continue to support PHP 8.0 for six months after that date.

See the `composer.json` for other requirements.

## Installation

Use [composer](https://getcomposer.org) to install PhpSpreadsheet into your project:

```sh
composer require phpoffice/phpspreadsheet
```

Or also download the documentation and samples if you plan to use them:

```sh
composer require phpoffice/phpspreadsheet --prefer-source
```

If you are building your installation on a development machine that is on a different PHP version to the server where it will be deployed, or if your PHP CLI version is not the same as your run-time such as `php-fpm` or Apache's `mod_php`, then you might want to add the following to your `composer.json` before installing:
```json
{
    "require": {
        "phpoffice/phpspreadsheet": "^1.23"
    },
    "config": {
        "platform": {
            "php": "7.4"
        }
    }
}
```
and then run
```sh
composer install
```
to ensure that the correct dependencies are retrieved to match your deployment environment.

See [CLI vs Application run-time](https://php.watch/articles/composer-platform-check) for more details.

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
