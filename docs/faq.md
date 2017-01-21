# Frequently asked questions

The up-to-date F.A.Q. page for PHPExcel can be found on
<http://www.codeplex.com/PHPExcel/Wiki/View.aspx?title=FAQ&referringTitle=Requirements>.

## There seems to be a problem with character encoding...

It is necessary to use UTF-8 encoding for all texts in PhpSpreadsheet.
If the script uses different encoding then you can convert those texts
with PHP's iconv() or mb\_convert\_encoding() functions.

## PHP complains about ZipArchive not being found

Make sure you meet all requirements, especially php\_zip extension
should be enabled.

The ZipArchive class is only required when reading or writing formats
that use Zip compression (Xlsx and Ods). Since version 1.7.6 the PCLZip
library has been bundled with PhpSpreadsheet as an alternative to the
ZipArchive class.

This can be enabled by calling:

``` php
\PhpOffice\PhpSpreadsheet\Settings::setZipClass(\PhpOffice\PhpSpreadsheet\Settings::PCLZIP);
```

*before* calling the save method of the Xlsx Writer.

You can revert to using ZipArchive by calling:

``` php
\PhpOffice\PhpSpreadsheet\Settings::setZipClass(\PhpOffice\PhpSpreadsheet\Settings::ZIPARCHIVE);
```

At present, this only allows you to write Xlsx files without the need
for ZipArchive (not to read Xlsx or Ods)

## Excel 2007 cannot open the file generated on Windows

"Excel found unreadable content in '\*.xlsx'. Do you want to recover the
contents of this workbook? If you trust the source of this workbook,
click Yes."

Some older versions of the 5.2.x php\_zip extension on Windows contain
an error when creating ZIP files. The version that can be found on
<http://snaps.php.net/win32/php5.2-win32-latest.zip> should work at all
times.

Alternatively, upgrading to at least PHP 5.2.9 should solve the problem.

If you can't locate a clean copy of ZipArchive, then you can use the
PCLZip library as an alternative when writing Xlsx files, as described
above.

## Fatal error: Allowed memory size of xxx bytes exhausted (tried to allocate yyy bytes) in zzz on line aaa

PhpSpreadsheet holds an "in memory" representation of a spreadsheet, so
it is susceptible to PHP's memory limitations. The memory made available
to PHP can be increased by editing the value of the memory\_limit
directive in your php.ini file, or by using ini\_set('memory\_limit',
'128M') within your code (ISP permitting).

Some Readers and Writers are faster than others, and they also use
differing amounts of memory. You can find some indication of the
relative performance and memory usage for the different Readers and
Writers, over the different versions of PhpSpreadsheet, on the
[discussion
board](http://phpexcel.codeplex.com/Thread/View.aspx?ThreadId=234150).

If you've already increased memory to a maximum, or can't change your
memory limit, then [this
discussion](http://phpexcel.codeplex.com/Thread/View.aspx?ThreadId=242712)
on the board describes some of the methods that can be applied to reduce
the memory usage of your scripts using PhpSpreadsheet.

## Protection on my worksheet is not working?

When you make use of any of the worksheet protection features (e.g. cell
range protection, prohibiting deleting rows, ...), make sure you enable
worksheet security. This can for example be done like this:

``` php
$spreadsheet->getActiveSheet()->getProtection()->setSheet(true);
```

## Feature X is not working with Reader\_Y / Writer\_Z

Not all features of PhpSpreadsheet are implemented in all of the Reader
/ Writer classes. This is mostly due to underlying libraries not
supporting a specific feature or not having implemented a specific
feature.

For example autofilter is not implemented in PEAR
Spreadsheet\_Excel\_writer, which is the base of our Xls writer.

We are slowly building up a list of features, together with the
different readers and writers that support them, in the "Functionality
Cross-Reference.xls" file in the /Documentation folder.

## Formulas don't seem to be calculated in Excel2003 using compatibility pack?

This is normal behaviour of the compatibility pack, Xlsx displays this
correctly. Use \PhpOffice\PhpSpreadsheet\Writer\Xls if you really need
calculated values, or force recalculation in Excel2003.

## Setting column width is not 100% accurate

Trying to set column width, I experience one problem. When I open the
file in Excel, the actual width is 0.71 less than it should be.

The short answer is that PhpSpreadsheet uses a measure where padding is
included. See section: "Setting a column's width" for more details.

## How do I use PhpSpreadsheet with my framework

-   There are some instructions for using PhpSpreadsheet with Joomla on
    the [Joomla message
    board](http://http:/forum.joomla.org/viewtopic.php?f=304&t=433060)
-   A page of advice on using [PhpSpreadsheet in the Yii
    framework](http://www.yiiframework.com/wiki/101/how-to-use-phpexcel-external-library-with-yii/)
-   [The
    Bakery](http://bakery.cakephp.org/articles/melgior/2010/01/26/simple-excel-spreadsheet-helper)
    has some helper classes for reading and writing with PhpSpreadsheet
    within CakePHP
-   Integrating [PhpSpreadsheet into Kohana
    3](http://www.flynsarmy.com/2010/07/phpexcel-module-for-kohana-3/)
    and \[Интеграция PHPExcel и Kohana
    Framework\]\[http://szpargalki.blogspot.com/2011/02/phpexcel-kohana-framework.html\]
-   Using [PhpSpreadsheet with
    TYPO3](http://typo3.org/documentation/document-library/extension-manuals/phpexcel_library/1.1.1/view/toc/0/)

### Tutorials

-   [English PHPExcel tutorial](http://openxmldeveloper.org)
-   [French PHPExcel
    tutorial](http://g-ernaelsten.developpez.com/tutoriels/excel2007/)
-   [Russian PHPExcel Blog
    Postings](http://www.web-junior.net/sozdanie-excel-fajjlov-s-pomoshhyu-phpexcel/)
-   [A Japanese-language introduction to
    PHPExcel](http://journal.mycom.co.jp/articles/2009/03/06/phpexcel/index.html)

