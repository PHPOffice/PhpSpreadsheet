# PhpSpreadsheet

Master:
[![Build Status](https://travis-ci.org/PHPOffice/PhpSpreadsheet.svg?branch=master)](https://travis-ci.org/PHPOffice/PhpSpreadsheet)
[![Code Quality](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/?branch=master)
[![Total Downloads](https://poser.pugx.org/phpoffice/phpspreadsheet/downloads.png)](https://packagist.org/packages/phpoffice/phpspreadsheet)
[![Latest Stable Version](https://poser.pugx.org/phpoffice/phpspreadsheet/v/stable.png)](https://packagist.org/packages/phpoffice/phpspreadsheet)
[![License](https://poser.pugx.org/phpoffice/phpspreadsheet/license.png)](https://packagist.org/packages/phpoffice/phpspreadsheet)
[![Join the chat at https://gitter.im/PHPOffice/PhpSpreadsheet](https://img.shields.io/badge/GITTER-join%20chat-green.svg)](https://gitter.im/PHPOffice/PhpSpreadsheet)

Develop:
[![Build Status](https://travis-ci.org/PHPOffice/PhpSpreadsheet.png?branch=develop)](http://travis-ci.org/PHPOffice/PhpSpreadsheet)
[![Code Quality](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/PHPOffice/PhpSpreadsheet/?branch=develop)

PhpSpreadsheet is a library written in pure PHP and providing a set of classes that allow you to read from and to write to different spreadsheet file formats, like Excel and LibreOffice Calc.

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
|PDF (using either the tcPDF, DomPDF or mPDF libraries, which need to be installed separately)|       |   ✓   |

## Requirements

 * PHP version 5.6 or higher
 * PHP extension php_zip enabled (required if you need PhpSpreadsheet to handle .xlsx .ods or .gnumeric files)
 * PHP extension php_xml enabled
 * PHP extension php_gd2 enabled (optional, but required for exact column width autocalculation)

*Note:* PHP 5.6.29 has [a bug](https://bugs.php.net/bug.php?id=73530) that
prevents SQLite3 caching to work correctly. Use a newer (or older) versions of
PHP if you need SQLite3 caching.

## PHP version support

Support for PHP versions will only be maintained for a period of six months beyond the end-of-life of that PHP version

## Want to contribute?

If you would like to contribute, here are some notes and guidelines:
 - All new development happens on feature/fix branches referenced with the GitHub issue number, and are then merged to the develop branch; so the develop branch is always the most up-to-date, working code
 - The master branch only contains tagged releases
 - If you are going to be submitting a pull request, please fork from develop, and submit your pull request back as a fix/feature branch referencing the GitHub issue number
 - Code changes must be validated by PHP-CS-Fixer and PHP_CodeSniffer (via `./vendor/bin/php-cs-fixer fix --verbose && ./vendor/bin/phpcs samples/ src/ tests/ --standard=PSR2 -n`)
 - [Helpful article about forking](https://help.github.com/articles/fork-a-repo/ "Forking a GitHub repository")
 - [Helpful article about pull requests](https://help.github.com/articles/using-pull-requests/ "Pull Requests")

## PHPExcel vs PhpSpreadsheet ?

PhpSpreadsheet is the next version of PHPExcel. It breaks compatibility to dramatically improve the code base quality (namespaces, PSR compliance, use of latest PHP language features, etc.).

Because all efforts have shifted to PhpSpreadsheet, PHPExcel will no longer be maintained. All contributions for PHPExcel, patches and new features, should target PhpSpreadsheet develop branch.

However PhpSpreadsheet is still unstable and not yet released. So if you need stability stick to PHPExcel until this project is released. If you prefer to live on the edge you can try to install this project [manually via composer](https://getcomposer.org/doc/05-repositories.md#loading-a-package-from-a-vcs-repository), but there is no guarantee and it will likely break again before an official release.

## License

PhpSpreadsheet is licensed under [LGPL (GNU LESSER GENERAL PUBLIC LICENSE)](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/license.md)
