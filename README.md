# PhpSpreadsheet

[![Build Status](https://github.com/PHPOffice/PhpSpreadsheet/workflows/main/badge.svg)](https://github.com/PHPOffice/PhpSpreadsheet/actions)
[![Code Quality](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/PHPOffice/PhpSpreadsheet)](https://packagist.org/packages/phpoffice/phpspreadsheet)
[![Latest Stable Version](https://img.shields.io/github/v/release/PHPOffice/PhpSpreadsheet)](https://packagist.org/packages/phpoffice/phpspreadsheet)
[![License](https://img.shields.io/github/license/PHPOffice/PhpSpreadsheet)](https://packagist.org/packages/phpoffice/phpspreadsheet)
[![Join the chat at https://gitter.im/PHPOffice/PhpSpreadsheet](https://img.shields.io/badge/GITTER-join%20chat-green.svg)](https://gitter.im/PHPOffice/PhpSpreadsheet)

PhpSpreadsheet is a library written in pure PHP and offers a set of classes that
allow you to read and write various spreadsheet file formats such as Excel and LibreOffice Calc.

## PHP version support

LTS: Support for PHP versions will only be maintained for a period of six months beyond the
[end of life of that PHP version](https://www.php.net/eol.php).

Currently the required PHP minimum version is PHP __7.3__.

See the `composer.json` for other requirements.

## Installation

Use [composer](https://getcomposer.org) to install PhpSpreadsheet into your project:

```sh
composer require phpoffice/phpspreadsheet
```

If you are building your installation on a development machine that is on a different PHP version to the server where it will be deployed, or if your PHP CLI version is not the same as your run-time such as `php-fpm` or Apache's `mod_php`, then you might want to add the following to your `composer.json` before installing:
```json lines
{
    "require": {
        "phpoffice/phpspreadsheet": "^1.23"
    },
    "config": {
        "platform": {
            "php": "7.3"
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

For Chart export, we support, which you will also need to install yourself
 - jpgraph/jpgraph

and then configure PhpSpreadsheet using:
```php
Settings::setChartRenderer(\PhpOffice\PhpSpreadsheet\Chart\Renderer\JpGraph::class);
```

You can `composer/require` the github version of jpgraph, but this was abandoned at version 4.0; or manually download the latest version that supports PHP 8 and above from [jpgraph.net](https://jpgraph.net/)

## Documentation

Read more about it, including install instructions, in the [official documentation](https://phpspreadsheet.readthedocs.io). Or check out the [API documentation](https://phpoffice.github.io/PhpSpreadsheet).

Please ask your support questions on [StackOverflow](https://stackoverflow.com/questions/tagged/phpspreadsheet), or have a quick chat on [Gitter](https://gitter.im/PHPOffice/PhpSpreadsheet).

## PHPExcel vs PhpSpreadsheet ?

PhpSpreadsheet is the next version of PHPExcel. It breaks compatibility to dramatically improve the code base quality (namespaces, PSR compliance, use of latest PHP language features, etc.).

Because all efforts have shifted to PhpSpreadsheet, PHPExcel will no longer be maintained. All contributions for PHPExcel, patches and new features, should target PhpSpreadsheet `master` branch.

Do you need to migrate? There is [an automated tool](/docs/topics/migration-from-PHPExcel.md) for that.

## License

PhpSpreadsheet is licensed under [MIT](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/LICENSE).
