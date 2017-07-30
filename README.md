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
