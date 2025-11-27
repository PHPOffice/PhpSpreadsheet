# PhpSpreadsheet

[![Build Status](https://github.com/PHPOffice/PhpSpreadsheet/workflows/main/badge.svg)](https://github.com/PHPOffice/PhpSpreadsheet/actions)
[![Code Coverage](https://coveralls.io/repos/github/PHPOffice/PhpSpreadsheet/badge.svg?branch=master)](https://coveralls.io/github/PHPOffice/PhpSpreadsheet?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/PHPOffice/PhpSpreadsheet)](https://packagist.org/packages/phpoffice/phpspreadsheet)
[![Latest Stable Version](https://img.shields.io/github/v/release/PHPOffice/PhpSpreadsheet)](https://packagist.org/packages/phpoffice/phpspreadsheet)
[![License](https://img.shields.io/github/license/PHPOffice/PhpSpreadsheet)](https://packagist.org/packages/phpoffice/phpspreadsheet)
[![Join the chat at https://gitter.im/PHPOffice/PhpSpreadsheet](https://img.shields.io/badge/GITTER-join%20chat-green.svg)](https://gitter.im/PHPOffice/PhpSpreadsheet)

PhpSpreadsheet is a library written in pure PHP and offers a set of classes that
allow you to read and write various spreadsheet file formats such as Excel and LibreOffice Calc.

This branch (2.4.x) is maintained (for security and some bug fixes), but it is *not* the latest version of PhpSpreadsheet, and may therefore lack features and bug fixes found in the latest version.

## PHP Version Support

This branch runs with Php 8.1, 8.2, 8.3, 8.4, or 8.5. 8.5 is the last version of Php which can be used with this branch.

LTS: For maintained branches, support for PHP versions will only be maintained for a period of six months beyond the
[end of life](https://www.php.net/supported-versions) of that PHP version.

Currently the required PHP minimum version is PHP __8.1__, and we [will support that version](https://www.php.net/supported-versions.php) until 30th June 2026.

See the `composer.json` for other requirements.

## Installation

See the [install instructions](https://phpspreadsheet.readthedocs.io/en/latest/#installation).

## Documentation

Read more about it, including install instructions, in the [official documentation](https://phpspreadsheet.readthedocs.io). Or check out the [API documentation](https://phpoffice.github.io/PhpSpreadsheet).

Please ask your support questions on [StackOverflow](https://stackoverflow.com/questions/tagged/phpspreadsheet), or have a quick chat on [Gitter](https://gitter.im/PHPOffice/PhpSpreadsheet).

## Patreon

I am now running a [Patreon](https://www.patreon.com/MarkBaker) to support the work that I do on PhpSpreadsheet.

Supporters will receive access to articles about working with PhpSpreadsheet, and how to use some of its more advanced features.

Posts already available to Patreon supporters:
 - The Dating Game
   - A  look at how MS Excel (and PhpSpreadsheet) handle date and time values.
- Looping the Loop
    - Advice on Iterating through the rows and cells in a worksheet.

And for Patrons at levels actively using PhpSpreadsheet:
 - Behind the Mask
   - A look at Number Format Masks.

The Next Article (currently Work in Progress):
 - Formula for Success
   - How to debug formulae that don't produce the expected result.


My aim is to post at least one article each month, taking a detailed look at some feature of MS Excel and how to use that feature in PhpSpreadsheet, or on how to perform different activities in PhpSpreadsheet.

Planned posts for the future include topics like:
 - Tables
 - Structured References
 - AutoFiltering
 - Array Formulae
 - Conditional Formatting
 - Data Validation
 - Value Binders
 - Images
 - Charts

After a period of six months exclusive to Patreon supporters, articles will be incorporated into the public documentation for the library.

## PHPExcel vs PhpSpreadsheet ?

PhpSpreadsheet is the next version of PHPExcel. It breaks compatibility to dramatically improve the code base quality (namespaces, PSR compliance, use of latest PHP language features, etc.).

Because all efforts have shifted to PhpSpreadsheet, PHPExcel will no longer be maintained. All contributions for PHPExcel, patches and new features, should target PhpSpreadsheet `master` branch.

Do you need to migrate? There is [an automated tool](/docs/topics/migration-from-PHPExcel.md) for that.

## License

PhpSpreadsheet is licensed under [MIT](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/LICENSE).
