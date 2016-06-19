# PHPSpreadsheet - OpenXML - Read, Write and Create spreadsheet documents in PHP - Spreadsheet engine

[![Join the chat at https://gitter.im/PHPOffice/PhpSpreadsheet](https://badges.gitter.im/PHPOffice/PhpSpreadsheet.svg)](https://gitter.im/PHPOffice/PhpSpreadsheet?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
PHPSpreadsheet is a library written in pure PHP and providing a set of classes that allow you to write to and read from different spreadsheet file formats, like Excel (BIFF) .xls, Excel 2007 (OfficeOpenXML) .xlsx, CSV, Libre/OpenOffice Calc .ods, Gnumeric, PDF, HTML, ... This project is built around Microsoft's OpenXML standard and PHP.

Master: [![Build Status](https://travis-ci.org/PHPOffice/PhpSpreadsheet.png?branch=master)](http://travis-ci.org/PHPOffice/PhpSpreadsheet)

Develop: [![Build Status](https://travis-ci.org/PHPOffice/PhpSpreadsheet.png?branch=develop)](http://travis-ci.org/PHPOffice/PhpSpreadsheet)

[![Join the chat at https://gitter.im/PHPOffice/PhpSpreadsheet](https://img.shields.io/badge/GITTER-join%20chat-green.svg)](https://gitter.im/PHPOffice/PhpSpreadsheet)

## File Formats supported

### Reading
 * BIFF 5-8 (.xls) Excel 95 and above
 * Office Open XML (.xlsx) Excel 2007 and above
 * SpreadsheetML (.xml) Excel 2003
 * Open Document Format/OASIS (.ods)
 * Gnumeric
 * HTML
 * SYLK
 * CSV

### Writing
 * BIFF 8 (.xls) Excel 95 and above
 * Office Open XML (.xlsx) Excel 2007 and above
 * HTML
 * CSV
 * PDF (using either the tcPDF, DomPDF or mPDF libraries, which need to be installed separately)


## Requirements
 * PHP version 5.5.0 or higher
 * PHP extension php_zip enabled (required if you need PHPSpreadsheet to handle .xlsx .ods or .gnumeric files)
 * PHP extension php_xml enabled
 * PHP extension php_gd2 enabled (optional, but required for exact column width autocalculation)


## Want to contribute?

If you would like to contribute, here are some notes and guidlines:
 - All new development happens on the 1.9 branch, so it is always the most up-to-date
 - The master branch only contains tagged releases
 - If you are going to be submitting a pull request, please fork from 1.9, and submit your pull request back to that 1.9 branch
 - Wherever possible, code changes should conform to PSR-2 standards
 - [Helpful article about forking](https://help.github.com/articles/fork-a-repo/ "Forking a Github repository")
 - [Helpful article about pull requests](https://help.github.com/articles/using-pull-requests/ "Pull Requests")


## License
PHPSpreadsheet is licensed under [LGPL (GNU LESSER GENERAL PUBLIC LICENSE)](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/license.md)
