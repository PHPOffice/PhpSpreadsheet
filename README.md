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

## Requirements
  * [Composer](https://getcomposer.org/) installed
<!---  (this is based on the old requirement on phpExcel)  
  * PHP version 5.2.0 or higher
  * PHP extension php_zip enabled (required if you need PHPExcel to handle .xlsx .ods or .gnumeric files)
  * PHP extension php_xml enabled
  * PHP extension php_gd2 enabled (optional, but required for exact column width autocalculation)
    *Note:* PHP 5.6.29 has [a bug](https://bugs.php.net/bug.php?id=73530) that
    prevents SQLite3 caching to work correctly. Use a newer (or older) versions of
    PHP if you need SQLite3 caching. --->

## Installation

PhpSpreadsheet is installed via Composer.

1.  via cli (command line interface) 

    `composer require phpoffice/phpspreadsheet`

1.  via composer.json, you just need to [add dependency](https://getcomposer.org/doc/04-schema.md) on PHPExcel into your package.

    Example:

  ```json
    {
        "require": {
           "phpoffice/phpspreadsheet": "^1.0.0"
        }
    }
  ```


## Documentation

Read more about it, including install instructions, in the official documentation, either at the online version:

https://phpspreadsheet.readthedocs.io

Or directly in this repository in the folder `docs/`.


## PHPExcel vs PhpSpreadsheet ?

PhpSpreadsheet is the next version of PHPExcel. It breaks compatibility to dramatically improve the code base quality (namespaces, PSR compliance, use of latest PHP language features, etc.).

Because all efforts have shifted to PhpSpreadsheet, PHPExcel will no longer be maintained. All contributions for PHPExcel, patches and new features, should target PhpSpreadsheet develop branch.

However PhpSpreadsheet is still unstable and not yet released. So if you need stability stick to PHPExcel until this project is released. If you prefer to live on the edge you can try to install this project [manually via composer](https://getcomposer.org/doc/05-repositories.md#loading-a-package-from-a-vcs-repository), but there is no guarantee and it will likely break again before an official release.

## License

PhpSpreadsheet is licensed under [LGPL (GNU LESSER GENERAL PUBLIC LICENSE)](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/license.md)
