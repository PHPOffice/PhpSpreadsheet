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

## File Formats supported

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

 * PHP version 5.5.0 or higher
 * PHP extension php_zip enabled (required if you need PhpSpreadsheet to handle .xlsx .ods or .gnumeric files)
 * PHP extension php_xml enabled
 * PHP extension php_gd2 enabled (optional, but required for exact column width autocalculation)

## PHP Version Support

 * Support for PHP versions will only be maintained for a period of six months beyond the end-of-life of that PHP version

## Want to contribute?

If you would like to contribute, here are some notes and guidlines:
 - All new development happens on feature/fix branches referenced with the github issue number, and are then merged to the develop branch; so the develop branch is always the most up-to-date, working code
 - The master branch only contains tagged releases
 - If you are going to be submitting a pull request, please fork from develop, and submit your pull request back as a fix/feature branch referencing the github issue number
 - Wherever possible, code changes should conform to PSR-2 standards
 - [Helpful article about forking](https://help.github.com/articles/fork-a-repo/ "Forking a Github repository")
 - [Helpful article about pull requests](https://help.github.com/articles/using-pull-requests/ "Pull Requests")


## License

PhpSpreadsheet is licensed under [LGPL (GNU LESSER GENERAL PUBLIC LICENSE)](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/license.md)
