# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

- Initial implementation of SUMIFS() function
- Additional codepages
- MemoryDrawing not working in HTML writer [#808](https://github.com/PHPOffice/PHPExcel/issues/808)

### Changed

- Start following [SemVer](http://semver.org) properly.

### Bugfixes

- Fix to getCell() method when cell reference includes a worksheet reference - @MarkBaker
- Ignore inlineStr type if formula element exists - @ncrypthic [#570](https://github.com/PHPOffice/PHPExcel/issues/570)
- Excel 2007 Reader freezes because of conditional formatting - @rentalhost [#575](https://github.com/PHPOffice/PHPExcel/issues/575)

### General

- Whitespace after toRichTextObject() - @MarkBaker [#554](https://github.com/PHPOffice/PHPExcel/issues/554)
- Optimize vlookup() sort - @umpirsky [#548](https://github.com/PHPOffice/PHPExcel/issues/548)
- c:max and c:min elements shall NOT be inside c:orientation elements - @vitalyrepin [#869](https://github.com/PHPOffice/PHPExcel/pull/869)
- Implement actual timezone adjustment into PHPExcel_Shared_Date::PHPToExcel - @sim642 [#489](https://github.com/PHPOffice/PHPExcel/pull/489)

### BREAKING CHANGE

- Introduction of namespaces for all classes, eg: `PHPExcel_Calculation_Functions` becomes `PhpOffice\PhpSpreadsheet\Calculation\Functions`
- Some classes were renamed for clarity and/or consistency:

For a comprehensive list of all class changes, and a semi-automated migration path, read the [migration guide](./docs/Migration-from-PHPExcel.md).

- Dropped `PHPExcel_Calculation_Functions::VERSION()`. Composer or git should be used to know the version.
- Dropped `PHPExcel_Settings::setPdfRenderer()` and `PHPExcel_Settings::setPdfRenderer()`. Composer should be used to autoload PDF libs.


## [1.8.1] - 2015-04-30

### Bugfixes

- Fix for Writing an Open Document cell with non-numeric formula - @goncons [#397](https://github.com/PHPOffice/PHPExcel/issues/397)
- Avoid potential divide by zero in basedrawing - @sarciszewski [#329](https://github.com/PHPOffice/PHPExcel/issues/329)
- XML External Entity (XXE) Processing, different behaviour between simplexml_load_string() and simplexml_load_file(). - @ymaerschalck [#405](https://github.com/PHPOffice/PHPExcel/issues/405)
- Fix to ensure that current cell is maintained when executing formula calculations - @MarkBaker
- Keep/set the value on Reader _loadSheetsOnly as NULL, courtesy of Restless-ET - @MarkBaker [#350](https://github.com/PHPOffice/PHPExcel/issues/350)
- Loading an Excel 2007 spreadsheet throws an "Autofilter must be set on a range of cells" exception - @MarkBaker [CodePlex #18105](https://phpexcel.codeplex.com/workitem/18105)
- Fix to autoloader registration for backward compatibility with PHP 5.2.0 not accepting the prepend flag - @MarkBaker [#388](https://github.com/PHPOffice/PHPExcel/issues/388)
- DOM loadHTMLFile() failing with options flags when using PHP < 5.4.0 - @MarkBaker [#384](https://github.com/PHPOffice/PHPExcel/issues/384)
- Fix for percentage operator in formulae for BIFF Writer - @MarkBaker
- Fix to getStyle() call for cell object - @MarkBaker
- Discard Autofilters in Excel2007 Reader when filter range isn't a valid range - @MarkBaker
- Fix invalid NA return in VLOOKUP - @frozenstupidity [#423](https://github.com/PHPOffice/PHPExcel/issues/423)
- "No Impact" conditional formatting fix for NumberFormat - @wiseloren [CodePlex #21454](https://phpexcel.codeplex.com/workitem/21454)
- Bug in Excel2003XML reader, parsing merged cells - @bobwitlox [#467](https://github.com/PHPOffice/PHPExcel/issues/467)
- Fix for CEIL() and FLOOR() when number argument is zero - @MarkBaker [#302](https://github.com/PHPOffice/PHPExcel/issues/302)

### General

- Remove cells cleanly when calling RemoveRow() or RemoveColumn() - @MarkBaker
- Small performance improvement for autosize columns - @MarkBaker
- Change the getter/setter for zeroHeight to camel case - @frost-nzcr4 [#379](https://github.com/PHPOffice/PHPExcel/issues/379)
- DefaultValueBinder is too much aggressive when converting string to numeric - @MarkBaker [#394](https://github.com/PHPOffice/PHPExcel/issues/394)
- Default precalculate formulas to false for writers - @MarkBaker
- Set default Cyclic Reference behaviour to 1 to eliminate exception when using a single cyclic iteration in formulae - @MarkBaker

### Features

- Some Excel writer libraries erroneously use Codepage 21010 for UTF-16LE - @MarkBaker [#396](https://github.com/PHPOffice/PHPExcel/issues/396)
- Methods to manage most of the existing options for Chart Axis, Major Grid-lines and Minor Grid-lines - @WiktrzGE [#404](https://github.com/PHPOffice/PHPExcel/issues/404)
- ODS read/write comments in the cell - @frost-nzcr4 [#403](https://github.com/PHPOffice/PHPExcel/issues/403)
- Additional Mac CJK codepage definitions - @CQD [#389](https://github.com/PHPOffice/PHPExcel/issues/389)
- Update Worksheet.php getStyleByColumnAndRow() to allow a range of cells rather than just a single cell - @bolovincev [#269](https://github.com/PHPOffice/PHPExcel/issues/269)
- New methods added for testing cell status within merge groups - @MarkBaker
- Handling merge cells in HTML Reader - @cifren/MBaker [#205](https://github.com/PHPOffice/PHPExcel/issues/205)
- Helper to convert basic HTML markup to a Rich Text object - @MarkBaker
- Improved Iterators - @MarkBaker
    - New Column Iterator
    - Support for row and column ranges
    - Improved handling for next/prev

### Security

- XML filescan in XML-based Readers to prevent XML Entity Expansion (XEE) - @MarkBaker
    - (see http://projects.webappsec.org/w/page/13247002/XML%20Entity%20Expansion for an explanation of XEE injection) attacks
    - Reference CVE-2015-3542 - Identification of problem courtesy of Dawid Golunski (Pentest Ltd.)

## [1.8.0] - 2014-03-02

### Bugfixes

- Undefined variable: fileHandle in CSV Reader - @MarkBaker [CodePlex #19830](https://phpexcel.codeplex.com/workitem/19830)
- Out of memory in style/supervisor.php - @MarkBaker [CodePlex #19968](https://phpexcel.codeplex.com/workitem/19968)
- Style error with merged cells in PDF Writer - @MarkBaker
- Problem with cloning worksheets - @MarkBaker
- Bug fix reading Open Office files - @tavoarcila [#259](https://github.com/PHPOffice/PHPExcel/issues/259)
- Serious bug in absolute cell reference used in shared formula - @MarkBaker [CodePlex #20397](https://phpexcel.codeplex.com/workitem/20397)
    - Would also have affected insert/delete column/row- CHOOSE() returns "#VALUE!" if the 1st entry is chosen - @RomanSyroeshko [#267](https://github.com/PHPOffice/PHPExcel/issues/267)
- When duplicating styles, styles shifted by one column to the right - @Gemorroj [#268](https://github.com/PHPOffice/PHPExcel/issues/268)
    - Fix also applied to duplicating conditional styles- Fix for formulae that reference a sheet whose name begins with a digit: - @IndrekHaav [#212](https://github.com/PHPOffice/PHPExcel/issues/212)
    - these were erroneously identified as numeric values, causing the parser to throw an undefined variable error.- Fixed undefined variable error due to $styleArray being used before it's initialised - @IndrekHaav [CodePlex #16208](https://phpexcel.codeplex.com/workitem/16208)
- ISTEXT() return wrong result if referencing an empty but formatted cell - @PowerKiKi [#273](https://github.com/PHPOffice/PHPExcel/issues/273)
- Binary comparison of strings are case insensitive - @PowerKiKi [#270](https://github.com/PHPOffice/PHPExcel/issues/270), [#31](https://github.com/PHPOffice/PHPExcel/issues/31)
- Insert New Row/Column Before is not correctly updating formula references - @MarkBaker [#275](https://github.com/PHPOffice/PHPExcel/issues/275)
- Passing an array of cells to _generateRow() in the HTML/PDF Writer causes caching problems with last cell in the range - @MarkBaker [#257](https://github.com/PHPOffice/PHPExcel/issues/257)
- Fix to empty worksheet garbage collection when using cell caching - @MarkBaker [#193](https://github.com/PHPOffice/PHPExcel/issues/193)
- Excel2007 does not correctly mark rows as hidden - @Jazzo [#248](https://github.com/PHPOffice/PHPExcel/issues/248)
- Fixed typo in Chart/Layout set/getYMode() - @Roy Shahbazian [#299](https://github.com/PHPOffice/PHPExcel/issues/299)
- Fatal error: Call to a member function cellExists() line: 3327 in calculation.php if referenced worksheet doesn't exist - @EliuFlorez [#279](https://github.com/PHPOffice/PHPExcel/issues/279)
- AdvancedValueBinder "Division by zero"-error - @MarkBaker [#290](https://github.com/PHPOffice/PHPExcel/issues/290)
- Adding Sheet to Workbook Bug - @MarkBaker [CodePlex #20604](https://phpexcel.codeplex.com/workitem/20604)
- Calculation engine incorrectly evaluates empty cells as #VALUE - @MarkBaker [CodePlex #20703](https://phpexcel.codeplex.com/workitem/20703)
- Formula references to cell on another sheet in ODS files - @MarkBaker [CodePlex #20760](https://phpexcel.codeplex.com/workitem/20760)

### Features

- LibreOffice created XLSX files results in an empty file. - @MarkBaker [#321](https://github.com/PHPOffice/PHPExcel/issues/321), [#158](https://github.com/PHPOffice/PHPExcel/issues/158), [CodePlex #17824](https://phpexcel.codeplex.com/workitem/17824)
- Implementation of the Excel HLOOKUP() function - @amerov
- Added "Quote Prefix" to style settings (Excel2007 Reader and Writer only) - @MarkBaker
- Added Horizontal FILL alignment for Excel5 and Excel2007 Readers/Writers, and Horizontal DISTRIBUTED alignment for Excel2007 Reader/Writer - @MarkBaker
- Add support for reading protected (RC4 encrypted) .xls files - @trvrnrth [#261](https://github.com/PHPOffice/PHPExcel/issues/261)

### General

- Adding support for macros, Ribbon in Excel 2007 - @LWol [#252](https://github.com/PHPOffice/PHPExcel/issues/252)
- Remove array_shift in ReferenceHelper::insertNewBefore improves column or row delete speed - @cdhutch [CodePlex #20055](https://phpexcel.codeplex.com/workitem/20055)
- Improve stock chart handling and rendering, with help from Swashata Ghosh - @MarkBaker
- Fix to calculation properties for Excel2007 so that the opening application will only recalculate on load if it's actually required - @MarkBaker
- Modified Excel2007 Writer to default preCalculateFormulas to false - @MarkBaker
    - Note that autosize columns will still recalculate affected formulae internally- Functionality to getHighestRow() for a specified column, and getHighestColumn() for a specified row - @dresenhista [#242](https://github.com/PHPOffice/PHPExcel/issues/242)
- Modify PHPExcel_Reader_Excel2007 to use zipClass from PHPExcel_Settings::getZipClass() - @adamriyadi [#247](https://github.com/PHPOffice/PHPExcel/issues/247)
    - This allows the use of PCLZip when reading for people that don't have access to ZipArchive
### Security

- Convert properties to string in OOCalc reader - @infojunkie [#276](https://github.com/PHPOffice/PHPExcel/issues/276)
- Disable libxml external entity loading by default. - @maartenba [#322](https://github.com/PHPOffice/PHPExcel/issues/322)
    - This is to prevent XML External Entity Processing (XXE) injection attacks (see http://websec.io/2012/08/27/Preventing-XEE-in-PHP.html for an explanation of XXE injection).
    - Reference CVE-2014-2054

## [1.7.9] - 2013-06-02

### Features

- Include charts option for HTML Writer - @MarkBaker
- Added composer file - @MarkBaker
- cache_in_memory_gzip "eats" last worksheet line, cache_in_memory doesn't - @MarkBaker [CodePlex #18844](https://phpexcel.codeplex.com/workitem/18844)
- echo statements in HTML.php - @MarkBaker [#104](https://github.com/PHPOffice/PHPExcel/issues/104)

### Bugfixes

- Added getStyle() method to Cell object - @MarkBaker
- Error in PHPEXCEL/Calculation.php script on line 2976 (stack pop check) - @Asker [CodePlex #18777](https://phpexcel.codeplex.com/workitem/18777)
- CSV files without a file extension being identified as HTML - @MarkBaker [CodePlex #18794](https://phpexcel.codeplex.com/workitem/18794)
- Wrong check for maximum number of rows in Excel5 Writer - @AndreKR [#66](https://github.com/PHPOffice/PHPExcel/issues/66)
- Cache directory for DiscISAM cache storage cannot be set - @MarkBaker [#67](https://github.com/PHPOffice/PHPExcel/issues/67)
- Fix to Excel2007 Reader for hyperlinks with an anchor fragment (following a #), otherwise they were treated as sheet references - @MarkBaker [CodePlex #17976](https://phpexcel.codeplex.com/workitem/17976)
- getSheetNames() fails on numeric (floating point style) names with trailing zeroes - @MarkBaker [CodePlex #18963](https://phpexcel.codeplex.com/workitem/18963)
- Modify cell's getCalculatedValue() method to return the content of RichText objects rather than the RichText object itself - @MarkBaker
- Fixed formula/formatting bug when removing rows - @techhead [#70](https://github.com/PHPOffice/PHPExcel/issues/70)
- Fix to cellExists for non-existent namedRanges - @alexgann [#63](https://github.com/PHPOffice/PHPExcel/issues/63)
- Sheet View in Excel5 Writer - @Progi1984 [#22](https://github.com/PHPOffice/PHPExcel/issues/22)
- PHPExcel_Worksheet::getCellCollection() may not return last cached cell - @amironov [#82](https://github.com/PHPOffice/PHPExcel/issues/82)
- Rich Text containing UTF-8 characters creating unreadable content with Excel5 Writer - @teso [CodePlex #18551](https://phpexcel.codeplex.com/workitem/18551)
- Work item GH-8/CP11704 : Conditional formatting in Excel 5 Writer - @Progi1984
- canRead() Error for GoogleDocs ODS files: in ODS files from Google Docs there is no mimetype file - @MarkBaker [#113](https://github.com/PHPOffice/PHPExcel/issues/113)
- "Sheet index is out of bounds." Exception - @MarkBaker [#80](https://github.com/PHPOffice/PHPExcel/issues/80)
- Fixed number format fatal error - @ccorliss [#105](https://github.com/PHPOffice/PHPExcel/issues/105)
- Add DROP TABLE in destructor for SQLite and SQLite3 cache controllers - @MarkBaker
- Fix merged-cell borders on HTML/PDF output - @alexgann [#154](https://github.com/PHPOffice/PHPExcel/issues/154)
- Fix: Hyperlinks break when removing rows - @Shanto [#161](https://github.com/PHPOffice/PHPExcel/issues/161)
- Fix Extra Table Row From Images and Charts - @neclimdul [#166](https://github.com/PHPOffice/PHPExcel/issues/166)

### General

- Single cell print area - @MarkBaker [#130](https://github.com/PHPOffice/PHPExcel/issues/130)
- Improved AdvancedValueBinder for currency - @kea [#69](https://github.com/PHPOffice/PHPExcel/issues/69)
- Fix for environments where there is no access to /tmp but to upload_tmp_dir - @MarkBaker
    - Provided an option to set the sys_get_temp_dir() call to use the upload_tmp_dir; though by default the standard temp directory will still be used- Search style by identity in PHPExcel_Worksheet::duplicateStyle() - @amironov  [#84](https://github.com/PHPOffice/PHPExcel/issues/84)
- Fill SheetView IO in Excel5 - @karak [#85](https://github.com/PHPOffice/PHPExcel/issues/85)
- Memory and Speed improvements in PHPExcel_Reader_Excel5 - @cfhay [CodePlex #18958](https://phpexcel.codeplex.com/workitem/18958)
- Modify listWorksheetNames() and listWorksheetInfo to use XMLReader with streamed XML rather than SimpleXML - @MarkBaker [#78](https://github.com/PHPOffice/PHPExcel/issues/78)
- Restructuring of PHPExcel Exceptions - @dbonsch
- Refactor Calculation Engine from singleton to a Multiton - @MarkBaker
    - Ensures that calculation cache is maintained independently for different workbooks

## [1.7.8] - 2012-10-12

### Features

- Phar builder script to add phar file as a distribution option - @kkamkou
- Refactor PDF Writer to allow use with a choice of PDF Rendering library - @MarkBaker
    - rather than restricting to tcPDF
    - Current options are tcPDF, mPDF, DomPDF
    - tcPDF Library has now been removed from the deployment bundle- Initial version of HTML Reader - @MarkBaker
- Implement support for AutoFilter in PHPExcel_Writer_Excel5 - @Progi1984
- Modified ERF and ERFC Engineering functions to accept Excel 2010's modified acceptance of negative arguments - @MarkBaker
- Support SheetView `view` attribute (Excel2007) - @k1LoW
- Excel compatibility option added for writing CSV files - @MarkBaker
    - While Excel 2010 can read CSV files with a simple UTF-8 BOM, Excel2007 and earlier require UTF-16LE encoded tab-separated files.
    - The new setExcelCompatibility(TRUE) option for the CSV Writer will generate files with this formatting for easy import into Excel2007 and below.- Language implementations for Turkish (tr) - @MarkBaker
- Added fraction tests to advanced value binder - @MarkBaker

### General

- Allow call to font setUnderline() for underline format to specify a simple boolean for UNDERLINE_NONE or UNDERLINE_SINGLE - @MarkBaker
- Add Currency detection to the Advanced Value Binder - @alexgann
- setCellValueExplicitByColumnAndRow() do not return PHPExcel_Worksheet - @MarkBaker [CodePlex #18404](https://phpexcel.codeplex.com/workitem/18404)
- Reader factory doesn't read anymore XLTX and XLT files - @MarkBaker [CodePlex #18324](https://phpexcel.codeplex.com/workitem/18324)
- Magic __toString() method added to Cell object to return raw data value as a string - @MarkBaker
- Add cell indent to html rendering - @alexgann

### Bugfixes

- ZeroHeight for rows in sheet format - @Raghav1981
- OOCalc cells containing <text:span> inside the <text:p> tag - @cyberconte
- Fix to listWorksheetInfo() method for OOCalc Reader - @schir1964
- Support for "e" (epoch) date format mask - @MarkBaker
    - Rendered as a 4-digit CE year in non-Excel outputs- Background color cell is always black when editing cell - @MarkBaker
- Allow "no impact" to formats on Conditional Formatting - @MarkBaker
- OOCalc Reader fix for NULL cells - @wackonline
- Fix to excel2007 Chart Writer when a $plotSeriesValues is empty - @seltzlab
- Various fixes to Chart handling - @MarkBaker
- Error loading xlsx file with column breaks - @MarkBaker [CodePlex #18370](https://phpexcel.codeplex.com/workitem/18370)
- OOCalc Reader now handles percentage and currency data types - @MarkBaker
- mb_stripos empty delimiter - @MarkBaker
- getNestingLevel() Error on Excel5 Read - @takaakik
- Fix to Excel5 Reader when cell annotations are defined before their referenced text objects - @MarkBaker
- OOCalc Reader modified to process number-rows-repeated - @MarkBaker
- Chart Title compatibility on Excel 2007 - @MarkBaker [CodePlex #18377](https://phpexcel.codeplex.com/workitem/18377)
- Chart Refresh returning cell reference rather than values - @MarkBaker [CodePlex #18146](https://phpexcel.codeplex.com/workitem/18146)
- Autoshape being identified in twoCellAnchor when includeCharts is TRUE triggering load error - @MarkBaker [CodePlex #18145](https://phpexcel.codeplex.com/workitem/18145)
- v-type texts for series labels now recognised and parsed correctly - @MarkBaker [CodePlex #18325](https://phpexcel.codeplex.com/workitem/18325)
- load file failed if the file has no extensionType - @wolf5x [CodePlex #18492](https://phpexcel.codeplex.com/workitem/18492)
- Pattern fill colours in Excel2007 Style Writer - @dverspui
- Excel2007 Writer order of font style elements to conform with Excel2003 using compatibility pack - @MarkBaker
- Problems with $_activeSheetIndex when decreased below 0. - @MarkBaker [CodePlex #18425](https://phpexcel.codeplex.com/workitem/18425)
- PHPExcel_CachedObjectStorage_SQLite3::cacheMethodIsAvailable() uses class_exists - autoloader throws error - @MarkBaker [CodePlex #18597](https://phpexcel.codeplex.com/workitem/18597)
- Cannot access private property PHPExcel_CachedObjectStorageFactory::$_cacheStorageMethod - @MarkBaker [CodePlex #18598](https://phpexcel.codeplex.com/workitem/18598)
- Data titles for charts - @MarkBaker [CodePlex #18397](https://phpexcel.codeplex.com/workitem/18397)
    - PHPExcel_Chart_Layout now has methods for getting/setting switches for displaying/hiding chart data labels- Discard single cell merge ranges when reading (stupid that Excel allows them in the first place) - @MarkBaker
- Discard hidden autoFilter named ranges - @MarkBaker


## [1.7.7] - 2012-05-19

### Bugfixes

- Support for Rich-Text in PHPExcel_Writer_Excel5 - @Progi1984 [CodePlex #8916](https://phpexcel.codeplex.com/workitem/8916)
- Change iterators to implement Iterator rather than extend CachingIterator, as a fix for PHP 5.4. changes in SPL - @MarkBaker
- Invalid cell coordinate in Autofilter for Excel2007 Writer - @MarkBaker [CodePlex #15459](https://phpexcel.codeplex.com/workitem/15459)
- PCLZip library issue - @MarkBaker [CodePlex #15518](https://phpexcel.codeplex.com/workitem/15518)
- Excel2007 Reader canRead function bug - @MarkBaker [CodePlex #15537](https://phpexcel.codeplex.com/workitem/15537)
- Support for Excel functions whose return can be used as either a value or as a cell reference depending on its context within a formula - @MarkBaker
- ini_set() call in Calculation class destructor - @gilles06 [CodePlex #15707](https://phpexcel.codeplex.com/workitem/15707)
- RangeToArray strange array keys - @MarkBaker [CodePlex #15786](https://phpexcel.codeplex.com/workitem/15786)
- INDIRECT() function doesn't work with named ranges - @MarkBaker [CodePlex #15762](https://phpexcel.codeplex.com/workitem/15762)
- Locale-specific fix to text functions when passing a boolean argument instead of a string - @MarkBaker
- reader/CSV fails on this file - @MarkBaker [CodePlex #16246](https://phpexcel.codeplex.com/workitem/16246)
    - auto_detect_line_endings now set in CSV reader- $arguments improperly used in CachedObjectStorage/PHPTemp.php - @MarkBaker [CodePlex #16212](https://phpexcel.codeplex.com/workitem/16212)
- Bug In Cache System (cell reference when throwing caching errors) - @MarkBaker [CodePlex #16643](https://phpexcel.codeplex.com/workitem/16643)
- PHP Invalid index notice on writing excel file when active sheet has been deleted - @MarkBaker [CodePlex #16895](https://phpexcel.codeplex.com/workitem/16895)
- External links in Excel2010 files cause Fatal error - @MarkBaker [CodePlex #16956](https://phpexcel.codeplex.com/workitem/16956)
- Previous calculation engine error conditions trigger cyclic reference errors - @MarkBaker [CodePlex #16960](https://phpexcel.codeplex.com/workitem/16960)
- PHPExcel_Style::applyFromArray() returns null rather than style object in advanced mode - @mkopinsky [CodePlex #16266](https://phpexcel.codeplex.com/workitem/16266)
- Cell::getFormattedValue returns RichText object instead of string - @fauvel [CodePlex #16958](https://phpexcel.codeplex.com/workitem/16958)
- Indexed colors do not refer to Excel's indexed colors? - @MarkBaker [CodePlex #17166](https://phpexcel.codeplex.com/workitem/17166)
- Indexed colors should be consistent with Excel and start from 1 (current index starts at 0) - @MarkBaker [CodePlex #17199](https://phpexcel.codeplex.com/workitem/17199)
- Named Range definition in .xls when sheet reeference is quote wrapped - @MarkBaker [CodePlex #17262](https://phpexcel.codeplex.com/workitem/17262)
- duplicateStyle() method doesn't duplicate conditional formats - @MarkBaker [CodePlex #17403](https://phpexcel.codeplex.com/workitem/17403)
    - Added an equivalent duplicateConditionalStyle() method for duplicating conditional styles- =sumproduct(A,B) <> =sumproduct(B,A) in xlsx - @bnr [CodePlex #17501](https://phpexcel.codeplex.com/workitem/17501)

### Features

- OOCalc cells contain same data bug? - @cyberconte [CodePlex #17471](https://phpexcel.codeplex.com/workitem/17471)
- listWorksheetInfo() method added to Readers... courtesy of Christopher Mullins - @schir1964
- Options for cell caching using Igbinary and SQLite/SQlite3. - @MarkBaker
- Additional row iterator options: allow a start row to be defined in the constructor; seek(), and prev() methods added. - @MarkBaker
- Implement document properties in Excel5 writer - @Progi1984 [CodePlex #9759](https://phpexcel.codeplex.com/workitem/9759)

### General

- Implement chart functionality (EXPERIMENTAL) - @MarkBaker [CodePlex #16](https://phpexcel.codeplex.com/workitem/16)
    - Initial definition of chart objects.
    - Reading Chart definitions through the Excel2007 Reader
    - Facility to render charts to images using the 3rd-party jpgraph library
    - Writing Charts using the Excel2007 Writer- Fix to build to ensure that Examples are included with the documentation - @MarkBaker
- Reduce cell caching overhead using dirty flag to ensure that cells are only rewritten to the cache if they have actually been changed - @MarkBaker
- Improved memory usage in CSV Writer - @MarkBaker
- Improved speed and memory usage in Excel5 Writer - @MarkBaker
- Experimental - @MarkBaker
    - Added getHighestDataColumn(), getHighestDataRow(), getHighestRowAndColumn() and calculateWorksheetDataDimension() methods for the worksheet that return the highest row and column that have cell records- Support for Rich-Text in PHPExcel_Writer_Excel5 - @Progi1984 [CodePlex #8916](https://phpexcel.codeplex.com/workitem/8916)
- Two easy to fix Issues concerning PHPExcel_Token_Stack (l10n/UC) - @MarkBaker [CodePlex #15405](https://phpexcel.codeplex.com/workitem/15405)
- Locale file paths not fit for windows - @MarkBaker [CodePlex #15461](https://phpexcel.codeplex.com/workitem/15461)
- Add file directory as a cache option for cache_to_discISAM - @MarkBaker [CodePlex #16643](https://phpexcel.codeplex.com/workitem/16643)
- Datatype.php & constant TYPE_NULL - @MarkBaker [CodePlex #16923](https://phpexcel.codeplex.com/workitem/16923)
- Ensure use of system temp directory for all temporary work files, unless explicitly specified - @MarkBaker
- [Patch] faster stringFromColumnIndex() - @char101 [CodePlex #16359](https://phpexcel.codeplex.com/workitem/16359)
- Fix for projects that still use old autoloaders - @whit1206 [CodePlex #16028](https://phpexcel.codeplex.com/workitem/16028)
- Unknown codepage: 10007 - @atz [CodePlex #17024](https://phpexcel.codeplex.com/workitem/17024)
    - Additional Mac codepages

## [1.7.6] - 2011-02-27

### Features

- Provide option to use PCLZip as an alternative to ZipArchive. - @MarkBaker
    - This allows the writing of Excel2007 files, even without ZipArchive enabled (it does require zlib), or when php_zip is one of the buggy PHP 5.2.6 or 5.2.8 versions
    - It can be enabled using PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
    - Note that it is not yet implemented as an alternative to ZipArchive for those Readers that are extracting from zips- Added listWorksheetNames() method to Readers that support multiple worksheets in a workbook, allowing a user to extract a list of all the worksheet names from a file without parsing/loading the whole file. - @MarkBaker [CodePlex #14979](https://phpexcel.codeplex.com/workitem/14979)
- Speed boost and memory reduction in the Worksheet toArray() method. - @MarkBaker
- Added new rangeToArray() and namedRangeToArray() methods to the PHPExcel_Worksheet object. - @MarkBaker
    - Functionally, these are identical to the toArray() method, except that they take an additional first parameter of a Range (e.g. 'B2:C3') or a Named Range name.
    - Modified the toArray() method so that it actually uses rangeToArray().- Added support for cell comments in the OOCalc, Gnumeric and Excel2003XML Readers, and in the Excel5 Reader - @MarkBaker
- Improved toFormattedString() handling for Currency and Accounting formats to render currency symbols - @MarkBaker

### Bugfixes

- Implement more Excel calculation functions - @MarkBaker
    - Implemented the DAVERAGE(), DCOUNT(), DCOUNTA(), DGET(), DMAX(), DMIN(), DPRODUCT(), DSTDEV(), DSTDEVP(), DSUM(), DVAR() and DVARP() Database functions- Simple =IF() formula disappears - @MarkBaker [CodePlex #14888](https://phpexcel.codeplex.com/workitem/14888)
- PHP Warning: preg_match(): Compilation failed: PCRE does not support \\L, \\l, \\N, \\P, \\p, \\U, \\u, or \\X - @MarkBaker [CodePlex #14898](https://phpexcel.codeplex.com/workitem/14898)
- VLOOKUP choking on parameters in PHPExcel.1.7.5/PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #14901](https://phpexcel.codeplex.com/workitem/14901)
- PHPExcel_Cell::isInRange() incorrect results - offset by one column - @MarkBaker [CodePlex #14973](https://phpexcel.codeplex.com/workitem/14973)
- Treat CodePage of 0 as CP1251 (for .xls files written by applications that don't set the CodePage correctly, such as Apple Numbers) - @MarkBaker
- Need method for removing autoFilter - @MarkBaker [CodePlex #11583](https://phpexcel.codeplex.com/workitem/11583)
- coordinateFromString throws exception for rows greater than 99,999 - @MarkBaker [CodePlex #15029](https://phpexcel.codeplex.com/workitem/15029)
- PHPExcel Excel2007 Reader colour problems with solidfill - @MarkBaker [CodePlex #14999](https://phpexcel.codeplex.com/workitem/14999)
- Formatting get lost and edit a template XLSX file - @MarkBaker [CodePlex #13215](https://phpexcel.codeplex.com/workitem/13215)
- Excel 2007 Reader /writer lost fontcolor - @MarkBaker [CodePlex #14029](https://phpexcel.codeplex.com/workitem/14029)
- file that makes cells go black - @MarkBaker [CodePlex #13374](https://phpexcel.codeplex.com/workitem/13374)
- Minor patchfix for Excel2003XML Reader when XML is defined with a charset attribute - @MarkBaker
- PHPExcel_Worksheet->toArray() index problem - @MarkBaker [CodePlex #15089](https://phpexcel.codeplex.com/workitem/15089)
- Merge cells 'un-merge' when using an existing spreadsheet - @MarkBaker [CodePlex #15094](https://phpexcel.codeplex.com/workitem/15094)
- Worksheet fromArray() only working with 2-D arrays - @MarkBaker [CodePlex #15129](https://phpexcel.codeplex.com/workitem/15129)
- rangeToarray function modified for non-existent cells - @xkeshav [CodePlex #15172](https://phpexcel.codeplex.com/workitem/15172)
- Images not getting copyied with the ->clone function - @MarkBaker [CodePlex #14980](https://phpexcel.codeplex.com/workitem/14980)
- AdvancedValueBinder.php: String sometimes becomes a date when it shouldn't - @MarkBaker [CodePlex #11576](https://phpexcel.codeplex.com/workitem/11576)
- Fix Excel5 Writer so that it only writes column dimensions for columns that are actually used rather than the full range (A to IV) - @MarkBaker
- FreezePane causing damaged or modified error - @MarkBaker [CodePlex #15198](https://phpexcel.codeplex.com/workitem/15198)
    - The freezePaneByColumnAndRow() method row argument should default to 1 rather than 0.
    - Default row argument for all __ByColumnAndRow() methods should be 1- Column reference rather than cell reference in Print Area definition - @MarkBaker [CodePlex #15121](https://phpexcel.codeplex.com/workitem/15121)
    - Fix Excel2007 Writer to handle print areas that are defined as row or column ranges rather than just as cell ranges- Reduced false positives from isDateTimeFormatCode() method by suppressing testing within quoted strings - @MarkBaker
- Caching and tmp partition exhaustion - @MarkBaker [CodePlex #15312](https://phpexcel.codeplex.com/workitem/15312)
- Writing to Variable No Longer Works. $_tmp_dir Missing in PHPExcel\PHPExcel\Shared\OLE\PPS\Root.php - @MarkBaker [CodePlex #15308](https://phpexcel.codeplex.com/workitem/15308)
- Named ranges with dot don't get parsed properly - @MarkBaker [CodePlex #15379](https://phpexcel.codeplex.com/workitem/15379)
- insertNewRowBefore fails to consistently update references - @MarkBaker [CodePlex #15096](https://phpexcel.codeplex.com/workitem/15096)
- "i" is not a valid character for Excel date format masks (in isDateTimeFormatCode() method) - @MarkBaker
- PHPExcel_ReferenceHelper::insertNewBefore() is missing an 'Update worksheet: comments' section - @MKunert [CodePlex #15421](https://phpexcel.codeplex.com/workitem/15421)

### General

- Full column/row references in named ranges not supported by updateCellReference() - @MarkBaker [CodePlex #15409](https://phpexcel.codeplex.com/workitem/15409)
- Improved performance (speed), for building the Shared Strings table in the Excel2007 Writer. - @MarkBaker
- Improved performance (speed), for PHP to Excel date conversions - @MarkBaker
- Enhanced SheetViews element structures in the Excel2007 Writer for frozen panes. - @MarkBaker
- Removed Serialized Reader/Writer as these no longer work. - @MarkBaker


## [1.7.5] - 2010-12-10

### Features

- Implement Gnumeric File Format - @MarkBaker [CodePlex #8769](https://phpexcel.codeplex.com/workitem/8769)
    - Initial work on Gnumeric Reader (Worksheet Data, Document Properties and basic Formatting)- Support for Extended Workbook Properties in Excel2007, Excel5 and OOCalc Readers; support for User-defined Workbook Properties in Excel2007 and OOCalc Readers - @MarkBaker
- Support for Extended and User-defined Workbook Properties in Excel2007 Writer - @MarkBaker
- Provided a setGenerateSheetNavigationBlock(false); option to suppress generation of the sheet navigation block when writing multiple worksheets to HTML - @MarkBaker
- Advanced Value Binder now recognises TRUE/FALSE strings (locale-specific) and converts to boolean - @MarkBaker
- PHPExcel_Worksheet->toArray() is returning truncated values - @MarkBaker [CodePlex #14301](https://phpexcel.codeplex.com/workitem/14301)
- Configure PDF Writer margins based on Excel Worksheet Margin Settings value - @MarkBaker
- Added Contiguous flag for the CSV Reader, when working with Read Filters - @MarkBaker
- Added getFormattedValue() method for cell object - @MarkBaker
- Added strictNullComparison argument to the worksheet fromArray() method - @MarkBaker

### Bugfixes

- Fix to toFormattedString() method in PHPExcel_Style_NumberFormat to handle fractions with a # code for the integer part - @MarkBaker
- NA() doesn't propagate in matrix calc - quick fix in JAMA/Matrix.php - @MarkBaker [CodePlex #14143](https://phpexcel.codeplex.com/workitem/14143)
- Excel5 : Formula : String constant containing double quotation mark - @Progi1984 [CodePlex #7895](https://phpexcel.codeplex.com/workitem/7895)
- Excel5 : Formula : Percent - @Progi1984 [CodePlex #7895](https://phpexcel.codeplex.com/workitem/7895)
- Excel5 : Formula : Error constant - @Progi1984 [CodePlex #7895](https://phpexcel.codeplex.com/workitem/7895)
- Excel5 : Formula : Concatenation operator - @Progi1984 [CodePlex #7895](https://phpexcel.codeplex.com/workitem/7895)
- Worksheet clone broken for CachedObjectStorage_Memory - @MarkBaker [CodePlex #14146](https://phpexcel.codeplex.com/workitem/14146)
- PHPExcel_Reader_Excel2007 fails when gradient fill without type is present in a file - @MarkBaker [CodePlex #12998](https://phpexcel.codeplex.com/workitem/12998)
- @ format for numeric strings in XLSX to CSV conversion - @MarkBaker [CodePlex #14176](https://phpexcel.codeplex.com/workitem/14176)
- Advanced Value Binder Not Working? - @MarkBaker [CodePlex #14223](https://phpexcel.codeplex.com/workitem/14223)
- unassigned object variable in PHPExcel->removeCellXfByIndex - @MarkBaker [CodePlex #14226](https://phpexcel.codeplex.com/workitem/14226)
- problem with getting cell values from another worksheet... (if cell doesn't exist) - @MarkBaker [CodePlex #14236](https://phpexcel.codeplex.com/workitem/14236)
- Setting cell values to one char strings & Trouble reading one character string (thanks gorfou) - @MarkBaker
- Worksheet title exception when duplicate worksheet is being renamed but exceeds the 31 character limit - @MarkBaker [CodePlex #14256](https://phpexcel.codeplex.com/workitem/14256)
- Named range with sheet name that contains the $ throws exception when getting the cell - @MarkBaker [CodePlex #14086](https://phpexcel.codeplex.com/workitem/14086)
- Added autoloader to DefaultValueBinder and AdvancedValueBinder - @MarkBaker
- Modified PHPExcel_Shared_Date::isDateTimeFormatCode() to return false if format code begins with "_" or with "0 " to prevent false positives - @MarkBaker
    - These leading characters are most commonly associated with number, currency or accounting (or occasionally fraction) formats- BUG : Excel5 and setReadFilter ? - @MarkBaker [CodePlex #14374](https://phpexcel.codeplex.com/workitem/14374)
- Wrong exception message while deleting column - @MarkBaker [CodePlex #14425](https://phpexcel.codeplex.com/workitem/14425)
- Formula evaluation fails with Japanese sheet refs - @MarkBaker [CodePlex #14679](https://phpexcel.codeplex.com/workitem/14679)
- PHPExcel_Writer_PDF does not handle cell borders correctly - @MarkBaker [CodePlex #13559](https://phpexcel.codeplex.com/workitem/13559)
- Style : applyFromArray() for 'allborders' not working - @MarkBaker [CodePlex #14831](https://phpexcel.codeplex.com/workitem/14831)

### General

- Using $this when not in object context in Excel5 Reader - @MarkBaker [CodePlex #14837](https://phpexcel.codeplex.com/workitem/14837)
- Removes a unnecessary loop through each cell when applying conditional formatting to a range. - @MarkBaker
- Removed spurious PHP end tags (?>) - @MarkBaker
- Improved performance (speed) and reduced memory overheads, particularly for the Writers, but across the whole library. - @MarkBaker


## [1.7.4] - 2010-08-26

### Bugfixes

- Excel5 : Formula : Power - @Progi1984 [CodePlex #7895](https://phpexcel.codeplex.com/workitem/7895)
- Excel5 : Formula : Unary plus - @Progi1984 [CodePlex #7895](https://phpexcel.codeplex.com/workitem/7895)
- Excel5 : Just write the Escher stream if necessary in Worksheet - @Progi1984
- Syntax errors in memcache.php 1.7.3c - @MarkBaker [CodePlex #13433](https://phpexcel.codeplex.com/workitem/13433)
- Support for row or column ranges in the calculation engine, e.g. =SUM(C:C) or =SUM(1:2) - @MarkBaker
    - Also support in the calculation engine for absolute row or column ranges e.g. =SUM($C:$E) or =SUM($3:5)- Picture problem with Excel 2003 - @Erik Tilt [CodePlex #13455](https://phpexcel.codeplex.com/workitem/13455)
- Wrong variable used in addExternalSheet in PHPExcel.php - @MarkBaker [CodePlex #13484](https://phpexcel.codeplex.com/workitem/13484)
- "Invalid cell coordinate" error when formula access data from an other sheet - @MarkBaker [CodePlex #13515](https://phpexcel.codeplex.com/workitem/13515)
- (related to Work item 13515) Calculation engine confusing cell range worksheet when referencing cells in a different worksheet to the formula - @MarkBaker
- Wrong var naming in Worksheet->garbageCollect() - @MarkBaker [CodePlex #13752](https://phpexcel.codeplex.com/workitem/13752)
- PHPExcel_Style_*::__clone() methods cause cloning loops? - @MarkBaker [CodePlex #13764](https://phpexcel.codeplex.com/workitem/13764)
- Recent builds causing problems loading xlsx files? (ZipArchive issue?) - @MarkBaker [CodePlex #11488](https://phpexcel.codeplex.com/workitem/11488)
- cache_to_apc causes fatal error when processing large data sets - @MarkBaker [CodePlex #13856](https://phpexcel.codeplex.com/workitem/13856)
- OOCalc reader misses first line if it's a 'table-header-row' - @MarkBaker [CodePlex #13880](https://phpexcel.codeplex.com/workitem/13880)
- using cache with copy or clone bug? - @MarkBaker [CodePlex #14011](https://phpexcel.codeplex.com/workitem/14011)
    - Fixed $worksheet->copy() or clone $worksheet when using cache_in_memory, cache_in_memory_gzip, cache_in_memory_serialized, cache_to_discISAM, cache_to_phpTemp, cache_to_apc and cache_to_memcache;
    - Fixed but untested when using cache_to_wincache.
### Features

- Standard Deviation functions returning DIV/0 Error when Standard Deviation is zero - @MarkBaker [CodePlex #13450](https://phpexcel.codeplex.com/workitem/13450)
- Support for print area with several ranges in the Excel2007 reader, and improved features for editing print area with several ranges - @MarkBaker
- Improved Cell Exception Reporting - @MarkBaker [CodePlex #13769](https://phpexcel.codeplex.com/workitem/13769)

### General

- Fixed problems with reading Excel2007 Properties - @MarkBaker
- PHP Strict Standards: Non-static method PHPExcel_Shared_String::utf16_decode() should not be called statically - @MarkBaker
- Array functions were ignored when loading an existing file containing them, and as a result, they would lose their 'cse' status. - @MarkBaker
- Minor memory tweaks to Excel2007 Writer - @MarkBaker
- Modified ReferenceHelper updateFormulaReferences() method to handle updates to row and column cell ranges (including absolute references e.g. =SUM(A:$E) or =SUM($5:5), and range/cell references that reference a worksheet by name), and to provide both performance and memory improvements. - @MarkBaker
- Modified Excel2007 Reader so that ReferenceHelper class is instantiated only once rather than for every shared formula in a workbook. - @MarkBaker
- Correct handling for additional (synonym) formula tokens in Excel5 Reader - @MarkBaker
- Additional reading of some Excel2007 Extended Properties (Company, Manager) - @MarkBaker


## [1.7.3c] - 2010-06-01

### Bugfixes

- Fatal error: Class 'ZipArchive' not found... ...Reader/Excel2007.php on line 217 - @MarkBaker [CodePlex #13012](https://phpexcel.codeplex.com/workitem/13012)
- PHPExcel_Writer_Excel2007 error after 1.7.3b - @MarkBaker [CodePlex #13398](https://phpexcel.codeplex.com/workitem/13398)


## [1.7.3b] - 2010-05-31

### Bugfixes

- Infinite loop when reading - @MarkBaker [CodePlex #12903](https://phpexcel.codeplex.com/workitem/12903)
- Wrong method chaining on PHPExcel_Worksheet class - @MarkBaker [CodePlex #13381](https://phpexcel.codeplex.com/workitem/13381)


## [1.7.3] - 2010-05-17

### General

- Applied patch 4990 (modified) - @Erik Tilt
- Applied patch 5568 (modified) - @MarkBaker
- Applied patch 5943 - @MarkBaker
- Upgrade build script to use Phing - @MarkBaker [CodePlex #13042](https://phpexcel.codeplex.com/workitem/13042)
- Replacing var with public/private - @Erik Tilt [CodePlex #11586](https://phpexcel.codeplex.com/workitem/11586)
- Applied Anthony's Sterling's Class Autoloader to reduce memory overhead by "Lazy Loading" of classes - @MarkBaker
- Modification to functions that accept a date parameter to support string values containing ordinals as per Excel (English language only) - @MarkBaker
- Modify PHPExcel_Style_NumberFormat::toFormattedString() to handle dates that fall outside of PHP's 32-bit date range - @MarkBaker
- Applied patch 5207 - @MarkBaker

### Features

- PHPExcel developer documentation: Set page margins - @Erik Tilt [CodePlex #11970](https://phpexcel.codeplex.com/workitem/11970)
- Special characters and accents in SYLK reader - @Erik Tilt [CodePlex #11038](https://phpexcel.codeplex.com/workitem/11038)
- Implement more Excel calculation functions - @MarkBaker
    - Implemented the COUPDAYS(), COUPDAYBS(), COUPDAYSNC(), COUPNCD(), COUPPCD() and PRICE() Financial functions
    - Implemented the N() and TYPE() Information functions
    - Implemented the HYPERLINK() Lookup and Reference function- Horizontal page break support in PHPExcel_Writer_PDF - @Erik Tilt [CodePlex #11526](https://phpexcel.codeplex.com/workitem/11526)
- Introduce method setActiveSheetIndexByName() - @Erik Tilt [CodePlex #11529](https://phpexcel.codeplex.com/workitem/11529)
- AdvancedValueBinder.php: Automatically wrap text when there is new line in string (ALT+"Enter") - @Erik Tilt [CodePlex #11550](https://phpexcel.codeplex.com/workitem/11550)
- Data validation support in PHPExcel_Reader_Excel5 and PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #10300](https://phpexcel.codeplex.com/workitem/10300)
- Improve autosize calculation - @MarkBaker [CodePlex #11616](https://phpexcel.codeplex.com/workitem/11616)
- Methods to translate locale-specific function names in formulae - @MarkBaker
    - Language implementations for Czech (cs), Danish (da), German (de), English (uk), Spanish (es), Finnish (fi), French (fr), Hungarian (hu), Italian (it), Dutch (nl), Norwegian (no), Polish (pl), Portuguese (pt), Brazilian Portuguese (pt_br), Russian (ru) and Swedish (sv)- Implement document properties in Excel5 reader/writer - @Erik Tilt [CodePlex #9759](https://phpexcel.codeplex.com/workitem/9759)
    - Fixed so far for PHPExcel_Reader_Excel5- Show/hide row and column headers in worksheet - @Erik Tilt [CodePlex #11849](https://phpexcel.codeplex.com/workitem/11849)
- Can't set font on writing PDF (by key) - @Erik Tilt [CodePlex #11919](https://phpexcel.codeplex.com/workitem/11919)
- Thousands scale (1000^n) support in PHPExcel_Style_NumberFormat::toFormattedString - @Erik Tilt [CodePlex #12096](https://phpexcel.codeplex.com/workitem/12096)
- Implement repeating rows in PDF and HTML writer - @Erik Tilt
- Sheet tabs in PHPExcel_Writer_HTML - @Erik Tilt [CodePlex #12289](https://phpexcel.codeplex.com/workitem/12289)
- Add Wincache CachedObjectProvider - @MarkBaker [CodePlex #13041](https://phpexcel.codeplex.com/workitem/13041)
- Configure PDF Writer paper size based on Excel Page Settings value, and provided methods to override paper size and page orientation with the writer - @MarkBaker
    - Note PHPExcel defaults to Letter size, while the previous PDF writer enforced A4 size, so PDF writer will now default to Letter- Initial implementation of cell caching: allowing larger workbooks to be managed, but at a cost in speed - @MarkBaker

### Bugfixes

- Added an identify() method to the IO Factory that identifies the reader which will be used to load a particular file without actually loading it. - @MarkBaker
- Warning messages with INDEX function having 2 arguments - @MarkBaker [CodePlex #10979](https://phpexcel.codeplex.com/workitem/10979)
- setValue('=') should result in string instead of formula - @Erik Tilt [CodePlex #11473](https://phpexcel.codeplex.com/workitem/11473)
- method _raiseFormulaError should no be private - @MarkBaker [CodePlex #11471](https://phpexcel.codeplex.com/workitem/11471)
- Fatal error: Call to undefined function mb_substr() in ...Classes\PHPExcel\Reader\Excel5.php on line 2903 - @Erik Tilt [CodePlex #11485](https://phpexcel.codeplex.com/workitem/11485)
- getBold(), getItallic(), getStrikeThrough() not always working with PHPExcel_Reader_Excel2007 - @Erik Tilt [CodePlex #11487](https://phpexcel.codeplex.com/workitem/11487)
- AdvancedValueBinder.php not working correctly for $cell->setValue('hh:mm:ss') - @Erik Tilt [CodePlex #11492](https://phpexcel.codeplex.com/workitem/11492)
- Fixed leap year handling for the YEARFRAC() Date/Time function when basis ia 1 (Actual/actual) - @MarkBaker
- Warning messages - @MarkBaker [CodePlex #11490](https://phpexcel.codeplex.com/workitem/11490)
    - Calculation Engine code modified to enforce strict standards for pass by reference- PHPExcel_Cell_AdvancedValueBinder doesnt work for dates in far future - @Erik Tilt [CodePlex #11483](https://phpexcel.codeplex.com/workitem/11483)
- MSODRAWING bug with long CONTINUE record in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #11528](https://phpexcel.codeplex.com/workitem/11528)
- PHPExcel_Reader_Excel2007 reads print titles as named range when there is more than one sheet - @Erik Tilt [CodePlex #11571](https://phpexcel.codeplex.com/workitem/11571)
- missing @return in phpdocblock in reader classes - @Erik Tilt [CodePlex #11561](https://phpexcel.codeplex.com/workitem/11561)
- AdvancedValueBinder.php: String sometimes becomes a date when it shouldn't - @Erik Tilt [CodePlex #11576](https://phpexcel.codeplex.com/workitem/11576)
- Small numbers escape treatment in PHPExcel_Style_NumberFormat::toFormattedString() - @Erik Tilt [CodePlex #11588](https://phpexcel.codeplex.com/workitem/11588)
- Blank styled cells are not blank in output by HTML writer due to &nbsp; - @Erik Tilt [CodePlex #11590](https://phpexcel.codeplex.com/workitem/11590)
- Calculation engine bug: Existing, blank cell + number gives #NUM - @MarkBaker [CodePlex #11587](https://phpexcel.codeplex.com/workitem/11587)
- AutoSize only measures length of first line in cell with multiple lines (ALT+Enter) - @Erik Tilt [CodePlex #11608](https://phpexcel.codeplex.com/workitem/11608)
- Fatal error running Tests/12serializedfileformat.php (PHPExcel 1.7.2) - @Erik Tilt [CodePlex #11608](https://phpexcel.codeplex.com/workitem/11608)
- Fixed various errors in the WORKDAY() and NETWORKDAYS() Date/Time functions (particularly related to holidays) - @MarkBaker
- Uncaught exception 'Exception' with message 'Valid scale is between 10 and 400.' in Classes/PHPExcel/Worksheet/SheetView.php:115 - @Erik Tilt [CodePlex #11660](https://phpexcel.codeplex.com/workitem/11660)
- "Unrecognized token 39 in formula" with PHPExcel_Reader_Excel5 (occuring with add-in functions) - @Erik Tilt [CodePlex #11551](https://phpexcel.codeplex.com/workitem/11551)
- Excel2007 reader not reading PHPExcel_Style_Conditional::CONDITION_EXPRESSION - @Erik Tilt [CodePlex #11668](https://phpexcel.codeplex.com/workitem/11668)
- Fix to the BESSELI(), BESSELJ(), BESSELK(), BESSELY() and COMPLEX() Engineering functions to use correct default values for parameters - @MarkBaker
- DATEVALUE function not working for pure time values + allow DATEVALUE() function to handle partial dates (e.g. "1-Jun" or "12/2010") - @MarkBaker [CodePlex #11525](https://phpexcel.codeplex.com/workitem/11525)
- Fix for empty quoted strings in formulae - @MarkBaker
- Trap for division by zero in Bessel functions - @MarkBaker
- Fix to OOCalc Reader to convert semi-colon (;) argument separator in formulae to a comma (,) - @MarkBaker
- PHPExcel_Writer_Excel5_Parser cannot parse formula like =SUM(C$5:C5) - @Erik Tilt [CodePlex #11693](https://phpexcel.codeplex.com/workitem/11693)
- Fix to OOCalc Reader to handle dates that fall outside 32-bit PHP's date range - @MarkBaker
- File->sys_get_temp_dir() can fail in safe mode - @Erik Tilt [CodePlex #11692](https://phpexcel.codeplex.com/workitem/11692)
- Sheet references in Excel5 writer do not work when referenced sheet title contains non-Latin symbols - @Erik Tilt [CodePlex #11727](https://phpexcel.codeplex.com/workitem/11727)
- Bug in HTML writer can result in missing rows in output - @Erik Tilt [CodePlex #11743](https://phpexcel.codeplex.com/workitem/11743)
- setShowGridLines(true) not working with PHPExcel_Writer_PDF - @Erik Tilt [CodePlex #11674](https://phpexcel.codeplex.com/workitem/11674)
- PHPExcel_Worksheet_RowIterator initial position incorrect - @Erik Tilt [CodePlex #11836](https://phpexcel.codeplex.com/workitem/11836)
- PHPExcel_Worksheet_HeaderFooterDrawing Strict Exception thrown (by jshaw86) - @Erik Tilt [CodePlex #11835](https://phpexcel.codeplex.com/workitem/11835)
- Parts of worksheet lost when there are embedded charts (Excel5 reader) - @Erik Tilt [CodePlex #11850](https://phpexcel.codeplex.com/workitem/11850)
- VLOOKUP() function error when lookup value is passed as a cell reference rather than an absolute value - @MarkBaker
- First segment of Rich-Text not read correctly by PHPExcel_Reader_Excel2007 - @Erik Tilt [CodePlex #12041](https://phpexcel.codeplex.com/workitem/12041)
- Fatal Error with getCell('name') when name matches the pattern for a cell reference - @MarkBaker [CodePlex #12048](https://phpexcel.codeplex.com/workitem/12048)
- excel5 writer appears to be swapping image locations - @Erik Tilt [CodePlex #12039](https://phpexcel.codeplex.com/workitem/12039)
- Undefined index: host in ZipStreamWrapper.php, line 94 and line 101 - @Erik Tilt [CodePlex #11954](https://phpexcel.codeplex.com/workitem/11954)
- BIFF8 File Format problem (too short COLINFO record) - @Erik Tilt [CodePlex #11672](https://phpexcel.codeplex.com/workitem/11672)
- Column width sometimes changed after read/write with Excel2007 reader/writer - @Erik Tilt [CodePlex #12121](https://phpexcel.codeplex.com/workitem/12121)
- Worksheet.php throws a fatal error when styling is turned off via setReadDataOnly on the reader - @Erik Tilt [CodePlex #11964](https://phpexcel.codeplex.com/workitem/11964)
- Checking for Circular References in Formulae - @MarkBaker [CodePlex #11851](https://phpexcel.codeplex.com/workitem/11851)
    - Calculation Engine code now traps for cyclic references, raising an error or throwing an exception, or allows 1 or more iterations through cyclic references, based on a configuration setting- PNG transparency using Excel2007 writer - @Erik Tilt [CodePlex #12244](https://phpexcel.codeplex.com/workitem/12244)
- Custom readfilter error when cell formulas reference excluded cells (Excel5 reader) - @Erik Tilt [CodePlex #12221](https://phpexcel.codeplex.com/workitem/12221)
- Protection problem in XLS - @Erik Tilt [CodePlex #12288](https://phpexcel.codeplex.com/workitem/12288)
- getColumnDimension()->setAutoSize() incorrect on cells with Number Formatting - @Erik Tilt [CodePlex #12300](https://phpexcel.codeplex.com/workitem/12300)
- Notices reading Excel file with Add-in funcitons (PHPExcel_Reader_Excel5) - @Erik Tilt [CodePlex #12378](https://phpexcel.codeplex.com/workitem/12378)
- Excel5 reader not reading formulas with deleted sheet references - @Erik Tilt [CodePlex #12380](https://phpexcel.codeplex.com/workitem/12380)
- Named range (defined name) scope problems for in PHPExcel - @Erik Tilt [CodePlex #12404](https://phpexcel.codeplex.com/workitem/12404)
- PHP Parse error: syntax error, unexpected T_PUBLIC in PHPExcel/Calculation.php on line 3482 - @Erik Tilt [CodePlex #12423](https://phpexcel.codeplex.com/workitem/12423)
- Named ranges don't appear in name box using Excel5 writer - @Erik Tilt [CodePlex #12505](https://phpexcel.codeplex.com/workitem/12505)
- Many merged cells + autoSize column -> slows down the writer - @Erik Tilt [CodePlex #12509](https://phpexcel.codeplex.com/workitem/12509)
- Incorrect fallback order comment in Shared/Strings.php ConvertEncoding() - @Erik Tilt [CodePlex #12539](https://phpexcel.codeplex.com/workitem/12539)
- IBM AIX iconv() will not work, should revert to mbstring etc. instead - @Erik Tilt [CodePlex #12538](https://phpexcel.codeplex.com/workitem/12538)
- Excel5 writer and mbstring functions overload - @Erik Tilt [CodePlex #12568](https://phpexcel.codeplex.com/workitem/12568)
- OFFSET needs to flattenSingleValue the $rows and $columns args - @MarkBaker [CodePlex #12672](https://phpexcel.codeplex.com/workitem/12672)
- Formula with DMAX(): Notice: Undefined offset: 2 in ...\PHPExcel\Calculation.php on line 2365 - @MarkBaker [CodePlex #12546](https://phpexcel.codeplex.com/workitem/12546)
    - Note that the Database functions have not yet been implemented- Call to a member function getParent() on a non-object in Classes\\PHPExcel\\Calculation.php Title is required - @MarkBaker [CodePlex #12839](https://phpexcel.codeplex.com/workitem/12839)
- Cyclic Reference in Formula - @MarkBaker [CodePlex #12935](https://phpexcel.codeplex.com/workitem/12935)
- Memory error...data validation? - @MarkBaker [CodePlex #13025](https://phpexcel.codeplex.com/workitem/13025)


## [1.7.2] - 2010-01-11

### General

- Applied patch 4362 - @Erik Tilt
- Applied patch 4363 (modified) - @Erik Tilt
- 1.7.1 Extremely Slow - Refactored PHPExcel_Calculation_Functions::flattenArray() method and set calculation cache timer default to 2.5 seconds - @MarkBaker [CodePlex #10874](https://phpexcel.codeplex.com/workitem/10874)
- Allow formulae to contain line breaks - @MarkBaker
- split() function deprecated in PHP 5.3.0 - @Erik Tilt [CodePlex #10910](https://phpexcel.codeplex.com/workitem/10910)
- sys_get_temp_dir() requires PHP 5.2.1, not PHP 5.2 [provide fallback function for PHP 5.2.0] - @Erik Tilt
- Implementation of the ISPMT() Financial function by Matt Groves - @MarkBaker
- Put the example of formula with more arguments in documentation - @MarkBaker [CodePlex #11052](https://phpexcel.codeplex.com/workitem/11052)

### Features

- Improved accuracy for the GAMMAINV() Statistical Function - @MarkBaker
- XFEXT record support to fix colors change from Excel5 reader, and copy/paste color change with Excel5 writer - @Erik Tilt [CodePlex #10409](https://phpexcel.codeplex.com/workitem/10409)
    - Excel5 reader reads RGB color information in XFEXT records for borders, font color and fill color- Implement more Excel calculation functions - @MarkBaker
    - Implemented the FVSCHEDULE(), XNPV(), IRR(), MIRR(), XIRR() and RATE() Financial functions
    - Implemented the SUMPRODUCT() Mathematical function
    - Implemented the ZTEST() Statistical Function- Multiple print areas in one sheet - @Erik Tilt [CodePlex #10919](https://phpexcel.codeplex.com/workitem/10919)
- Store calculated values in output by PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #10930](https://phpexcel.codeplex.com/workitem/10930)
- Sheet protection options in Excel5 reader/writer - @Erik Tilt [CodePlex #10939](https://phpexcel.codeplex.com/workitem/10939)
- Modification of the COUNT(), AVERAGE(), AVERAGEA(), DEVSQ, AVEDEV(), STDEV(), STDEVA(), STDEVP(), STDEVPA(), VARA() and VARPA() SKEW() and KURT() functions to correctly handle boolean values depending on whether they're passed in as values, values within a matrix or values within a range of cells. - @MarkBaker
- Cell range selection - @Erik Tilt
- Root-relative path handling - @MarkBaker [CodePlex #10266](https://phpexcel.codeplex.com/workitem/10266)

### Bugfixes

- Named Ranges not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #11315](https://phpexcel.codeplex.com/workitem/11315)
- Excel2007 Reader fails to load Apache POI generated Excel - @MarkBaker [CodePlex #11206](https://phpexcel.codeplex.com/workitem/11206)
- Number format is broken when system's thousands separator is empty - @MarkBaker [CodePlex #11154](https://phpexcel.codeplex.com/workitem/11154)
- ReferenceHelper::updateNamedFormulas throws errors if oldName is empty - @MarkBaker [CodePlex #11401](https://phpexcel.codeplex.com/workitem/11401)
- parse_url() fails to parse path to an image in xlsx - @MarkBaker [CodePlex #11296](https://phpexcel.codeplex.com/workitem/11296)
- Workaround for iconv_substr() bug in PHP 5.2.0 - @Erik Tilt [CodePlex #10876](https://phpexcel.codeplex.com/workitem/10876)
- 1 pixel error for image width and height with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #10877](https://phpexcel.codeplex.com/workitem/10877)
- Fix to GEOMEAN() Statistical function - @MarkBaker
- setValue('-') and setValue('.') sets numeric 0 instead of 1-character string - @Erik Tilt [CodePlex #10884](https://phpexcel.codeplex.com/workitem/10884)
- Row height sometimes much too low after read with PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10885](https://phpexcel.codeplex.com/workitem/10885)
- Diagonal border. Miscellaneous missing support. - @Erik Tilt [CodePlex #10888](https://phpexcel.codeplex.com/workitem/10888)
    - Constant PHPExcel_Style_Borders::DIAGONAL_BOTH added to support double-diagonal (cross)
    - PHPExcel_Reader_Excel2007 not always reading diagonal borders (only recognizes 'true' and not '1')
    - PHPExcel_Reader_Excel5 support for diagonal borders
    - PHPExcel_Writer_Excel5 support for diagonal borders- Session bug: Fatal error: Call to a member function bindValue() on a non-object in ...\Classes\PHPExcel\Cell.php on line 217 - @Erik Tilt [CodePlex #10894](https://phpexcel.codeplex.com/workitem/10894)
- Colors messed up saving twice with same instance of PHPExcel_Writer_Excel5 (regression since 1.7.0) - @Erik Tilt [CodePlex #10896](https://phpexcel.codeplex.com/workitem/10896)
- Method PHPExcel_Worksheet::setDefaultStyle is not working - @Erik Tilt [CodePlex #10917](https://phpexcel.codeplex.com/workitem/10917)
- PHPExcel_Reader_CSV::canRead() sometimes says false when it shouldn't - @Erik Tilt [CodePlex #10897](https://phpexcel.codeplex.com/workitem/10897)
- Changes in workbook not picked up between two saves with PHPExcel_Writer_Excel2007 - @Erik Tilt [CodePlex #10922](https://phpexcel.codeplex.com/workitem/10922)
- Decimal and thousands separators missing in HTML and PDF output - @Erik Tilt [CodePlex #10913](https://phpexcel.codeplex.com/workitem/10913)
- Notices with PHPExcel_Reader_Excel5 and named array constants - @Erik Tilt [CodePlex #10936](https://phpexcel.codeplex.com/workitem/10936)
- Calculation engine limitation on 32-bit platform with integers > 2147483647 - @MarkBaker [CodePlex #10938](https://phpexcel.codeplex.com/workitem/10938)
- Shared(?) formulae containing absolute cell references not read correctly using Excel5 Reader - @Erik Tilt [CodePlex #10959](https://phpexcel.codeplex.com/workitem/10959)
- Warning messages with intersection operator involving single cell - @MarkBaker [CodePlex #10962](https://phpexcel.codeplex.com/workitem/10962)
- Infinite loop in Excel5 reader caused by zero-length string in SST - @Erik Tilt [CodePlex #10980](https://phpexcel.codeplex.com/workitem/10980)
- Remove unnecessary cell sorting to improve speed by approx. 18% in HTML and PDF writers - @Erik Tilt [CodePlex #10983](https://phpexcel.codeplex.com/workitem/10983)
- Cannot read A1 cell content - OO_Reader - @MarkBaker [CodePlex #10977](https://phpexcel.codeplex.com/workitem/10977)
- Transliteration failed, invalid encoding - @Erik Tilt [CodePlex #11000](https://phpexcel.codeplex.com/workitem/11000)


## [1.7.1] - 2009-11-02

### General

- ereg() function deprecated in PHP 5.3.0 - @Erik Tilt [CodePlex #10687](https://phpexcel.codeplex.com/workitem/10687)
- Writer Interface Inconsequence - setTempDir and setUseDiskCaching - @MarkBaker [CodePlex #10739](https://phpexcel.codeplex.com/workitem/10739)

### Features

- Upgrade to TCPDF 4.8.009 - @Erik Tilt
- Support for row and column styles (feature request) - @Erik Tilt
    - Basic implementation for Excel2007/Excel5 reader/writer- Hyperlink to local file in Excel5 reader/writer - @Erik Tilt [CodePlex #10459](https://phpexcel.codeplex.com/workitem/10459)
- Color Tab (Color Sheet's name) - @MarkBaker [CodePlex #10472](https://phpexcel.codeplex.com/workitem/10472)
- Border style "double" support in PHPExcel_Writer_HTML - @Erik Tilt [CodePlex #10488](https://phpexcel.codeplex.com/workitem/10488)
- Multi-section number format support in HTML/PDF/CSV writers - @Erik Tilt [CodePlex #10492](https://phpexcel.codeplex.com/workitem/10492)
- Some additional performance tweaks in the calculation engine - @MarkBaker
- Fix result of DB() and DDB() Financial functions to 2dp when in Gnumeric Compatibility mode - @MarkBaker
- Added AMORDEGRC(), AMORLINC() and COUPNUM() Financial function (no validation of parameters yet) - @MarkBaker
- Improved accuracy of TBILLEQ(), TBILLPRICE() and TBILLYIELD() Financial functions when in Excel or Gnumeric mode - @MarkBaker
- Added INDIRECT() Lookup/Reference function (only supports full addresses at the moment) - @MarkBaker
- PHPExcel_Reader_CSV::canRead() improvements - @MarkBaker [CodePlex #10498](https://phpexcel.codeplex.com/workitem/10498)
- Input encoding option for PHPExcel_Reader_CSV - @Erik Tilt [CodePlex #10500](https://phpexcel.codeplex.com/workitem/10500)
- Colored number format support, e.g. [Red], in HTML/PDF output - @Erik Tilt [CodePlex #10493](https://phpexcel.codeplex.com/workitem/10493)
- Color Tab (Color Sheet's name) [Excel5 reader/writer support] - @Erik Tilt [CodePlex #10559](https://phpexcel.codeplex.com/workitem/10559)
- Initial version of SYLK (slk) and Excel 2003 XML Readers (Cell data and basic cell formatting) - @MarkBaker
- Initial version of Open Office Calc (ods) Reader (Cell data only) - @MarkBaker
- Initial use of "pass by reference" in the calculation engine for ROW() and COLUMN() Lookup/Reference functions - @MarkBaker
- COLUMNS() and ROWS() Lookup/Reference functions, and SUBSTITUTE() Text function - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- AdvancedValueBinder(): Re-enable zero-padded string-to-number conversion, e.g '0004' -> 4 - @Erik Tilt [CodePlex #10502](https://phpexcel.codeplex.com/workitem/10502)
- Make PHP type match Excel datatype - @Erik Tilt [CodePlex #10600](https://phpexcel.codeplex.com/workitem/10600)
- Change first page number on header - @MarkBaker [CodePlex #10630](https://phpexcel.codeplex.com/workitem/10630)
- Applied patch 3941 - @MarkBaker
- Hidden sheets - @MB,ET [CodePlex #10745](https://phpexcel.codeplex.com/workitem/10745)
- mbstring fallback when iconv is broken - @Erik Tilt [CodePlex #10761](https://phpexcel.codeplex.com/workitem/10761)
- Note, can't yet handle comparison of two matrices - @MarkBaker
- Improved handling for validation and error trapping in a number of functions - @MarkBaker
- Improved support for fraction number formatting - @MarkBaker
- Support Reading CSV with Byte Order Mark (BOM) - @Erik Tilt [CodePlex #10455](https://phpexcel.codeplex.com/workitem/10455)

### Bugfixes

- addExternalSheet() at specified index - @Erik Tilt [CodePlex #10860](https://phpexcel.codeplex.com/workitem/10860)
- Named range can no longer be passed to worksheet->getCell() - @MarkBaker [CodePlex #10684](https://phpexcel.codeplex.com/workitem/10684)
- RichText HTML entities no longer working in PHPExcel 1.7.0 - @Erik Tilt [CodePlex #10455](https://phpexcel.codeplex.com/workitem/10455)
- Fit-to-width value of 1 is lost after read/write of Excel2007 spreadsheet [+ support for simultaneous scale/fitToPage] - @Erik Tilt
- Performance issue identified by profiling - @MarkBaker [CodePlex #10469](https://phpexcel.codeplex.com/workitem/10469)
- setSelectedCell is wrong - @Erik Tilt [CodePlex #10473](https://phpexcel.codeplex.com/workitem/10473)
- Images get squeezed/stretched with (Mac) Verdana 10 Excel files using Excel5 reader/writer - @Erik Tilt [CodePlex #10481](https://phpexcel.codeplex.com/workitem/10481)
- Error in argument count for DATEDIF() function - @MarkBaker [CodePlex #10482](https://phpexcel.codeplex.com/workitem/10482)
- updateFormulaReferences is buggy - @MarkBaker [CodePlex #10452](https://phpexcel.codeplex.com/workitem/10452)
- CellIterator returns null Cell if onlyExistingCells is set and key() is in use - @MarkBaker [CodePlex #10485](https://phpexcel.codeplex.com/workitem/10485)
- Wrong RegEx for parsing cell references in formulas - @MarkBaker [CodePlex #10453](https://phpexcel.codeplex.com/workitem/10453)
- Optimisation subverted to devastating effect if IterateOnlyExistingCells is clear - @MarkBaker [CodePlex #10486](https://phpexcel.codeplex.com/workitem/10486)
- Fatal error: Uncaught exception 'Exception' with message 'Unrecognized token 6C in formula'... with PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10494](https://phpexcel.codeplex.com/workitem/10494)
- Fractions stored as text are not treated as numbers by PHPExcel's calculation engine - @MarkBaker [CodePlex #10490](https://phpexcel.codeplex.com/workitem/10490)
- AutoFit (autosize) row height not working in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #10503](https://phpexcel.codeplex.com/workitem/10503)
- Fixed problem with null values breaking the calculation stack - @MarkBaker
- Date number formats sometimes fail with PHPExcel_Style_NumberFormat::toFormattedString, e.g. [$-40047]mmmm d yyyy - @Erik Tilt [CodePlex #10524](https://phpexcel.codeplex.com/workitem/10524)
- Fixed minor problem with DATEDIFF YM calculation - @MarkBaker
- Applied patch 3695 - @MarkBaker
- setAutosize() and Date cells not working properly - @Erik Tilt [CodePlex #10536](https://phpexcel.codeplex.com/workitem/10536)
- Time value hour offset in output by HTML/PDF/CSV writers (system timezone problem) - @Erik Tilt [CodePlex #10556](https://phpexcel.codeplex.com/workitem/10556)
- Control characters 0x14-0x1F are not treated by PHPExcel - @Erik Tilt [CodePlex #10558](https://phpexcel.codeplex.com/workitem/10558)
- PHPExcel_Writer_Excel5 not working when open_basedir restriction is in effect - @Erik Tilt [CodePlex #10560](https://phpexcel.codeplex.com/workitem/10560)
- IF formula calculation problem in PHPExcel 1.7.0 (string comparisons) - @MarkBaker [CodePlex #10563](https://phpexcel.codeplex.com/workitem/10563)
- Improved CODE() Text function result for UTF-8 characters - @MarkBaker
- Empty rows are collapsed with HTML/PDF writer - @Erik Tilt [CodePlex #10568](https://phpexcel.codeplex.com/workitem/10568)
- Gaps between rows in output by PHPExcel_Writer_PDF (Upgrading to TCPDF 4.7.003) - @Erik Tilt [CodePlex #10569](https://phpexcel.codeplex.com/workitem/10569)
- Problem reading formulas (Excel5 reader problem with "fake" shared formulas) - @Erik Tilt [CodePlex #10575](https://phpexcel.codeplex.com/workitem/10575)
- Error type in formula: "_raiseFormulaError message is Formula Error: An unexpected error occured" - @MarkBaker [CodePlex #10588](https://phpexcel.codeplex.com/workitem/10588)
- Miscellaneous column width problems in Excel5/Excel2007 writer - @Erik Tilt [CodePlex #10599](https://phpexcel.codeplex.com/workitem/10599)
- Reader/Excel5 'Unrecognized token 2D in formula' in latest version - @Erik Tilt [CodePlex #10615](https://phpexcel.codeplex.com/workitem/10615)
- on php 5.3 PHPExcel 1.7 Excel 5 reader fails in _getNextToken, token = 2C, throws exception - @Erik Tilt [CodePlex #10623](https://phpexcel.codeplex.com/workitem/10623)
- Fatal error when altering styles after workbook has been saved - @Erik Tilt [CodePlex #10617](https://phpexcel.codeplex.com/workitem/10617)
- Images vertically stretched or squeezed when default font size is changed (PHPExcel_Writer_Excel5) - @Erik Tilt [CodePlex #10661](https://phpexcel.codeplex.com/workitem/10661)
- Styles not read in "manipulated" Excel2007 workbook - @Erik Tilt [CodePlex #10676](https://phpexcel.codeplex.com/workitem/10676)
- Windows 7 says corrupt file by PHPExcel_Writer_Excel5 when opening in Excel - @Erik Tilt [CodePlex #10059](https://phpexcel.codeplex.com/workitem/10059)
- Calculations sometimes not working with cell references to other sheets - @MarkBaker [CodePlex #10708](https://phpexcel.codeplex.com/workitem/10708)
- Problem with merged cells after insertNewRowBefore() - @Erik Tilt [CodePlex #10706](https://phpexcel.codeplex.com/workitem/10706)
- Applied patch 4023 - @MarkBaker
- Fix to SUMIF() and COUNTIF() Statistical functions for when condition is a match against a string value - @MarkBaker
- PHPExcel_Cell::coordinateFromString should throw exception for bad string parameter - @Erik Tilt [CodePlex #10721](https://phpexcel.codeplex.com/workitem/10721)
- EucrosiaUPC (Thai font) not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #10723](https://phpexcel.codeplex.com/workitem/10723)
- Improved the return of calculated results when the result value is an array - @MarkBaker
- Allow calculation engine to support Functions prefixed with @ within formulae - @MarkBaker
- Intersection operator (space operator) fatal error with calculation engine - @MarkBaker [CodePlex #10632](https://phpexcel.codeplex.com/workitem/10632)
- Chinese, Japanese, Korean characters show as squares in PDF - @Erik Tilt [CodePlex #10742](https://phpexcel.codeplex.com/workitem/10742)
- sheet title allows invalid characters - @Erik Tilt [CodePlex #10756](https://phpexcel.codeplex.com/workitem/10756)
- Sheet!$A$1 as function argument in formula causes infinite loop in Excel5 writer - @Erik Tilt [CodePlex #10757](https://phpexcel.codeplex.com/workitem/10757)
- Cell range involving name not working with calculation engine - Modified calculation parser to handle range operator (:), but doesn't currently handle worksheet references with spaces or other non-alphameric characters, or trap erroneous references - @MarkBaker [CodePlex #10740](https://phpexcel.codeplex.com/workitem/10740)
- DATE function problem with calculation engine (says too few arguments given) - @MarkBaker [CodePlex #10798](https://phpexcel.codeplex.com/workitem/10798)
- Blank cell can cause wrong calculated value - @MarkBaker [CodePlex #10799](https://phpexcel.codeplex.com/workitem/10799)
- Modified ROW() and COLUMN() Lookup/Reference Functions to return an array when passed a cell range, plus some additional work on INDEX() - @MarkBaker
- Images not showing in Excel 97 using PHPExcel_Writer_Excel5 (patch by Jordi Gutiérrez Hermoso) - @Erik Tilt [CodePlex #10817](https://phpexcel.codeplex.com/workitem/10817)
- When figures are contained in the excel sheet, Reader was stopped - @Erik Tilt [CodePlex #10785](https://phpexcel.codeplex.com/workitem/10785)
- Formulas changed after insertNewRowBefore() - @MarkBaker [CodePlex #10818](https://phpexcel.codeplex.com/workitem/10818)
- Cell range row offset problem with shared formulas using PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10825](https://phpexcel.codeplex.com/workitem/10825)
- Warning: Call-time pass-by-reference has been deprecated - @MarkBaker [CodePlex #10832](https://phpexcel.codeplex.com/workitem/10832)
- Image should "Move but don't size with cells" instead of "Move and size with cells" with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #10849](https://phpexcel.codeplex.com/workitem/10849)
- Opening a Excel5 generated XLS in Excel 2007 results in header/footer entry not showing on input - @Erik Tilt [CodePlex #10856](https://phpexcel.codeplex.com/workitem/10856)
- addExternalSheet() not returning worksheet - @Erik Tilt [CodePlex #10859](https://phpexcel.codeplex.com/workitem/10859)
- Invalid results in formulas with named ranges - @MarkBaker [CodePlex #10629](https://phpexcel.codeplex.com/workitem/10629)


## [1.7.0] - 2009-08-10

### General

- Expand documentation: Number formats - @Erik Tilt
- Class 'PHPExcel_Cell_AdvancedValueBinder' not found - @Erik Tilt

### Features

- Change return type of date functions to PHPExcel_Calculation_Functions::RETURNDATE_EXCEL - @MarkBaker
- New RPN and stack-based calculation engine for improved performance of formula calculation - @MarkBaker
    - Faster (anything between 2 and 12 times faster than the old parser, depending on the complexity and nature of the formula)
    - Significantly more memory efficient when formulae reference cells across worksheets
    - Correct behaviour when referencing Named Ranges that exist on several worksheets
    - Support for Excel ^ (Exponential) and % (Percentage) operators
    - Support for matrices within basic arithmetic formulae (e.g. ={1,2,3;4,5,6;7,8,9}/2)
    - Better trapping/handling of NaN and infinity results (return #NUM! error)
    - Improved handling of empty parameters for Excel functions
    - Optional logging of calculation steps- New calculation engine can be accessed independently of workbooks (for use as a standalone calculator) - @MarkBaker
- Implement more Excel calculation functions - @MarkBaker
    - Initial implementation of the COUNTIF() and SUMIF() Statistical functions
    - Added ACCRINT() Financial function- Modifications to number format handling for dddd and ddd masks in dates, use of thousand separators even when locale only implements it for money, and basic fraction masks (0 ?/? and ?/?) - @MarkBaker
- Support arbitrary fixed number of decimals in PHPExcel_Style_NumberFormat::toFormattedString() - @Erik Tilt
- Improving performance and memory on data dumps - @Erik Tilt
    - Various style optimizations (merging from branch wi6857-memory)
    - Moving hyperlink and dataValidation properties from cell to worksheet for lower PHP memory usage- Provide fluent interfaces where possible - @MarkBaker
- Make easy way to apply a border to a rectangular selection - @Erik Tilt
- Support for system window colors in PHPExcel_Reader_Excel5 - @Erik Tilt
- Horizontal center across selection - @Erik Tilt
- Merged cells record, write to full record size in PHPExcel_Writer_Excel5 - @Erik Tilt
- Add page break between sheets in exported PDF - @MarkBaker
- Sanitization of UTF-8 input for cell values - @Erik Tilt
- Read cached calculated value with PHPExcel_Reader_Excel5 - @Erik Tilt
- Miscellaneous CSS improvements for PHPExcel_Writer_HTML - @Erik Tilt
- getProperties: setCompany feature request - @Erik Tilt
- Insert worksheet at a specified index - @MarkBaker
- Change worksheet index - @MarkBaker
- Readfilter for CSV reader - @MarkBaker
- Check value of mbstring.func_overload when saving with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #10172](https://phpexcel.codeplex.com/workitem/10172)
- Eliminate dependency of an include path pointing to class directory - @Erik Tilt [CodePlex #10251](https://phpexcel.codeplex.com/workitem/10251)
- Method for getting the correct reader for a certain file (contribution) - @Erik Tilt [CodePlex #10292](https://phpexcel.codeplex.com/workitem/10292)
- Choosing specific row in fromArray method - @Erik Tilt [CodePlex #10287](https://phpexcel.codeplex.com/workitem/10287)
- Shared formula support in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10319](https://phpexcel.codeplex.com/workitem/10319)

### Bugfixes

- Right-to-left column direction in worksheet - @MB,ET [CodePlex #10345](https://phpexcel.codeplex.com/workitem/10345)
- PHPExcel_Reader_Excel5 not reading PHPExcel_Style_NumberFormat::FORMAT_NUMBER ('0') - @Erik Tilt
- Fractional row height in locale other than English results in corrupt output using PHPExcel_Writer_Excel2007 - @Erik Tilt
- Fractional (decimal) numbers not inserted correctly when locale is other than English - @Erik Tilt
- Fractional calculated value in locale other than English results in corrupt output using PHPExcel_Writer_Excel2007 - @Erik Tilt
- Locale aware decimal and thousands separator in exported formats HTML, CSV, PDF - @Erik Tilt
- Cannot Add Image with Space on its Name - @MarkBaker
- Black line at top of every page in output by PHPExcel_Writer_PDF - @Erik Tilt
- Border styles and border colors not showing in HTML output (regression since 1.6.4) - @Erik Tilt
- Hidden screen gridlines setting in worksheet not read by PHPExcel_Reader_Excel2007 - @Erik Tilt
- Some valid sheet names causes corrupt output using PHPExcel_Writer_Excel2007 - @MarkBaker
- More than 32,767 characters in a cell gives corrupt Excel file - @Erik Tilt
- Images not getting copyied with the ->copy() function - @Erik Tilt
- Bad calculation of column width setAutoSize(true) function - @Erik Tilt
- Dates are sometimes offset by 1 day in output by HTML and PDF writers depending on system timezone setting - @Erik Tilt
- Wingdings symbol fonts not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #10003](https://phpexcel.codeplex.com/workitem/10003)
- White space string prefix stripped by PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #10010](https://phpexcel.codeplex.com/workitem/10010)
- The name of the Workbook stream MUST be "Workbook", not "Book" - @Erik Tilt [CodePlex #10023](https://phpexcel.codeplex.com/workitem/10023)
- Avoid message "Microsoft Excel recalculates formulas..." when closing xls file from Excel - @Erik Tilt [CodePlex #10030](https://phpexcel.codeplex.com/workitem/10030)
- Non-unique newline representation causes problems with LEN formula - @Erik Tilt [CodePlex #10031](https://phpexcel.codeplex.com/workitem/10031)
- Newline in cell not showing with PHPExcel_Writer_HTML and PHPExcel_Writer_PDF - @Erik Tilt [CodePlex #10033](https://phpexcel.codeplex.com/workitem/10033)
- Rich-Text strings get prefixed by &nbsp; when output by HTML writer - @Erik Tilt [CodePlex #10046](https://phpexcel.codeplex.com/workitem/10046)
- Leading spaces do not appear in output by HTML/PDF writers - @Erik Tilt [CodePlex #10052](https://phpexcel.codeplex.com/workitem/10052)
- Empty Apache POI-generated file can not be read - @MarkBaker [CodePlex #10061](https://phpexcel.codeplex.com/workitem/10061)
- Column width not scaling correctly with font size in HTML and PDF writers - @Erik Tilt [CodePlex #10068](https://phpexcel.codeplex.com/workitem/10068)
- Inaccurate row heights with HTML writer - @Erik Tilt [CodePlex #10069](https://phpexcel.codeplex.com/workitem/10069)
- Reference helper - @MarkBaker
- Excel 5 Named ranges should not be local to the worksheet, but accessible from all worksheets - @MarkBaker
- Row heights are ignored by PHPExcel_Writer_PDF - @Erik Tilt [CodePlex #10088](https://phpexcel.codeplex.com/workitem/10088)
- Write raw XML - @MarkBaker
- removeRow(), removeColumn() not always clearing cell values - @Erik Tilt [CodePlex #10098](https://phpexcel.codeplex.com/workitem/10098)
- Problem reading certain hyperlink records with PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10142](https://phpexcel.codeplex.com/workitem/10142)
- Hyperlink cell range read failure with PHPExcel_Reader_Excel2007 - @Erik Tilt [CodePlex #10143](https://phpexcel.codeplex.com/workitem/10143)
- 'Column string index can not be empty.' - @MarkBaker [CodePlex #10149](https://phpexcel.codeplex.com/workitem/10149)
- getHighestColumn() sometimes says there are 256 columns with PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10204](https://phpexcel.codeplex.com/workitem/10204)
- extractSheetTitle fails when sheet title contains exclamation mark (!) - @Erik Tilt [CodePlex #10220](https://phpexcel.codeplex.com/workitem/10220)
- setTitle() sometimes erroneously appends integer to sheet name - @Erik Tilt [CodePlex #10221](https://phpexcel.codeplex.com/workitem/10221)
- Mac BIFF5 Excel file read failure (missing support for Mac OS Roman character set) - @Erik Tilt [CodePlex #10229](https://phpexcel.codeplex.com/workitem/10229)
- BIFF5 header and footer incorrectly read by PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10230](https://phpexcel.codeplex.com/workitem/10230)
- iconv notices when reading hyperlinks with PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10259](https://phpexcel.codeplex.com/workitem/10259)
- Excel5 reader OLE read failure with small Mac BIFF5 Excel files - @Erik Tilt [CodePlex #10252](https://phpexcel.codeplex.com/workitem/10252)
- Problem in reading formula : IF( IF ) with PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10272](https://phpexcel.codeplex.com/workitem/10272)
- Error reading formulas referencing external sheets with PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10274](https://phpexcel.codeplex.com/workitem/10274)
- Image horizontally stretched when default font size is increased (PHPExcel_Writer_Excel5) - @Erik Tilt [CodePlex #10291](https://phpexcel.codeplex.com/workitem/10291)
- Undefined offset in Reader\Excel5.php on line 3572 - @Erik Tilt [CodePlex #10333](https://phpexcel.codeplex.com/workitem/10333)
- PDF output different then XLS (copied data) - @MarkBaker [CodePlex #10340](https://phpexcel.codeplex.com/workitem/10340)
- Internal hyperlinks with UTF-8 sheet names not working in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #10352](https://phpexcel.codeplex.com/workitem/10352)
- String shared formula result read error with PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #10361](https://phpexcel.codeplex.com/workitem/10361)
- Uncaught exception 'Exception' with message 'Valid scale is between 10 and 400.' in Classes/PHPExcel/Worksheet/PageSetup.php:338 - @Erik Tilt [CodePlex #10363](https://phpexcel.codeplex.com/workitem/10363)
- Using setLoadSheetsOnly fails if you do not use setReadDataOnly(true) and sheet is not the first sheet - @Erik Tilt [CodePlex #10355](https://phpexcel.codeplex.com/workitem/10355)
- getCalculatedValue() sometimes incorrect with IF formula and 0-values - @MarkBaker [CodePlex #10362](https://phpexcel.codeplex.com/workitem/10362)
- Excel Reader 2007 problem with "shared" formulae when "master" is an error - @MarkBaker
- Named Range Bug, using the same range name on different worksheets - @MarkBaker
- Java code in JAMA classes - @MarkBaker
- getCalculatedValue() not working with some formulas involving error types - @MarkBaker
- evaluation of both return values in an IF() statement returning an error if either result was an error, irrespective of the IF evaluation - @MarkBaker
- Power in formulas: new calculation engine no longer treats ^ as a bitwise XOR operator - @MarkBaker
- Bugfixes and improvements to many of the Excel functions in PHPExcel - @MarkBaker
    - Added optional "places" parameter in the BIN2HEX(), BIN2OCT, DEC2BIN(), DEC2OCT(), DEC2HEX(), HEX2BIN(), HEX2OCT(), OCT2BIN() and OCT2HEX() Engineering Functions
    - Trap for unbalanced matrix sizes in MDETERM() and MINVERSE() Mathematic and Trigonometric functions
    - Fix for default characters parameter value for LEFT() and RIGHT() Text functions
    - Fix for GCD() and LCB() Mathematical functions when the parameters include a zero (0) value
    - Fix for BIN2OCT() Engineering Function for 2s complement values (which were returning hex values)
    - Fix for BESSELK() and BESSELY() Engineering functions
    - Fix for IMDIV() Engineering Function when result imaginary component is positive (wasn't setting the sign)
    - Fix for ERF() Engineering Function when called with an upper limit value for the integration
    - Fix to DATE() Date/Time Function for year value of 0
    - Set ISPMT() function as category FINANCIAL
    - Fix for DOLLARDE() and DOLLARFR() Financial functions
    - Fix to EFFECT() Financial function (treating $nominal_rate value as a variable name rather than a value)
    - Fix to CRITBINOM() Statistical function (CurrentValue and EssentiallyZero treated as constants rather than variables)
    - Note that an Error in the function logic can still lead to a permanent loop
    - Fix to MOD() Mathematical function to work with floating point results
    - Fix for QUOTIENT() Mathematical function
    - Fix to HOUR(), MINUTE() and SECOND() Date/Time functions to return an error when passing in a floating point value of 1.0 or greater, or less than 0
    - LOG() Function now correctly returns base-10 log when called with only one parameter, rather than the natural log as the default base
    - Modified text functions to handle multibyte character set (UTF-8).

## [1.6.7] - 2009-04-22

### BREAKING CHANGE

In previous versions of PHPExcel up to and including 1.6.6,
when a cell had a date-like number format code, it was possible to enter a date
directly using an integer PHP-time without converting to Excel date format.
Starting with PHPExcel 1.6.7 this is no longer supported. Refer to the developer
documentation for more information on entering dates into a cell.

### General

- Deprecate misspelled setStriketrough() and getStriketrough() methods - @MarkBaker [CodePlex #9416](https://phpexcel.codeplex.com/workitem/9416)

### Features

- Performance improvement when saving file - @MarkBaker [CodePlex #9526](https://phpexcel.codeplex.com/workitem/9526)
- Check that sheet title has maximum 31 characters - @MarkBaker [CodePlex #9598](https://phpexcel.codeplex.com/workitem/9598)
- True support for Excel built-in number format codes - @MB, ET [CodePlex #9631](https://phpexcel.codeplex.com/workitem/9631)
- Ability to read defect BIFF5 Excel file without CODEPAGE record - @Erik Tilt [CodePlex #9683](https://phpexcel.codeplex.com/workitem/9683)
- Auto-detect which reader to invoke - @MarkBaker [CodePlex #9701](https://phpexcel.codeplex.com/workitem/9701)
- Deprecate insertion of dates using PHP-time (Unix time) [request for removal of feature] - @Erik Tilt [CodePlex #9214](https://phpexcel.codeplex.com/workitem/9214)
- Support for entering time values like '9:45', '09:45' using AdvancedValueBinder - @Erik Tilt [CodePlex #9747](https://phpexcel.codeplex.com/workitem/9747)

### Bugfixes

- DataType dependent horizontal alignment in HTML and PDF writer - @Erik Tilt [CodePlex #9797](https://phpexcel.codeplex.com/workitem/9797)
- Cloning data validation object causes script to stop - @MarkBaker [CodePlex #9375](https://phpexcel.codeplex.com/workitem/9375)
- Simultaneous repeating rows and repeating columns not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #9400](https://phpexcel.codeplex.com/workitem/9400)
- Simultaneous repeating rows and repeating columns not working with PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #9399](https://phpexcel.codeplex.com/workitem/9399)
- Row outline level not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #9437](https://phpexcel.codeplex.com/workitem/9437)
- Occasional notices with PHPExcel_Reader_Excel5 when Excel file contains drawing elements - @Erik Tilt [CodePlex #9452](https://phpexcel.codeplex.com/workitem/9452)
- PHPExcel_Reader_Excel5 fails as a whole when workbook contains images other than JPEG/PNG - @Erik Tilt [CodePlex #9453](https://phpexcel.codeplex.com/workitem/9453)
- Excel5 writer checks for iconv but does not necessarily use it - @Erik Tilt [CodePlex #9444](https://phpexcel.codeplex.com/workitem/9444)
- Altering a style on copied worksheet alters also the original - @Erik Tilt [CodePlex #9463](https://phpexcel.codeplex.com/workitem/9463)
- Formulas are incorrectly updated when a sheet is renamed - @MarkBaker [CodePlex #9480](https://phpexcel.codeplex.com/workitem/9480)
- PHPExcel_Worksheet::extractSheetTitle not treating single quotes correctly - @MarkBaker [CodePlex #9513](https://phpexcel.codeplex.com/workitem/9513)
- PHP Warning raised in function array_key_exists - @MarkBaker [CodePlex #9477](https://phpexcel.codeplex.com/workitem/9477)
- getAlignWithMargins() gives wrong value when using PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #9599](https://phpexcel.codeplex.com/workitem/9599)
- getScaleWithDocument() gives wrong value when using PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #9600](https://phpexcel.codeplex.com/workitem/9600)
- PHPExcel_Reader_Excel2007 not reading the first user-defined number format - @MarkBaker [CodePlex #9630](https://phpexcel.codeplex.com/workitem/9630)
- Print area converted to uppercase after read with PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #9647](https://phpexcel.codeplex.com/workitem/9647)
- Incorrect reading of scope for named range using PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #9661](https://phpexcel.codeplex.com/workitem/9661)
- Error with pattern (getFillType) and rbg (getRGB) - @MarkBaker [CodePlex #9690](https://phpexcel.codeplex.com/workitem/9690)
- AdvancedValueBinder affected by system timezone setting when inserting date values - @Erik Tilt [CodePlex #9712](https://phpexcel.codeplex.com/workitem/9712)
- PHPExcel_Reader_Excel2007 not reading value of active sheet index - @Erik Tilt [CodePlex #9743](https://phpexcel.codeplex.com/workitem/9743)
- getARGB() sometimes returns SimpleXMLElement object instead of string with PHPExcel_Reader_Excel2007 - @Erik Tilt [CodePlex #9742](https://phpexcel.codeplex.com/workitem/9742)
- Negative image offset causes defects in 14excel5.xls and 20readexcel5.xlsx - @Erik Tilt [CodePlex #9731](https://phpexcel.codeplex.com/workitem/9731)
- HTML & PDF Writer not working with mergeCells (regression since 1.6.5) - @Erik Tilt [CodePlex #9758](https://phpexcel.codeplex.com/workitem/9758)
- Too wide columns with HTML and PDF writer - @Erik Tilt [CodePlex #9774](https://phpexcel.codeplex.com/workitem/9774)
- PDF and cyrillic fonts - @MarkBaker [CodePlex #9775](https://phpexcel.codeplex.com/workitem/9775)
- Percentages not working correctly with HTML and PDF writers (shows 0.25% instead of 25%) - @Erik Tilt [CodePlex #9793](https://phpexcel.codeplex.com/workitem/9793)
- PHPExcel_Writer_HTML creates extra borders around cell contents using setUseInlineCss(true) - @Erik Tilt [CodePlex #9791](https://phpexcel.codeplex.com/workitem/9791)
- Problem with text wrap + merged cells in HTML and PDF writer - @Erik Tilt [CodePlex #9784](https://phpexcel.codeplex.com/workitem/9784)
- Adjacent path separators in include_path causing IOFactory to violate open_basedir restriction - @Erik Tilt [CodePlex #9814](https://phpexcel.codeplex.com/workitem/9814)


## [1.6.6] - 2009-03-02

### General

- Improve support for built-in number formats in PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #9102](https://phpexcel.codeplex.com/workitem/9102)
- Source files are in both UNIX and DOS formats - changed to UNIX - @Erik Tilt [CodePlex #9281](https://phpexcel.codeplex.com/workitem/9281)

### Features

- Update documentation: Which language to write formulas in? - @MarkBaker [CodePlex #9338](https://phpexcel.codeplex.com/workitem/9338)
- Ignore DEFCOLWIDTH records with value 8 in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #8817](https://phpexcel.codeplex.com/workitem/8817)
- Support for width, height, offsetX, offsetY for images in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #8847](https://phpexcel.codeplex.com/workitem/8847)
- Disk Caching in specific folder - @MarkBaker [CodePlex #8870](https://phpexcel.codeplex.com/workitem/8870)
- Added SUMX2MY2, SUMX2PY2, SUMXMY2, MDETERM and MINVERSE Mathematical and Trigonometric Functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Added CONVERT Engineering Function - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Added DB, DDB, DISC, DOLLARDE, DOLLARFR, INTRATE, IPMT, PPMT, PRICEDISC, PRICEMAT and RECEIVED Financial Functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Added ACCRINTM, CUMIPMT, CUMPRINC, TBILLEQ, TBILLPRICE, TBILLYIELD, YIELDDISC and YIELDMAT Financial Functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Added DOLLAR Text Function - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Added CORREL, COVAR, FORECAST, INTERCEPT, RSQ, SLOPE and STEYX Statistical Functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Added PEARSON Statistical Functions as a synonym for CORREL - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Added LINEST, LOGEST (currently only valid for stats = false), TREND and GROWTH Statistical Functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Added RANK and PERCENTRANK Statistical Functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Added ROMAN Mathematical Function (Classic form only) - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Update documentation to show example of getCellByColumnAndRow($col, $row) - @MarkBaker [CodePlex #8931](https://phpexcel.codeplex.com/workitem/8931)
- Implement worksheet, row and cell iterators - @MarkBaker [CodePlex #8770](https://phpexcel.codeplex.com/workitem/8770)
- Support for arbitrary defined names (named range) - @MarkBaker [CodePlex #9001](https://phpexcel.codeplex.com/workitem/9001)
- Update formulas when sheet title / named range title changes - @MB, ET [CodePlex #9016](https://phpexcel.codeplex.com/workitem/9016)
- Ability to read cached calculated value - @MarkBaker [CodePlex #9103](https://phpexcel.codeplex.com/workitem/9103)
- Support for Excel 1904 calendar date mode (Mac) - @MBaker, ET [CodePlex #8483](https://phpexcel.codeplex.com/workitem/8483)
- PHPExcel_Writer_Excel5 improvements writing shared strings table - @Erik Tilt [CodePlex #9194](https://phpexcel.codeplex.com/workitem/9194)
- PHPExcel_Writer_Excel5 iconv fallback when mbstring extension is not enabled - @Erik Tilt [CodePlex #9248](https://phpexcel.codeplex.com/workitem/9248)
- UTF-8 support in font names in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #9253](https://phpexcel.codeplex.com/workitem/9253)
- Implement value binding architecture - @MarkBaker [CodePlex #9215](https://phpexcel.codeplex.com/workitem/9215)
- PDF writer not working with UTF-8 - @MarkBaker [CodePlex #6742](https://phpexcel.codeplex.com/workitem/6742)

### Bugfixes

- Eliminate duplicate style entries in multisheet workbook written by PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #9355](https://phpexcel.codeplex.com/workitem/9355)
- Redirect to client browser fails due to trailing white space in class definitions - @Erik Tilt [CodePlex #8810](https://phpexcel.codeplex.com/workitem/8810)
- Spurious column dimension element introduced in blank worksheet after using PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #8816](https://phpexcel.codeplex.com/workitem/8816)
- Image gets slightly narrower than expected when using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8830](https://phpexcel.codeplex.com/workitem/8830)
- Image laid over non-visible row gets squeezed in height when using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8831](https://phpexcel.codeplex.com/workitem/8831)
- PHPExcel_Reader_Excel5 fails when there are 10 or more images in the workbook - @Erik Tilt [CodePlex #8860](https://phpexcel.codeplex.com/workitem/8860)
- Different header/footer images in different sheets not working with PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #8909](https://phpexcel.codeplex.com/workitem/8909)
- Fractional seconds disappear when using PHPExcel_Reader_Excel2007 and PHPExcel_Reader_Excel5 - @MB, ET [CodePlex #8924](https://phpexcel.codeplex.com/workitem/8924)
- Images not showing in OpenOffice when using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7994](https://phpexcel.codeplex.com/workitem/7994)
- Images not showing on print using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #9047](https://phpexcel.codeplex.com/workitem/9047)
- PHPExcel_Writer_Excel5 maximum allowed record size 4 bytes too short - @Erik Tilt [CodePlex #9085](https://phpexcel.codeplex.com/workitem/9085)
- Not numeric strings are formatted as dates and numbers using worksheet's toArray method - @MarkBaker [CodePlex #9119](https://phpexcel.codeplex.com/workitem/9119)
- Excel5 simple formula parsing error - @Erik Tilt [CodePlex #9132](https://phpexcel.codeplex.com/workitem/9132)
- Problems writing dates with CSV - @Erik Tilt [CodePlex #9206](https://phpexcel.codeplex.com/workitem/9206)
- PHPExcel_Reader_Excel5 reader fails with fatal error when reading group shapes - @Erik Tilt [CodePlex #9203](https://phpexcel.codeplex.com/workitem/9203)
- PHPExcel_Writer_Excel5 fails completely when workbook contains more than 57 colors - @Erik Tilt [CodePlex #9231](https://phpexcel.codeplex.com/workitem/9231)
- PHPExcel_Writer_PDF not compatible with autoload - @Erik Tilt [CodePlex #9244](https://phpexcel.codeplex.com/workitem/9244)
- Fatal error: Call to a member function getNestingLevel() on a non-object in PHPExcel/Reader/Excel5.php on line 690 - @Erik Tilt [CodePlex #9250](https://phpexcel.codeplex.com/workitem/9250)
- Notices when running test 04printing.php on PHP 5.2.8 - @MarkBaker [CodePlex #9246](https://phpexcel.codeplex.com/workitem/9246)
- insertColumn() spawns creation of spurious RowDimension - @MarkBaker [CodePlex #9294](https://phpexcel.codeplex.com/workitem/9294)
- Fix declarations for methods in extended Trend classes - @MarkBaker [CodePlex #9296](https://phpexcel.codeplex.com/workitem/9296)
- Fix to parameters for the FORECAST Statistical Function - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- PDF writer problems with cell height and text wrapping - @MarkBaker [CodePlex #7083](https://phpexcel.codeplex.com/workitem/7083)
- Fix test for calculated value in case the returned result is an array - @MarkBaker
- Column greater than 256 results in corrupt Excel file using PHPExcel_Writer_Excel5 - @Erik Tilt
- Excel Numberformat 0.00 results in non internal decimal places values in toArray() Method - @MarkBaker [CodePlex #9351](https://phpexcel.codeplex.com/workitem/9351)
- setAutoSize not taking into account text rotation - @MB,ET [CodePlex #9356](https://phpexcel.codeplex.com/workitem/9356)
- Call to undefined method PHPExcel_Worksheet_MemoryDrawing::getPath() in PHPExcel/Writer/HTML.php - @Erik Tilt [CodePlex #9372](https://phpexcel.codeplex.com/workitem/9372)


## [1.6.5] - 2009-01-05

### General

- Applied patch 2063 - @MarkBaker
- Optimise Shared Strings - @MarkBaker
- Optimise Cell Sorting - @MarkBaker
- Optimise Style Hashing - @MarkBaker
- UTF-8 enhancements - @Erik Tilt
- PHPExcel_Writer_HTML validation errors against strict HTML 4.01 / CSS 2.1 - @Erik Tilt
- Documented work items 6203 and 8110 in manual - @MarkBaker
- Restructure package hierachy so classes can be found more easily in auto-generated API (from work item 8468) - @Erik Tilt

### Features

- Redirect output to a client's browser: Update recommendation in documentation - @MarkBaker [CodePlex #8806](https://phpexcel.codeplex.com/workitem/8806)
- PHPExcel_Reader_Excel5 support for print gridlines - @Erik Tilt [CodePlex #7897](https://phpexcel.codeplex.com/workitem/7897)
- Screen gridlines support in Excel5 reader/writer - @Erik Tilt [CodePlex #7899](https://phpexcel.codeplex.com/workitem/7899)
- Option for adding image to spreadsheet from image resource in memory - @MB, ET [CodePlex #7552](https://phpexcel.codeplex.com/workitem/7552)
- PHPExcel_Reader_Excel5 style support for BIFF5 files (Excel 5.0 - Excel 95) - @Erik Tilt [CodePlex #7862](https://phpexcel.codeplex.com/workitem/7862)
- PHPExcel_Reader_Excel5 support for user-defined colors and special built-in colors - @Erik Tilt [CodePlex #7918](https://phpexcel.codeplex.com/workitem/7918)
- Support for freeze panes in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #7992](https://phpexcel.codeplex.com/workitem/7992)
- Support for header and footer margins in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #7996](https://phpexcel.codeplex.com/workitem/7996)
- Support for active sheet index in Excel5 reader/writer - @Erik Tilt [CodePlex #7997](https://phpexcel.codeplex.com/workitem/7997)
- Freeze panes not read by PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #7991](https://phpexcel.codeplex.com/workitem/7991)
- Support for screen zoom level (feature request) - @MB, ET [CodePlex #7993](https://phpexcel.codeplex.com/workitem/7993)
- Support for default style in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #8012](https://phpexcel.codeplex.com/workitem/8012)
- Apple iWork / Numbers.app incompatibility - @MarkBaker [CodePlex #8094](https://phpexcel.codeplex.com/workitem/8094)
- Support "between rule" in conditional formatting - @MarkBaker [CodePlex #7931](https://phpexcel.codeplex.com/workitem/7931)
- Comment size, width and height control (feature request) - @MarkBaker [CodePlex #8308](https://phpexcel.codeplex.com/workitem/8308)
- Improve method for storing MERGEDCELLS records in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8418](https://phpexcel.codeplex.com/workitem/8418)
- Support for protectCells() in Excel5 reader/writer - @Erik Tilt [CodePlex #8435](https://phpexcel.codeplex.com/workitem/8435)
- Support for fitToWidth and fitToHeight pagesetup properties in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #8472](https://phpexcel.codeplex.com/workitem/8472)
- Support for setShowSummaryBelow() and setShowSummaryRight() in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8489](https://phpexcel.codeplex.com/workitem/8489)
- Support for Excel 1904 calendar date mode (Mac) - @MarkBaker [CodePlex #8483](https://phpexcel.codeplex.com/workitem/8483)
- Excel5 reader: Support for reading images (bitmaps) - @Erik Tilt [CodePlex #7538](https://phpexcel.codeplex.com/workitem/7538)
- Support for default style in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8787](https://phpexcel.codeplex.com/workitem/8787)
- Modified calculate() method to return either an array or the first value from the array for those functions that return arrays rather than single values (e.g the MMULT and TRANSPOSE function). This performance can be modified based on the $returnArrayAsType which can be set/retrieved by calling the setArrayReturnType() and getArrayReturnType() methods of the PHPExcel_Calculation class. - @MarkBaker

### Bugfixes

- Added ERROR.TYPE Information Function, MMULT Mathematical and Trigonometry Function, and TRANSPOSE Lookup and Reference Function - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- setPrintGridlines(true) not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7896](https://phpexcel.codeplex.com/workitem/7896)
- Incorrect mapping of fill patterns in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7907](https://phpexcel.codeplex.com/workitem/7907)
- setShowGridlines(false) not working with PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #7898](https://phpexcel.codeplex.com/workitem/7898)
- getShowGridlines() gives inverted value when reading sheet with PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #7905](https://phpexcel.codeplex.com/workitem/7905)
- User-defined column width becomes slightly larger after read/write with Excel5 - @Erik Tilt [CodePlex #7944](https://phpexcel.codeplex.com/workitem/7944)
- Incomplete border style support in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7949](https://phpexcel.codeplex.com/workitem/7949)
- Conditional formatting "containsText" read/write results in MS Office Excel 2007 crash - @MarkBaker [CodePlex #7928](https://phpexcel.codeplex.com/workitem/7928)
- All sheets are always selected in output when using PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #7995](https://phpexcel.codeplex.com/workitem/7995)
- COLUMN function warning message during plain read/write - @MarkBaker [CodePlex #8013](https://phpexcel.codeplex.com/workitem/8013)
- setValue(0) results in string data type '0' - @MarkBaker [CodePlex #8155](https://phpexcel.codeplex.com/workitem/8155)
- Styles not removed when removing rows from sheet - @MarkBaker [CodePlex #8226](https://phpexcel.codeplex.com/workitem/8226)
- =IF formula causes fatal error during $objWriter->save() in Excel2007 format - @MarkBaker [CodePlex #8301](https://phpexcel.codeplex.com/workitem/8301)
- Exception thrown reading valid xls file: "Excel file is corrupt. Didn't find CONTINUE record while reading shared strings" - @Erik Tilt [CodePlex #8333](https://phpexcel.codeplex.com/workitem/8333)
- MS Outlook corrupts files generated by PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8320](https://phpexcel.codeplex.com/workitem/8320)
- Undefined method PHPExcel_Worksheet::setFreezePane() in ReferenceHelper.php on line 271 - @MarkBaker [CodePlex #8351](https://phpexcel.codeplex.com/workitem/8351)
- Ampersands (&), left and right angles (<, >) in Rich-Text strings leads to corrupt output using PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #8401](https://phpexcel.codeplex.com/workitem/8401)
- Print header and footer not supporting UTF-8 in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8408](https://phpexcel.codeplex.com/workitem/8408)
- Vertical page breaks not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8463](https://phpexcel.codeplex.com/workitem/8463)
- Missing support for accounting underline types in PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8476](https://phpexcel.codeplex.com/workitem/8476)
- Infinite loops when reading corrupt xls file using PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #8482](https://phpexcel.codeplex.com/workitem/8482)
- Sheet protection password not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8566](https://phpexcel.codeplex.com/workitem/8566)
- PHPExcel_Style_NumberFormat::FORMAT_NUMBER ignored by PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8596](https://phpexcel.codeplex.com/workitem/8596)
- PHPExcel_Reader_Excel5 fails a whole when workbook contains a chart - @Erik Tilt [CodePlex #8781](https://phpexcel.codeplex.com/workitem/8781)
- Occasional loss of column widths using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #8788](https://phpexcel.codeplex.com/workitem/8788)
- Notices while reading formulas with deleted sheet references using PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #8795](https://phpexcel.codeplex.com/workitem/8795)
- Default style not read by PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #8807](https://phpexcel.codeplex.com/workitem/8807)
- Blank rows occupy too much space in file generated by PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #9341](https://phpexcel.codeplex.com/workitem/9341)


## [1.6.4] - 2008-10-27

### Features

- RK record number error in MS developer documentation: 0x007E should be 0x027E - @Erik Tilt [CodePlex #7882](https://phpexcel.codeplex.com/workitem/7882)
- getHighestColumn() returning "@" for blank worksheet causes corrupt output - @MarkBaker [CodePlex #7878](https://phpexcel.codeplex.com/workitem/7878)
- Implement ROW and COLUMN Lookup/Reference Functions (when specified with a parameter) - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Implement initial work on OFFSET Lookup/Reference Function (returning address rather than value at address) - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Excel5 reader: Page margins - @Erik Tilt [CodePlex #7416](https://phpexcel.codeplex.com/workitem/7416)
- Excel5 reader: Header & Footer - @Erik Tilt [CodePlex #7417](https://phpexcel.codeplex.com/workitem/7417)
- Excel5 reader support for page setup (paper size etc.) - @Erik Tilt [CodePlex #7449](https://phpexcel.codeplex.com/workitem/7449)
- Improve speed and memory consumption of PHPExcel_Writer_CSV - @MarkBaker [CodePlex #7445](https://phpexcel.codeplex.com/workitem/7445)
- Better recognition of number format in HTML, CSV, and PDF writer - @MarkBaker [CodePlex #7432](https://phpexcel.codeplex.com/workitem/7432)
- Font support: Superscript and Subscript - @MarkBaker [CodePlex #7485](https://phpexcel.codeplex.com/workitem/7485)
- Excel5 reader font support: Super- and subscript - @Erik Tilt [CodePlex #7509](https://phpexcel.codeplex.com/workitem/7509)
- Excel5 reader style support: Text rotation and stacked text - @Erik Tilt [CodePlex #7521](https://phpexcel.codeplex.com/workitem/7521)
- Excel5 reader: Support for hyperlinks - @Erik Tilt [CodePlex #7530](https://phpexcel.codeplex.com/workitem/7530)
- Import sheet by request - @MB, ET [CodePlex #7557](https://phpexcel.codeplex.com/workitem/7557)
- PHPExcel_Reader_Excel5 support for page breaks - @Erik Tilt [CodePlex #7607](https://phpexcel.codeplex.com/workitem/7607)
- PHPExcel_Reader_Excel5 support for shrink-to-fit - @Erik Tilt [CodePlex #7622](https://phpexcel.codeplex.com/workitem/7622)
- Support for error types - @MB, ET [CodePlex #7675](https://phpexcel.codeplex.com/workitem/7675)
- Excel5 reader true formula support - @Erik Tilt [CodePlex #7388](https://phpexcel.codeplex.com/workitem/7388)
- Support for named ranges (defined names) in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #7701](https://phpexcel.codeplex.com/workitem/7701)
- Support for repeating rows and repeating columns (print titles) in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #7781](https://phpexcel.codeplex.com/workitem/7781)
- Support for print area in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #7783](https://phpexcel.codeplex.com/workitem/7783)
- Excel5 reader and writer support for horizontal and vertical centering of page - @Erik Tilt [CodePlex #7795](https://phpexcel.codeplex.com/workitem/7795)
- Applied patch 1962 - @MarkBaker
- Excel5 reader and writer support for hidden cells (formulas) - @Erik Tilt [CodePlex #7866](https://phpexcel.codeplex.com/workitem/7866)
- Support for indentation in cells (feature request) - @MB, ET [CodePlex #7612](https://phpexcel.codeplex.com/workitem/7612)

### Bugfixes

- Option for reading only specified interval of rows in a sheet - @MB, ET [CodePlex #7828](https://phpexcel.codeplex.com/workitem/7828)
- PHPExcel_Calculation_Functions::DATETIMENOW() and PHPExcel_Calculation_Functions::DATENOW() to force UTC - @MarkBaker [CodePlex #7367](https://phpexcel.codeplex.com/workitem/7367)
- Modified PHPExcel_Shared_Date::FormattedPHPToExcel() and PHPExcel_Shared_Date::ExcelToPHP to force datatype for return values - @MarkBaker [CodePlex #7395](https://phpexcel.codeplex.com/workitem/7395)
- Excel5 reader not producing UTF-8 strings with BIFF5 files - @Erik Tilt [CodePlex #7450](https://phpexcel.codeplex.com/workitem/7450)
- Array constant in formula gives run-time notice with Excel2007 writer - @MarkBaker [CodePlex #7470](https://phpexcel.codeplex.com/workitem/7470)
- PHPExcel_Reader_Excel2007 setReadDataOnly(true) returns Rich-Text - @MarkBaker [CodePlex #7494](https://phpexcel.codeplex.com/workitem/7494)
- PHPExcel_Reader_Excel5 setReadDataOnly(true) returns Rich-Text - @Erik Tilt [CodePlex #7496](https://phpexcel.codeplex.com/workitem/7496)
- Characters before superscript or subscript losing style - @MarkBaker [CodePlex #7497](https://phpexcel.codeplex.com/workitem/7497)
- Subscript not working with HTML writer - @MarkBaker [CodePlex #7507](https://phpexcel.codeplex.com/workitem/7507)
- DefaultColumnDimension not working on first column (A) - @MarkBaker [CodePlex #7508](https://phpexcel.codeplex.com/workitem/7508)
- Negative numbers are stored as text in PHPExcel_Writer_2007 - @MarkBaker [CodePlex #7527](https://phpexcel.codeplex.com/workitem/7527)
- Text rotation and stacked text not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7531](https://phpexcel.codeplex.com/workitem/7531)
- PHPExcel_Shared_Date::isDateTimeFormatCode erroneously says true - @MarkBaker [CodePlex #7536](https://phpexcel.codeplex.com/workitem/7536)
- Different images with same filename in separate directories become duplicates - @MarkBaker [CodePlex #7559](https://phpexcel.codeplex.com/workitem/7559)
- PHPExcel_Reader_Excel5 not returning sheet names as UTF-8 using for Excel 95 files - @Erik Tilt [CodePlex #7568](https://phpexcel.codeplex.com/workitem/7568)
- setAutoSize(true) on empty column gives column width of 10 using PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #7575](https://phpexcel.codeplex.com/workitem/7575)
- setAutoSize(true) on empty column gives column width of 255 using PHPExcel_Writer_Excel5 - @MB, ET [CodePlex #7573](https://phpexcel.codeplex.com/workitem/7573)
- Worksheet_Drawing bug - @MarkBaker [CodePlex #7514](https://phpexcel.codeplex.com/workitem/7514)
- getCalculatedValue() with REPT function causes script to stop - @MarkBaker [CodePlex #7593](https://phpexcel.codeplex.com/workitem/7593)
- getCalculatedValue() with LEN function causes script to stop - @MarkBaker [CodePlex #7594](https://phpexcel.codeplex.com/workitem/7594)
- Explicit fit-to-width (page setup) results in fit-to-height becoming 1 - @MarkBaker [CodePlex #7600](https://phpexcel.codeplex.com/workitem/7600)
- Fit-to-width value of 1 is lost after read/write of Excel2007 spreadsheet - @MarkBaker [CodePlex #7610](https://phpexcel.codeplex.com/workitem/7610)
- Conditional styles not read properly using PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #7516](https://phpexcel.codeplex.com/workitem/7516)
- PHPExcel_Writer_2007: Default worksheet style works only for first sheet - @MarkBaker [CodePlex #7611](https://phpexcel.codeplex.com/workitem/7611)
- Cannot Lock Cells using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #6940](https://phpexcel.codeplex.com/workitem/6940)
- Incorrect cell protection values found when using Excel5 reader - @Erik Tilt [CodePlex #7621](https://phpexcel.codeplex.com/workitem/7621)
- Default row height not working above highest row using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7623](https://phpexcel.codeplex.com/workitem/7623)
- Default column width does not get applied when using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7637](https://phpexcel.codeplex.com/workitem/7637)
- Broken support for UTF-8 string formula results in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #7642](https://phpexcel.codeplex.com/workitem/7642)
- UTF-8 sheet names not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7643](https://phpexcel.codeplex.com/workitem/7643)
- getCalculatedValue() with ISNONTEXT function causes script to stop - @MarkBaker [CodePlex #7631](https://phpexcel.codeplex.com/workitem/7631)
- Missing BIFF3 functions in PHPExcel_Writer_Excel5: USDOLLAR (YEN), FINDB, SEARCHB, REPLACEB, LEFTB, RIGHTB, MIDB, LENB, ASC, DBCS (JIS) - @Erik Tilt [CodePlex #7652](https://phpexcel.codeplex.com/workitem/7652)
- Excel5 reader doesn't read numbers correctly in 64-bit systems - @Erik Tilt [CodePlex #7663](https://phpexcel.codeplex.com/workitem/7663)
- Missing BIFF5 functions in PHPExcel_Writer_Excel5: ISPMT, DATEDIF, DATESTRING, NUMBERSTRING - @Erik Tilt [CodePlex #7667](https://phpexcel.codeplex.com/workitem/7667)
- Missing BIFF8 functions in PHPExcel_Writer_Excel5: GETPIVOTDATA, HYPERLINK, PHONETIC, AVERAGEA, MAXA, MINA, STDEVPA, VARPA, STDEVA, VARA - @Erik Tilt [CodePlex #7668](https://phpexcel.codeplex.com/workitem/7668)
- Wrong host value in PHPExcel_Shared_ZipStreamWrapper::stream_open() - @MarkBaker [CodePlex #7657](https://phpexcel.codeplex.com/workitem/7657)
- PHPExcel_Reader_Excel5 not reading explicitly entered error types in cells - @Erik Tilt [CodePlex #7676](https://phpexcel.codeplex.com/workitem/7676)
- Boolean and error data types not preserved for formula results in PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #7678](https://phpexcel.codeplex.com/workitem/7678)
- PHPExcel_Reader_Excel2007 ignores cell data type - @MarkBaker [CodePlex #7695](https://phpexcel.codeplex.com/workitem/7695)
- PHPExcel_Reader_Excel5 ignores cell data type - @Erik Tilt [CodePlex #7712](https://phpexcel.codeplex.com/workitem/7712)
- PHPExcel_Writer_Excel5 not aware of data type - @Erik Tilt [CodePlex #7587](https://phpexcel.codeplex.com/workitem/7587)
- Long strings sometimes truncated when using PHPExcel_Reader_Excel5 - @Erik Tilt [CodePlex #7713](https://phpexcel.codeplex.com/workitem/7713)
- Direct entry of boolean or error type in cell not supported by PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7727](https://phpexcel.codeplex.com/workitem/7727)
- PHPExcel_Reader_Excel2007: Error reading cell with data type string, date number format, and numeric-like cell value - @MarkBaker [CodePlex #7714](https://phpexcel.codeplex.com/workitem/7714)
- Row and column outlines (group indent level) not showing after using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7735](https://phpexcel.codeplex.com/workitem/7735)
- Missing UTF-8 support in number format codes for PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7737](https://phpexcel.codeplex.com/workitem/7737)
- Missing UTF-8 support with PHPExcel_Writer_Excel5 for explicit string in formula - @Erik Tilt [CodePlex #7750](https://phpexcel.codeplex.com/workitem/7750)
- Problem with class constants in PHPExcel_Style_NumberFormat - @MarkBaker [CodePlex #7726](https://phpexcel.codeplex.com/workitem/7726)
- Sometimes errors with PHPExcel_Reader_Excel5 reading hyperlinks - @Erik Tilt [CodePlex #7758](https://phpexcel.codeplex.com/workitem/7758)
- Hyperlink in cell always results in string data type when using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7759](https://phpexcel.codeplex.com/workitem/7759)
- Excel file with blank sheet seen as broken in MS Office Excel 2007 when created by PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7771](https://phpexcel.codeplex.com/workitem/7771)
- PHPExcel_Reader_Excel5: Incorrect reading of formula with explicit string containing (escaped) double-quote - @Erik Tilt [CodePlex #7785](https://phpexcel.codeplex.com/workitem/7785)
- getCalculatedValue() fails on formula with sheet name containing (escaped) single-quote - @MarkBaker [CodePlex #7787](https://phpexcel.codeplex.com/workitem/7787)
- getCalculatedValue() fails on formula with explicit string containing (escaped) double-quote - @MarkBaker [CodePlex #7786](https://phpexcel.codeplex.com/workitem/7786)
- Problems with simultaneous repeatRowsAtTop and repeatColumnsAtLeft using Excel2007 reader and writer - @MarkBaker [CodePlex #7780](https://phpexcel.codeplex.com/workitem/7780)
- PHPExcel_Reader_Excel5: Error reading formulas with sheet reference containing special characters - @Erik Tilt [CodePlex #7802](https://phpexcel.codeplex.com/workitem/7802)
- Off-sheet references sheet!A1 not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7831](https://phpexcel.codeplex.com/workitem/7831)
- Repeating rows/columns (print titles), print area not working with PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7834](https://phpexcel.codeplex.com/workitem/7834)
- Formula having datetime number format shows as text when using PHPExcel_Writer_Excel5 - @Erik Tilt [CodePlex #7849](https://phpexcel.codeplex.com/workitem/7849)
- Cannot set formula to hidden using applyFromArray() - @MarkBaker [CodePlex #7863](https://phpexcel.codeplex.com/workitem/7863)
- HTML/PDF Writers limited to 26 columns by calculateWorksheetDimension (erroneous comparison in getHighestColumn() method) - @MarkBaker [CodePlex #7805](https://phpexcel.codeplex.com/workitem/7805)
- Formula returning error type is lost when read by PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #7873](https://phpexcel.codeplex.com/workitem/7873)
- PHPExcel_Reader_Excel5: Cell style lost for last column in group of blank cells - @Erik Tilt [CodePlex #7883](https://phpexcel.codeplex.com/workitem/7883)
- Column width sometimes collapses to auto size using Excel2007 reader/writer - @MarkBaker [CodePlex #7886](https://phpexcel.codeplex.com/workitem/7886)
- Data Validation Formula = 0 crashes Excel - @MarkBaker [CodePlex #9343](https://phpexcel.codeplex.com/workitem/9343)


## [1.6.3] - 2008-08-25

### General

- Modified PHPExcel_Shared_Date::PHPToExcel() to force UTC - @MarkBaker [CodePlex #7367](https://phpexcel.codeplex.com/workitem/7367)
- Applied patch 1629 - @MarkBaker
- Applied patch 1644 - @MarkBaker
- Implement repeatRow and repeatColumn in Excel5 writer - @MarkBaker [CodePlex #6485](https://phpexcel.codeplex.com/workitem/6485)

### Features

- Remove scene3d filter in Excel2007 drawing - @MarkBaker [CodePlex #6838](https://phpexcel.codeplex.com/workitem/6838)
- Implement CHOOSE and INDEX Lookup/Reference Functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Implement CLEAN Text Functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Implement YEARFRAC Date/Time Functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Implement 2 options for print/show gridlines - @MarkBaker [CodePlex #6508](https://phpexcel.codeplex.com/workitem/6508)
- Add VLOOKUP function (contribution) - @MarkBaker [CodePlex #7270](https://phpexcel.codeplex.com/workitem/7270)
- Implemented: ShrinkToFit - @MarkBaker [CodePlex #7182](https://phpexcel.codeplex.com/workitem/7182)
- Row heights not updated correctly when inserting new rows - @MarkBaker [CodePlex #7218](https://phpexcel.codeplex.com/workitem/7218)
- Copy worksheets within the same workbook - @MarkBaker [CodePlex #7157](https://phpexcel.codeplex.com/workitem/7157)
- Excel5 reader style support: horizontal and vertical alignment plus text wrap - @Erik Tilt [CodePlex #7290](https://phpexcel.codeplex.com/workitem/7290)
- Excel5 reader support for merged cells - @Erik Tilt [CodePlex #7294](https://phpexcel.codeplex.com/workitem/7294)
- Excel5 reader: Sheet Protection - @Erik Tilt [CodePlex #7296](https://phpexcel.codeplex.com/workitem/7296)
- Excel5 reader: Password for sheet protection - @Erik Tilt [CodePlex #7297](https://phpexcel.codeplex.com/workitem/7297)
- Excel5 reader: Column width - @Erik Tilt [CodePlex #7299](https://phpexcel.codeplex.com/workitem/7299)
- Excel5 reader: Row height - @Erik Tilt [CodePlex #7301](https://phpexcel.codeplex.com/workitem/7301)
- Excel5 reader: Font support - @Erik Tilt [CodePlex #7304](https://phpexcel.codeplex.com/workitem/7304)
- Excel5 reader: support for locked cells - @Erik Tilt [CodePlex #7324](https://phpexcel.codeplex.com/workitem/7324)
- Excel5 reader style support: Fill (background colors and patterns) - @Erik Tilt [CodePlex #7330](https://phpexcel.codeplex.com/workitem/7330)
- Excel5 reader style support: Borders (style and color) - @Erik Tilt [CodePlex #7332](https://phpexcel.codeplex.com/workitem/7332)
- Excel5 reader: Rich-Text support - @Erik Tilt [CodePlex #7346](https://phpexcel.codeplex.com/workitem/7346)
- Read Excel built-in number formats with Excel 2007 reader - @MarkBaker [CodePlex #7313](https://phpexcel.codeplex.com/workitem/7313)
- Excel5 reader: Number format support - @Erik Tilt [CodePlex #7317](https://phpexcel.codeplex.com/workitem/7317)
- Creating a copy of PHPExcel object - @MarkBaker [CodePlex #7362](https://phpexcel.codeplex.com/workitem/7362)
- Excel5 reader: support for row / column outline (group) - @Erik Tilt [CodePlex #7373](https://phpexcel.codeplex.com/workitem/7373)
- Implement default row/column sizes - @MarkBaker [CodePlex #7380](https://phpexcel.codeplex.com/workitem/7380)
- Writer HTML - option to return styles and table separately - @MarkBaker [CodePlex #7364](https://phpexcel.codeplex.com/workitem/7364)

### Bugfixes

- Excel5 reader: Support for remaining built-in number formats - @Erik Tilt [CodePlex #7393](https://phpexcel.codeplex.com/workitem/7393)
- Fixed rounding in HOUR MINUTE and SECOND Time functions, and improved performance for these - @MarkBaker
- Fix to TRIM function - @MarkBaker
- Fixed range validation in TIME Functions.php - @MarkBaker
- EDATE and EOMONTH functions now return date values based on the returnDateType flag - @MarkBaker
- Write date values that are the result of a calculation function correctly as Excel serialized dates rather than PHP serialized date values - @MarkBaker
- Excel2007 reader not always reading boolean correctly - @MarkBaker [CodePlex #6690](https://phpexcel.codeplex.com/workitem/6690)
- Columns above IZ - @MarkBaker [CodePlex #6275](https://phpexcel.codeplex.com/workitem/6275)
- Other locale than English causes Excel2007 writer to produce broken xlsx - @MarkBaker [CodePlex #6853](https://phpexcel.codeplex.com/workitem/6853)
- Typo: Number_fromat in NumberFormat.php - @MarkBaker [CodePlex #7061](https://phpexcel.codeplex.com/workitem/7061)
- Bug in Worksheet_BaseDrawing setWidth() - @MarkBaker [CodePlex #6865](https://phpexcel.codeplex.com/workitem/6865)
- PDF writer collapses column width for merged cells - @MarkBaker [CodePlex #6891](https://phpexcel.codeplex.com/workitem/6891)
- Issues with drawings filenames - @MarkBaker [CodePlex #6867](https://phpexcel.codeplex.com/workitem/6867)
- fromArray() local variable isn't defined - @MarkBaker [CodePlex #7073](https://phpexcel.codeplex.com/workitem/7073)
- PHPExcel_Writer_Excel5->setTempDir() not passed to all classes involved in writing to a file - @MarkBaker [CodePlex #7276](https://phpexcel.codeplex.com/workitem/7276)
- Excel5 reader not handling UTF-8 properly - @MarkBaker [CodePlex #7277](https://phpexcel.codeplex.com/workitem/7277)
- If you write a 0 value in cell, cell shows as empty - @MarkBaker [CodePlex #7327](https://phpexcel.codeplex.com/workitem/7327)
- Excel2007 writer: Row height ignored for empty rows - @MarkBaker [CodePlex #7302](https://phpexcel.codeplex.com/workitem/7302)
- Excel2007 (comments related error) - @MarkBaker [CodePlex #7281](https://phpexcel.codeplex.com/workitem/7281)
- Column width in other locale - @MarkBaker [CodePlex #7345](https://phpexcel.codeplex.com/workitem/7345)
- Excel2007 reader not reading underlined Rich-Text - @MarkBaker [CodePlex #7347](https://phpexcel.codeplex.com/workitem/7347)
- Excel5 reader converting booleans to strings - @Erik Tilt [CodePlex #7357](https://phpexcel.codeplex.com/workitem/7357)
- Recursive Object Memory Leak - @MarkBaker [CodePlex #7365](https://phpexcel.codeplex.com/workitem/7365)
- Excel2007 writer ignoring row dimensions without cells - @MarkBaker [CodePlex #7372](https://phpexcel.codeplex.com/workitem/7372)
- Excel5 reader is converting formatted numbers / dates to strings - @Erik Tilt [CodePlex #7382](https://phpexcel.codeplex.com/workitem/7382)


## [1.6.2] - 2008-06-23

### General

- Document style array values - @MarkBaker [CodePlex #6088](https://phpexcel.codeplex.com/workitem/6088)
- Applied patch 1195 - @MarkBaker
- Redirecting output to a client’s web browser - http headers - @MarkBaker [CodePlex #6178](https://phpexcel.codeplex.com/workitem/6178)
- Improve worksheet garbage collection - @MarkBaker [CodePlex #6187](https://phpexcel.codeplex.com/workitem/6187)
- Functions that return date values can now be configured to return as Excel serialized date/time, PHP serialized date/time, or a PHP date/time object. - @MarkBaker
- Functions that explicitly accept dates as parameters now permit values as Excel serialized date/time, PHP serialized date/time, a valid date string, or a PHP date/time object. - @MarkBaker
- Implement ACOSH, ASINH and ATANH functions for those operating platforms/PHP versions that don't include these functions - @MarkBaker
- Implement ATAN2 logic reversing the arguments as per Excel - @MarkBaker
- Additional validation of parameters for COMBIN - @MarkBaker

### Features

- Fixed validation for CEILING and FLOOR when the value and significance parameters have different signs; and allowed default value of 1 or -1 for significance when in GNUMERIC compatibility mode - @MarkBaker
- Implement ADDRESS, ISLOGICAL, ISTEXT and ISNONTEXT functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Implement COMPLEX, IMAGINARY, IMREAL, IMARGUMENT, IMCONJUGATE, IMABS, IMSUB, IMDIV, IMSUM, IMPRODUCT, IMSQRT, IMEXP, IMLN, IMLOG10, IMLOG2, IMPOWER IMCOS and IMSIN Engineering functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Implement NETWORKDAYS and WORKDAY Date/Time functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
- Make cell column AAA available - @MarkBaker [CodePlex #6100](https://phpexcel.codeplex.com/workitem/6100)
- Mark particular cell as selected when opening Excel - @MarkBaker [CodePlex #6095](https://phpexcel.codeplex.com/workitem/6095)
- Multiple sheets in PDF and HTML - @MarkBaker [CodePlex #6120](https://phpexcel.codeplex.com/workitem/6120)
- Implement PHPExcel_ReaderFactory and PHPExcel_WriterFactory - @MarkBaker [CodePlex #6227](https://phpexcel.codeplex.com/workitem/6227)
- Set image root of PHPExcel_Writer_HTML - @MarkBaker [CodePlex #6249](https://phpexcel.codeplex.com/workitem/6249)
- Enable/disable calculation cache - @MarkBaker [CodePlex #6264](https://phpexcel.codeplex.com/workitem/6264)
- PDF writer and multi-line text - @MarkBaker [CodePlex #6259](https://phpexcel.codeplex.com/workitem/6259)
- Feature request - setCacheExpirationTime() - @MarkBaker [CodePlex #6350](https://phpexcel.codeplex.com/workitem/6350)
- Implement late-binding mechanisms to reduce memory footprint - @JB [CodePlex #6370](https://phpexcel.codeplex.com/workitem/6370)
- Implement shared styles - @JB [CodePlex #6430](https://phpexcel.codeplex.com/workitem/6430)
- Copy sheet from external Workbook to active Workbook - @MarkBaker [CodePlex #6391](https://phpexcel.codeplex.com/workitem/6391)

### Bugfixes

- Functions in Conditional Formatting - @MarkBaker [CodePlex #6428](https://phpexcel.codeplex.com/workitem/6428)
- Default Style in Excel5 - @MarkBaker [CodePlex #6096](https://phpexcel.codeplex.com/workitem/6096)
- Numbers starting with '+' cause Excel 2007 errors - @MarkBaker [CodePlex #6150](https://phpexcel.codeplex.com/workitem/6150)
- ExcelWriter5 is not PHP5 compatible, using it with E_STRICT results in a bunch of errors (applied patches) - @MarkBaker [CodePlex #6092](https://phpexcel.codeplex.com/workitem/6092)
- Error Reader Excel2007 line 653 foreach ($relsDrawing->Relationship as $ele) - @MarkBaker [CodePlex #6179](https://phpexcel.codeplex.com/workitem/6179)
- Worksheet toArray() screws up DATE - @MarkBaker [CodePlex #6229](https://phpexcel.codeplex.com/workitem/6229)
- References to a Richtext cell in a formula - @MarkBaker [CodePlex #6253](https://phpexcel.codeplex.com/workitem/6253)
- insertNewColumnBefore Bug - @MarkBaker [CodePlex #6285](https://phpexcel.codeplex.com/workitem/6285)
- Error reading Excel2007 file with shapes - @MarkBaker [CodePlex #6319](https://phpexcel.codeplex.com/workitem/6319)
- Determine whether date values need conversion from PHP dates to Excel dates before writing to file, based on the data type (float or integer) - @MarkBaker [CodePlex #6302](https://phpexcel.codeplex.com/workitem/6302)
- Fixes to DATE function when it is given negative input parameters - @MarkBaker
- PHPExcel handles empty cells other than Excel - @MarkBaker [CodePlex #6347](https://phpexcel.codeplex.com/workitem/6347)
- PHPExcel handles 0 and "" as being the same - @MarkBaker [CodePlex #6348](https://phpexcel.codeplex.com/workitem/6348)
- Problem Using Excel2007 Reader for Spreadsheets containing images - @MarkBaker [CodePlex #6357](https://phpexcel.codeplex.com/workitem/6357)
- ShowGridLines ignored when reading/writing Excel 2007 - @MarkBaker [CodePlex #6359](https://phpexcel.codeplex.com/workitem/6359)
- Bug With Word Wrap in Excel 2007 Reader - @MarkBaker [CodePlex #6426](https://phpexcel.codeplex.com/workitem/6426)


## [1.6.1] - 2008-04-28

### General

- Fix documentation printing - @MarkBaker [CodePlex #5532](https://phpexcel.codeplex.com/workitem/5532)
- Memory usage improvements - @MarkBaker [CodePlex #5586](https://phpexcel.codeplex.com/workitem/5586)
- Applied patch 990 - @MarkBaker

### Features

- Applied patch 991 - @MarkBaker
- Implement PHPExcel_Reader_Excel5 - @BM [CodePlex #2841](https://phpexcel.codeplex.com/workitem/2841)
- Implement "toArray" and "fromArray" method - @MarkBaker [CodePlex #5564](https://phpexcel.codeplex.com/workitem/5564)
- Read shared formula - @MarkBaker [CodePlex #5665](https://phpexcel.codeplex.com/workitem/5665)
- Read image twoCellAnchor - @MarkBaker [CodePlex #5681](https://phpexcel.codeplex.com/workitem/5681)
- &G Image as bg for headerfooter - @MarkBaker [CodePlex #4446](https://phpexcel.codeplex.com/workitem/4446)
- Implement page layout functionality for Excel5 format - @MarkBaker [CodePlex #5834](https://phpexcel.codeplex.com/workitem/5834)

### Bugfixes

- Feature request: PHPExcel_Writer_PDF - @MarkBaker [CodePlex #6039](https://phpexcel.codeplex.com/workitem/6039)
- DefinedNames null check - @MarkBaker [CodePlex #5517](https://phpexcel.codeplex.com/workitem/5517)
- Hyperlinks should not always have trailing slash - @MarkBaker [CodePlex #5463](https://phpexcel.codeplex.com/workitem/5463)
- Saving Error - Uncaught exception (#REF! named range) - @MarkBaker [CodePlex #5592](https://phpexcel.codeplex.com/workitem/5592)
- Error when creating Zip file on Linux System (Not Windows) - @MarkBaker [CodePlex #5634](https://phpexcel.codeplex.com/workitem/5634)
- Time incorrecly formated - @MarkBaker [CodePlex #5876](https://phpexcel.codeplex.com/workitem/5876)
- Conditional formatting - second rule not applied - @MarkBaker [CodePlex #5914](https://phpexcel.codeplex.com/workitem/5914)
- PHPExcel_Reader_Excel2007 cannot load PHPExcel_Shared_File - @MarkBaker [CodePlex #5978](https://phpexcel.codeplex.com/workitem/5978)
- Output redirection to web browser - @MarkBaker [CodePlex #6020](https://phpexcel.codeplex.com/workitem/6020)


## [1.6.0] - 2008-02-14

### Features

- Use PHPExcel datatypes in formula calculation - @MarkBaker [CodePlex #3156](https://phpexcel.codeplex.com/workitem/3156)
- Center on page when printing - @MarkBaker [CodePlex #5019](https://phpexcel.codeplex.com/workitem/5019)
- Hyperlink to other spreadsheet - @MarkBaker [CodePlex #5099](https://phpexcel.codeplex.com/workitem/5099)
- Set the print area of a worksheet - @MarkBaker [CodePlex #5104](https://phpexcel.codeplex.com/workitem/5104)
- Read "definedNames" property of worksheet - @MarkBaker [CodePlex #5118](https://phpexcel.codeplex.com/workitem/5118)
- Set default style for all cells - @MarkBaker [CodePlex #5338](https://phpexcel.codeplex.com/workitem/5338)
- Named Ranges - @MarkBaker [CodePlex #4216](https://phpexcel.codeplex.com/workitem/4216)

### Bugfixes

- Implement worksheet references (Sheet1!A1) - @MarkBaker [CodePlex #5398](https://phpexcel.codeplex.com/workitem/5398)
- Redirect output to a client's web browser - @MarkBaker [CodePlex #4967](https://phpexcel.codeplex.com/workitem/4967)
- "File Error: data may have been lost." seen in Excel 2007 and Excel 2003 SP3 when opening XLS file - @MarkBaker [CodePlex #5008](https://phpexcel.codeplex.com/workitem/5008)
- Bug in style's getHashCode() - @MarkBaker [CodePlex #5165](https://phpexcel.codeplex.com/workitem/5165)
- PHPExcel_Reader not correctly reading numeric values - @MarkBaker [CodePlex #5165](https://phpexcel.codeplex.com/workitem/5165)
- Text rotation is read incorrectly - @MarkBaker [CodePlex #5324](https://phpexcel.codeplex.com/workitem/5324)
- Enclosure " and data " result a bad data : \" instead of "" - @MarkBaker [CodePlex #5326](https://phpexcel.codeplex.com/workitem/5326)
- Formula parser - IF statement returning array instead of scalar - @MarkBaker [CodePlex #5332](https://phpexcel.codeplex.com/workitem/5332)
- setFitToWidth(nbpage) & setFitToWidth(nbpage) work partially - @MarkBaker [CodePlex #5351](https://phpexcel.codeplex.com/workitem/5351)
- Worksheet::setTitle() causes unwanted renaming - @MarkBaker [CodePlex #5361](https://phpexcel.codeplex.com/workitem/5361)
- Hyperlinks not working. Results in broken xlsx file. - @MarkBaker [CodePlex #5407](https://phpexcel.codeplex.com/workitem/5407)


## [1.5.5] - 2007-12-24

### General

- Grouping Rows - @MarkBaker [CodePlex #4135](https://phpexcel.codeplex.com/workitem/4135)

### Features

- Semi-nightly builds - @MarkBaker [CodePlex #4427](https://phpexcel.codeplex.com/workitem/4427)
- Implement "date" datatype - @MarkBaker [CodePlex #3155](https://phpexcel.codeplex.com/workitem/3155)
- Date format not honored in CSV writer - @MarkBaker [CodePlex #4150](https://phpexcel.codeplex.com/workitem/4150)
- RichText and sharedStrings - @MarkBaker [CodePlex #4199](https://phpexcel.codeplex.com/workitem/4199)
- Implement more Excel calculation functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
    - Addition of DATE, DATEDIF, DATEVALUE, DAY, DAYS360- Implement more Excel calculation functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
    - Addition of AVEDEV, HARMEAN and GEOMEAN
    - Addition of the BINOMDIST (Non-cumulative only), COUNTBLANK, EXPONDIST, FISHER, FISHERINV, NORMDIST, NORMSDIST, PERMUT, POISSON (Non-cumulative only) and STANDARDIZE Statistical Functions
    - Addition of the CEILING, COMBIN, EVEN, FACT, FACTDOUBLE, FLOOR, MULTINOMIAL, ODD, ROUNDDOWN, ROUNDUP, SIGN, SQRTPI and SUMSQ Mathematical Functions
    - Addition of the NORMINV, NORMSINV, CONFIDENCE and SKEW Statistical Functions
    - Addition of the CRITBINOM, HYPGEOMDIST, KURT, LOGINV, LOGNORMDIST, NEGBINOMDIST and WEIBULL Statistical Functions
    - Addition of the LARGE, PERCENTILE, QUARTILE, SMALL and TRIMMEAN Statistical Functions
    - Addition of the BIN2HEX, BIN2OCT, DELTA, ERF, ERFC, GESTEP, HEX2BIN, HEX2DEC, HEX2OCT, OCT2BIN and OCT2HEX Engineering Functions
    - Addition of the CHIDIST, GAMMADIST and GAMMALN Statistical Functions
    - Addition of the GCD, LCM, MROUND and SUBTOTAL Mathematical Functions
    - Addition of the LOWER, PROPER and UPPER Text Functions
    - Addition of the BETADIST and BETAINV Statistical Functions
    - Addition of the CHIINV and GAMMAINV Statistical Functions
    - Addition of the SERIESSUM Mathematical Function
    - Addition of the CHAR, CODE, FIND, LEN, REPT, SEARCH, T, TRIM Text Functions
    - Addition of the FALSE and TRUE Boolean Functions
    - Addition of the TDIST and TINV Statistical Functions
    - Addition of the EDATE, EOMONTH, YEAR, MONTH, TIME, TIMEVALUE, HOUR, MINUTE, SECOND, WEEKDAY, WEEKNUM, NOW, TODAY and Date/Time Function
    - Addition of the BESSELI, BESSELJ, BESSELK and BESSELY Engineering Functions
    - Addition of the SLN and SYD Financial Functions
    - reworked MODE calculation to handle floating point numbers
    - Improved error trapping for invalid input values
    - Fix to SMALL, LARGE, PERCENTILE and TRIMMEAN to eliminate non-numeric values
    - Added CDF to BINOMDIST and POISSON
    - Fix to a potential endless loop in CRITBINOM, together with other bugfixes to the algorithm
    - Fix to SQRTPI so that it will work with a real value parameter rather than just integers
    - Trap for passing negative values to FACT
    - Improved accuracy of the NORMDIST cumulative function, and of the ERF and ERFC functions
    - Replicated Excel data-type and error handling for BIN, DEC, OCT and HEX conversion functions
    - Replicated Excel data-type and error handling for AND and OR Boolean functions
    - Bugfix to MROUND
    - Rework of the DATE, DATEVALUE, DAY, DAYS360 and DATEDIF date/Time functions to use Excel dates rather than straight PHP dates
    - Rework of the AND, OR Boolean functions to ignore string values
    - Rework of the BIN2DEC, BIN2HEX, BIN2OCT, DEC2BIN, DEC2HEX, DEC2OCT Engineering functions to handle two's complement
    - Excel, Gnumeric and OpenOffice Calc compatibility flag for functions
    - Note, not all functions have yet been written to work with the Gnumeric and OpenOffice Calc compatibility flags
    - 1900 or 1904 Calendar flag for date functions
    - Reworked ExcelToPHP date method to handle the Excel 1900 leap year
    - Note that this will not correctly return values prior to 13-Dec-1901 20:45:52 as this is the minimum value that PHP date serial values can handle. If you need to work with dates prior to this, then an ExcelToPHPObject method has been added which will work correctly with values between Excel's 1900 calendar base date of 1-Jan-1900, and 13-Dec-1901
    - Addition of ExcelToPHPObject date method to return a PHP DateTime object from an Excel date serial value
    - PHPToExcel method modified to accept either PHP date serial numbers or PHP DateTime objects
    - Addition of FormattedPHPToExcel which will accept a date and time broken to into year, month, day, hour, minute, second and return an Excel date serial value- Control characters in Excel 2007 - @MarkBaker [CodePlex #4485](https://phpexcel.codeplex.com/workitem/4485)
- BaseDrawing::setWidthAndHeight method request - @MarkBaker [CodePlex #4796](https://phpexcel.codeplex.com/workitem/4796)
- Page Setup -> Print Titles -> Sheet -> 'Rows to repeat at top' - @MarkBaker [CodePlex #4798](https://phpexcel.codeplex.com/workitem/4798)

### Bugfixes

- Comment functionality - @MarkBaker [CodePlex #4433](https://phpexcel.codeplex.com/workitem/4433)
- Undefined variable in PHPExcel_Writer_Serialized - @MarkBaker [CodePlex #4124](https://phpexcel.codeplex.com/workitem/4124)
- Notice: Object of class PHPExcel_RichText could not be converted to int - @MarkBaker [CodePlex #4125](https://phpexcel.codeplex.com/workitem/4125)
- Excel5Writer: utf8 string not converted to utf16 - @MarkBaker [CodePlex #4126](https://phpexcel.codeplex.com/workitem/4126)
- PHPExcel_RichText and autosize - @MarkBaker [CodePlex #4180](https://phpexcel.codeplex.com/workitem/4180)
- Excel5Writer produces broken xls files after change mentioned in work item 4126 - @MarkBaker [CodePlex #4574](https://phpexcel.codeplex.com/workitem/4574)
- Small bug in PHPExcel_Reader_Excel2007 function _readStyle - @MarkBaker [CodePlex #4797](https://phpexcel.codeplex.com/workitem/4797)


## [1.5.0] - 2007-10-23

### Features

- Refactor PHPExcel Drawing - @MarkBaker [CodePlex #3265](https://phpexcel.codeplex.com/workitem/3265)
- Update Shared/OLE.php to latest version from PEAR - @CS [CodePlex #3079](https://phpexcel.codeplex.com/workitem/3079)
- Excel2007 vs Excel2003 compatibility pack - @MarkBaker [CodePlex #3217](https://phpexcel.codeplex.com/workitem/3217)
- Cell protection (lock/unlock) - @MarkBaker [CodePlex #3234](https://phpexcel.codeplex.com/workitem/3234)
- Create clickable links (hyperlinks) - @MarkBaker [CodePlex #3543](https://phpexcel.codeplex.com/workitem/3543)
- Additional page setup parameters - @MarkBaker [CodePlex #3241](https://phpexcel.codeplex.com/workitem/3241)
- Make temporary file path configurable (Excel5) - @MarkBaker [CodePlex #3300](https://phpexcel.codeplex.com/workitem/3300)
- Small addition to applyFromArray for font - @MarkBaker [CodePlex #3306](https://phpexcel.codeplex.com/workitem/3306)

### Bugfixes

- Better feedback when save of file is not possible - @MarkBaker [CodePlex #3373](https://phpexcel.codeplex.com/workitem/3373)
- Text Rotation - @MarkBaker [CodePlex #3181](https://phpexcel.codeplex.com/workitem/3181)
- Small bug in Page Orientation - @MarkBaker [CodePlex #3237](https://phpexcel.codeplex.com/workitem/3237)
- insertNewColumnBeforeByColumn undefined - @MarkBaker [CodePlex #3812](https://phpexcel.codeplex.com/workitem/3812)
- Sheet references not working in formula (Excel5 Writer) - @MarkBaker [CodePlex #3893](https://phpexcel.codeplex.com/workitem/3893)


## [1.4.5] - 2007-08-23

### General

- Class file endings - @MarkBaker [CodePlex #3003](https://phpexcel.codeplex.com/workitem/3003)
- Different calculation engine improvements - @MarkBaker [CodePlex #3081](https://phpexcel.codeplex.com/workitem/3081)
- Different improvements in PHPExcel_Reader_Excel2007 - @MarkBaker [CodePlex #3082](https://phpexcel.codeplex.com/workitem/3082)

### Features

- Set XML indentation in PHPExcel_Writer_Excel2007 - @MarkBaker [CodePlex #3146](https://phpexcel.codeplex.com/workitem/3146)
- Optionally store temporary Excel2007 writer data in file instead of memory - @MarkBaker [CodePlex #3159](https://phpexcel.codeplex.com/workitem/3159)
- Implement show/hide gridlines - @MarkBaker [CodePlex #3063](https://phpexcel.codeplex.com/workitem/3063)
- Implement option to read only data - @MarkBaker [CodePlex #3064](https://phpexcel.codeplex.com/workitem/3064)
- Optionally disable formula precalculation - @MarkBaker [CodePlex #3080](https://phpexcel.codeplex.com/workitem/3080)
- Explicitly set cell datatype - @MarkBaker [CodePlex #3154](https://phpexcel.codeplex.com/workitem/3154)

### Bugfixes

- Implement more Excel calculation functions - @MarkBaker [CodePlex #2346](https://phpexcel.codeplex.com/workitem/2346)
    - Addition of MINA, MAXA, COUNTA, AVERAGEA, MEDIAN, MODE, DEVSQ, STDEV, STDEVA, STDEVP, STDEVPA, VAR, VARA, VARP and VARPA Excel Functions
    - Fix to SUM, PRODUCT, QUOTIENT, MIN, MAX, COUNT and AVERAGE functions when cell contains a numeric value in a string datatype, bringing it in line with MS Excel behaviour- File_exists on ZIP fails on some installations - @MarkBaker [CodePlex #2881](https://phpexcel.codeplex.com/workitem/2881)
- Argument in textRotation should be -90..90 - @MarkBaker [CodePlex #2879](https://phpexcel.codeplex.com/workitem/2879)
- Excel2007 reader/writer not implementing OpenXML/SpreadsheetML styles 100% correct - @MarkBaker [CodePlex #2883](https://phpexcel.codeplex.com/workitem/2883)
- Active sheet index not read/saved - @MarkBaker [CodePlex #2513](https://phpexcel.codeplex.com/workitem/2513)
- Print and print preview of generated XLSX causes Excel2007 to crash - @MarkBaker [CodePlex #2935](https://phpexcel.codeplex.com/workitem/2935)
- Error in Calculations - COUNT() function - @MarkBaker [CodePlex #2952](https://phpexcel.codeplex.com/workitem/2952)
- HTML and CSV writer not writing last row - @MarkBaker [CodePlex #3002](https://phpexcel.codeplex.com/workitem/3002)
- Memory leak in Excel5 writer - @MarkBaker [CodePlex #3017](https://phpexcel.codeplex.com/workitem/3017)
- Printing (PHPExcel_Writer_Excel5) - @MarkBaker [CodePlex #3044](https://phpexcel.codeplex.com/workitem/3044)
- Problems reading zip:// - @MarkBaker [CodePlex #3046](https://phpexcel.codeplex.com/workitem/3046)
- Error reading conditional formatting - @MarkBaker [CodePlex #3047](https://phpexcel.codeplex.com/workitem/3047)
- Bug in Excel5 writer (storePanes) - @MarkBaker [CodePlex #3067](https://phpexcel.codeplex.com/workitem/3067)
- Memory leak in PHPExcel_Style_Color - @MarkBaker [CodePlex #3077](https://phpexcel.codeplex.com/workitem/3077)


## [1.4.0] - 2007-07-23

### General

- Coding convention / code cleanup - @MarkBaker [CodePlex #2687](https://phpexcel.codeplex.com/workitem/2687)
- Use set_include_path in tests - @MarkBaker [CodePlex #2717](https://phpexcel.codeplex.com/workitem/2717)

### Features

- Move PHPExcel_Writer_Excel5 OLE to PHPExcel_Shared_OLE - @MarkBaker [CodePlex #2812](https://phpexcel.codeplex.com/workitem/2812)
- Hide/Unhide Column or Row - @MarkBaker [CodePlex #2679](https://phpexcel.codeplex.com/workitem/2679)
- Implement multi-cell styling - @MarkBaker [CodePlex #2271](https://phpexcel.codeplex.com/workitem/2271)
- Implement CSV file format (reader/writer) - @MarkBaker [CodePlex #2720](https://phpexcel.codeplex.com/workitem/2720)

### Bugfixes

- Implement HTML file format - @MarkBaker [CodePlex #2845](https://phpexcel.codeplex.com/workitem/2845)
- Active sheet index not read/saved - @MarkBaker [CodePlex #2513](https://phpexcel.codeplex.com/workitem/2513)
- Freeze Panes with PHPExcel_Writer_Excel5 - @MarkBaker [CodePlex #2678](https://phpexcel.codeplex.com/workitem/2678)
- OLE.php - @MarkBaker [CodePlex #2680](https://phpexcel.codeplex.com/workitem/2680)
- Copy and pasting multiple drop-down list cells breaks reader - @MarkBaker [CodePlex #2736](https://phpexcel.codeplex.com/workitem/2736)
- Function setAutoFilterByColumnAndRow takes wrong arguments - @MarkBaker [CodePlex #2775](https://phpexcel.codeplex.com/workitem/2775)
- Simplexml_load_file fails on ZipArchive - @MarkBaker [CodePlex #2858](https://phpexcel.codeplex.com/workitem/2858)


## [1.3.5] - 2007-06-27

### Features

- Documentation - @MarkBaker [CodePlex #15](https://phpexcel.codeplex.com/workitem/15)
- PHPExcel_Writer_Excel5 - @JV
- PHPExcel_Reader_Excel2007: Image shadows - @JV
- Data validation - @MarkBaker [CodePlex #2385](https://phpexcel.codeplex.com/workitem/2385)

### Bugfixes

- Implement richtext strings - @MarkBaker
- Empty relations when adding image to any sheet but the first one - @MarkBaker [CodePlex #2443](https://phpexcel.codeplex.com/workitem/2443)
- Excel2007 crashes on print preview - @MarkBaker [CodePlex #2536](https://phpexcel.codeplex.com/workitem/2536)


## [1.3.0] - 2007-06-05

### General

- Create PEAR package - @MarkBaker [CodePlex #1942](https://phpexcel.codeplex.com/workitem/1942)

### Features

- Replace *->duplicate() by __clone() - @MarkBaker [CodePlex #2331](https://phpexcel.codeplex.com/workitem/2331)
- PHPExcel_Reader_Excel2007: Column auto-size, Protection, Merged cells, Wrap text, Page breaks, Auto filter, Images - @JV
- Implement "freezing" panes - @MarkBaker [CodePlex #245](https://phpexcel.codeplex.com/workitem/245)
- Cell addressing alternative - @MarkBaker [CodePlex #2273](https://phpexcel.codeplex.com/workitem/2273)
- Implement cell word-wrap attribute - @MarkBaker [CodePlex #2270](https://phpexcel.codeplex.com/workitem/2270)
- Auto-size column - @MarkBaker [CodePlex #2282](https://phpexcel.codeplex.com/workitem/2282)
- Implement formula calculation - @MarkBaker [CodePlex #241](https://phpexcel.codeplex.com/workitem/241)

### Bugfixes

- Insert/remove row/column - @MarkBaker [CodePlex #2375](https://phpexcel.codeplex.com/workitem/2375)
- PHPExcel_Worksheet::getCell() should not accept absolute coordinates - @MarkBaker [CodePlex #1931](https://phpexcel.codeplex.com/workitem/1931)
- Cell reference without row number - @MarkBaker [CodePlex #2272](https://phpexcel.codeplex.com/workitem/2272)
- Styles with same coordinate but different worksheet - @MarkBaker [CodePlex #2276](https://phpexcel.codeplex.com/workitem/2276)
- PHPExcel_Worksheet->getCellCollection() usort error - @MarkBaker [CodePlex #2290](https://phpexcel.codeplex.com/workitem/2290)
- Bug in PHPExcel_Cell::stringFromColumnIndex - @SS [CodePlex #2353](https://phpexcel.codeplex.com/workitem/2353)
- Reader: numFmts can be missing, use cellStyleXfs instead of cellXfs in styles - @JV [CodePlex #2353](https://phpexcel.codeplex.com/workitem/2353)


## [1.2.0] - 2007-04-26

### General

- Stringtable attribute "count" not necessary, provides wrong info to Excel sometimes... - @MarkBaker
- Updated tests to address more document properties - @MarkBaker
- Some refactoring in PHPExcel_Writer_Excel2007_Workbook - @MarkBaker
- New package: PHPExcel_Shared - @MarkBaker
- Password hashing algorithm implemented in PHPExcel_Shared_PasswordHasher - @MarkBaker
- Moved pixel conversion functions to PHPExcel_Shared_Drawing - @MarkBaker
- Switch over to LGPL license - @MarkBaker [CodePlex #244](https://phpexcel.codeplex.com/workitem/244)

### Features

- Include PHPExcel version in file headers - @MarkBaker [CodePlex #5](https://phpexcel.codeplex.com/workitem/5)
- Autofilter - @MarkBaker [CodePlex #6](https://phpexcel.codeplex.com/workitem/6)
- Extra document property: keywords - @MarkBaker [CodePlex #7](https://phpexcel.codeplex.com/workitem/7)
- Extra document property: category - @MarkBaker [CodePlex #8](https://phpexcel.codeplex.com/workitem/8)
- Document security - @MarkBaker [CodePlex #9](https://phpexcel.codeplex.com/workitem/9)
- PHPExcel_Writer_Serialized and PHPExcel_Reader_Serialized - @MarkBaker [CodePlex #10](https://phpexcel.codeplex.com/workitem/10)
- Alternative syntax: Addressing a cell - @MarkBaker [CodePlex #11](https://phpexcel.codeplex.com/workitem/11)
- Merge cells - @MarkBaker [CodePlex #12](https://phpexcel.codeplex.com/workitem/12)

### Bugfixes

- Protect ranges of cells with a password - @MarkBaker [CodePlex #13](https://phpexcel.codeplex.com/workitem/13)
- (style/fill/patternFill/fgColor or bgColor can be empty) - @JV [CodePlex #14](https://phpexcel.codeplex.com/workitem/14)


## [1.1.1] - 2007-03-26

### General

- Syntax error in "Classes/PHPExcel/Writer/Excel2007.php" on line 243 - @MarkBaker [CodePlex #1250](https://phpexcel.codeplex.com/workitem/1250)
- Reader should check if file exists and throws an exception when it doesn't - @MarkBaker [CodePlex #1282](https://phpexcel.codeplex.com/workitem/1282)


## [1.1.0] - 2007-03-22

### Bugfixes

- Style information lost after passing trough Excel2007_Reader - @MarkBaker [CodePlex #836](https://phpexcel.codeplex.com/workitem/836)

### General

- Number of columns > AZ fails fixed in PHPExcel_Cell::columnIndexFromString - @MarkBaker [CodePlex #913](https://phpexcel.codeplex.com/workitem/913)

### Features

- Added a brief file with installation instructions - @MarkBaker
- Page breaks (horizontal and vertical) - @MarkBaker
- Image shadows - @MarkBaker


## [1.0.0] - 2007-02-22

### Bugfixes

- PHPExcel->removeSheetByIndex now re-orders sheets after deletion, so no array indexes are lost - @JV
- PHPExcel_Writer_Excel2007_Worksheet::_writeCols() used direct assignment to $pSheet->getColumnDimension('A')->Width instead of $pSheet->getColumnDimension('A')->setWidth() - @JV
- DocumentProperties used $this->LastModifiedBy instead of $this->_lastModifiedBy. - @JV

### General

- Only first = should be removed when writing formula in PHPExcel_Writer_Excel2007_Worksheet. - @JV
- Consistency of method names to camelCase - @JV
- Updated tests to match consistency changes - @JV
- Detection of mime-types now with image_type_to_mime_type() - @JV
- Constants now hold string value used in Excel 2007 - @JV

### Features

- Fixed folder name case (WorkSheet -> Worksheet) - @MarkBaker
- PHPExcel classes (not the Writer classes) can be duplicated, using a duplicate() method. - @MarkBaker
- Cell styles can now be duplicated to a range of cells using PHPExcel_Worksheet->duplicateStyle() - @MarkBaker
- Conditional formatting - @MarkBaker
- Reader for Excel 2007 (not supporting full specification yet!) - @JV


## [1.0.0 RC] - 2007-01-31

- Project name has been changed to PHPExcel
- Project homepage is now http://www.codeplex.com/PHPExcel
- Started versioning at number: PHPExcel 1.0.0 RC


## 2007-01-22

- Fixed some performance issues on large-scale worksheets (mainly loops vs. indexed arrays)
- Performance on creating StringTable has been increased
- Performance on writing Excel2007 worksheet has been increased


## 2007-01-18

- Images can now be rotated
- Fixed bug: When drawings have full path specified, no mime type can be deducted
- Fixed bug: Only one drawing can be added to a worksheet


## 2007-01-12

- Refactoring of some classes to use ArrayObject instead of array()
- Cell style now has support for number format (i.e. #,##0)
- Implemented embedding images


## 2007-01-02

- Cell style now has support for fills, including gradient fills
- Cell style now has support for fonts
- Cell style now has support for border colors
- Cell style now has support for font colors
- Cell style now has support for alignment


## 2006-12-21

- Support for cell style borders
- Support for cell styles
- Refactoring of Excel2007 Writer into multiple classes in package SpreadSheet_Writer_Excel2007
- Refactoring of all classes, changed public members to public properties using getter/setter
- Worksheet names are now unique. On duplicate worksheet names, a number is appended.
- Worksheet now has parent SpreadSheet object
- Worksheet now has support for page header and footer
- Worksheet now has support for page margins
- Worksheet now has support for page setup (only Paper size and Orientation)
- Worksheet properties now accessible by using getProperties()
- Worksheet now has support for row and column dimensions (height / width)
- Exceptions thrown have a more clear description


## Initial version

- Create a Spreadsheet object
- Add one or more Worksheet objects
- Add cells to Worksheet objects
- Export Spreadsheet object to Excel 2007 OpenXML format
- Each cell supports the following data formats: string, number, formula, boolean.
