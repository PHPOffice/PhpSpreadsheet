# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com)
and this project adheres to [Semantic Versioning](https://semver.org).

## [1.5.1] - 2018-11-20

### Security

- Fix and improve XXE security scanning for XML-based and HTML Readers - [#771](https://github.com/PHPOffice/PhpSpreadsheet/issues/771)

### Added

- Support page margin in mPDF - [#750](https://github.com/PHPOffice/PhpSpreadsheet/issues/750)

### Fixed

- Support numeric condition in SUMIF, SUMIFS, AVERAGEIF, COUNTIF, MAXIF and MINIF - [#683](https://github.com/PHPOffice/PhpSpreadsheet/issues/683)
- SUMIFS containing multiple conditions - [#704](https://github.com/PHPOffice/PhpSpreadsheet/issues/704)
- Csv reader avoid notice when the file is empty - [#743](https://github.com/PHPOffice/PhpSpreadsheet/pull/743)
- Fix print area parser for XLSX reader - [#734](https://github.com/PHPOffice/PhpSpreadsheet/pull/734)
- Support overriding `DefaultValueBinder::dataTypeForValue()` without overriding `DefaultValueBinder::bindValue()` - [#735](https://github.com/PHPOffice/PhpSpreadsheet/pull/735)
- Mpdf export can exceed pcre.backtrack_limit - [#637](https://github.com/PHPOffice/PhpSpreadsheet/issues/637)
- Fix index overflow on data values array - [#748](https://github.com/PHPOffice/PhpSpreadsheet/pull/748)

## [1.5.0] - 2018-10-21

### Added

- PHP 7.3 support
- Add the DAYS() function - [#594](https://github.com/PHPOffice/PhpSpreadsheet/pull/594)

### Fixed

- Sheet title can contain exclamation mark - [#325](https://github.com/PHPOffice/PhpSpreadsheet/issues/325)
- Xls file cause the exception during open by Xls reader - [#402](https://github.com/PHPOffice/PhpSpreadsheet/issues/402)
- Skip non numeric value in SUMIF - [#618](https://github.com/PHPOffice/PhpSpreadsheet/pull/618)
- OFFSET should allow omitted height and width - [#561](https://github.com/PHPOffice/PhpSpreadsheet/issues/561)
- Correctly determine delimiter when CSV contains line breaks inside enclosures - [#716](https://github.com/PHPOffice/PhpSpreadsheet/issues/716)

## [1.4.1] - 2018-09-30

### Fixed

- Remove locale from formatting string - [#644](https://github.com/PHPOffice/PhpSpreadsheet/pull/644)
- Allow iterators to go out of bounds with prev - [#587](https://github.com/PHPOffice/PhpSpreadsheet/issues/587)
- Fix warning when reading xlsx without styles - [#631](https://github.com/PHPOffice/PhpSpreadsheet/pull/631)
- Fix broken sample links on windows due to $baseDir having backslash - [#653](https://github.com/PHPOffice/PhpSpreadsheet/pull/653)

## [1.4.0] - 2018-08-06

### Added

- Add excel function EXACT(value1, value2) support - [#595](https://github.com/PHPOffice/PhpSpreadsheet/pull/595)
- Support workbook view attributes for Xlsx format - [#523](https://github.com/PHPOffice/PhpSpreadsheet/issues/523)
- Read and write hyperlink for drawing image - [#490](https://github.com/PHPOffice/PhpSpreadsheet/pull/490)
- Added calculation engine support for the new bitwise functions that were added in MS Excel 2013
  - BITAND()          Returns a Bitwise 'And' of two numbers
  - BITOR()           Returns a Bitwise 'Or' of two number
  - BITXOR()          Returns a Bitwise 'Exclusive Or' of two numbers
  - BITLSHIFT()       Returns a number shifted left by a specified number of bits
  - BITRSHIFT()       Returns a number shifted right by a specified number of bits
- Added calculation engine support for other new functions that were added in MS Excel 2013 and MS Excel 2016
  - Text Functions
    - CONCAT()        Synonym for CONCATENATE()
    - NUMBERVALUE()   Converts text to a number, in a locale-independent way
    - UNICHAR()       Synonym for CHAR() in PHPSpreadsheet, which has always used UTF-8 internally
    - UNIORD()        Synonym for ORD() in PHPSpreadsheet, which has always used UTF-8 internally
    - TEXTJOIN()      Joins together two or more text strings, separated by a delimiter
  - Logical Functions
    - XOR()           Returns a logical Exclusive Or of all arguments
  - Date/Time Functions
    - ISOWEEKNUM()    Returns the ISO 8601 week number of the year for a given date
  - Lookup and Reference Functions
    - FORMULATEXT()   Returns a formula as a string
  - Financial Functions
    - PDURATION()     Calculates the number of periods required for an investment to reach a specified value
    - RRI()           Calculates the interest rate required for an investment to grow to a specified future value
  - Engineering Functions
    - ERF.PRECISE()   Returns the error function integrated between 0 and a supplied limit
    - ERFC.PRECISE()  Synonym for ERFC
  - Math and Trig Functions
    - SEC()           Returns the secant of an angle
    - SECH()          Returns the hyperbolic secant of an angle
    - CSC()           Returns the cosecant of an angle
    - CSCH()          Returns the hyperbolic cosecant of an angle
    - COT()           Returns the cotangent of an angle
    - COTH()          Returns the hyperbolic cotangent of an angle
    - ACOT()          Returns the cotangent of an angle
    - ACOTH()         Returns the hyperbolic cotangent of an angle
- Refactored Complex Engineering Functions to use external complex number library
- Added calculation engine support for the new complex number functions that were added in MS Excel 2013
    - IMCOSH()        Returns the hyperbolic cosine of a complex number
    - IMCOT()         Returns the cotangent of a complex number
    - IMCSC()         Returns the cosecant of a complex number
    - IMCSCH()        Returns the hyperbolic cosecant of a complex number
    - IMSEC()         Returns the secant of a complex number
    - IMSECH()        Returns the hyperbolic secant of a complex number
    - IMSINH()        Returns the hyperbolic sine of a complex number
    - IMTAN()         Returns the tangent of a complex number 

### Fixed

- Fix ISFORMULA() function to work with a cell reference to another worksheet
- Xlsx reader crashed when reading a file with workbook protection - [#553](https://github.com/PHPOffice/PhpSpreadsheet/pull/553)
- Cell formats with escaped spaces were causing incorrect date formatting - [#557](https://github.com/PHPOffice/PhpSpreadsheet/issues/557)
- Could not open CSV file containing HTML fragment - [#564](https://github.com/PHPOffice/PhpSpreadsheet/issues/564)
- Exclude the vendor folder in migration - [#481](https://github.com/PHPOffice/PhpSpreadsheet/issues/481)
- Chained operations on cell ranges involving borders operated on last cell only [#428](https://github.com/PHPOffice/PhpSpreadsheet/issues/428)
- Avoid memory exhaustion when cloning worksheet with a drawing [#437](https://github.com/PHPOffice/PhpSpreadsheet/issues/437)
- Migration tool keep variables containing $PHPExcel untouched [#598](https://github.com/PHPOffice/PhpSpreadsheet/issues/598)
- Rowspans/colspans were incorrect when adding worksheet using loadIntoExisting [#619](https://github.com/PHPOffice/PhpSpreadsheet/issues/619)

## [1.3.1] - 2018-06-12

### Fixed

- Ranges across Z and AA columns incorrectly threw an exception - [#545](https://github.com/PHPOffice/PhpSpreadsheet/issues/545)

## [1.3.0] - 2018-06-10

### Added

- Support to read Xlsm templates with form elements, macros, printer settings, protected elements and back compatibility drawing, and save result without losing important elements of document - [#435](https://github.com/PHPOffice/PhpSpreadsheet/issues/435)
- Expose sheet title maximum length as `Worksheet::SHEET_TITLE_MAXIMUM_LENGTH` - [#482](https://github.com/PHPOffice/PhpSpreadsheet/issues/482)
- Allow escape character to be set in CSV reader – [#492](https://github.com/PHPOffice/PhpSpreadsheet/issues/492)

### Fixed

- Subtotal 9 in a group that has other subtotals 9 exclude the totals of the other subtotals in the range - [#332](https://github.com/PHPOffice/PhpSpreadsheet/issues/332)
- `Helper\Html` support UTF-8 HTML input - [#444](https://github.com/PHPOffice/PhpSpreadsheet/issues/444)
- Xlsx loaded an extra empty comment for each real comment - [#375](https://github.com/PHPOffice/PhpSpreadsheet/issues/375)
- Xlsx reader do not read rows and columns filtered out in readFilter at all - [#370](https://github.com/PHPOffice/PhpSpreadsheet/issues/370)
- Make newer Excel versions properly recalculate formulas on document open - [#456](https://github.com/PHPOffice/PhpSpreadsheet/issues/456)
- `Coordinate::extractAllCellReferencesInRange()` throws an exception for an invalid range – [#519](https://github.com/PHPOffice/PhpSpreadsheet/issues/519)
- Fixed parsing of conditionals in COUNTIF functions - [#526](https://github.com/PHPOffice/PhpSpreadsheet/issues/526)
- Corruption errors for saved Xlsx docs with frozen panes - [#532](https://github.com/PHPOffice/PhpSpreadsheet/issues/532)

## [1.2.1] - 2018-04-10

### Fixed

- Plain text and richtext mixed in same cell can be read - [#442](https://github.com/PHPOffice/PhpSpreadsheet/issues/442)

## [1.2.0] - 2018-03-04

### Added

- HTML writer creates a generator meta tag - [#312](https://github.com/PHPOffice/PhpSpreadsheet/issues/312)
- Support invalid zoom value in XLSX format - [#350](https://github.com/PHPOffice/PhpSpreadsheet/pull/350)
- Support for `_xlfn.` prefixed functions and `ISFORMULA`, `MODE.SNGL`, `STDEV.S`, `STDEV.P` - [#390](https://github.com/PHPOffice/PhpSpreadsheet/pull/390)

### Fixed

- Avoid potentially unsupported PSR-16 cache keys - [#354](https://github.com/PHPOffice/PhpSpreadsheet/issues/354)
- Check for MIME type to know if CSV reader can read a file - [#167](https://github.com/PHPOffice/PhpSpreadsheet/issues/167)
- Use proper € symbol for currency format - [#379](https://github.com/PHPOffice/PhpSpreadsheet/pull/379)
- Read printing area correctly when skipping some sheets - [#371](https://github.com/PHPOffice/PhpSpreadsheet/issues/371)
- Avoid incorrectly overwriting calculated value type - [#394](https://github.com/PHPOffice/PhpSpreadsheet/issues/394)
- Select correct cell when calling freezePane - [#389](https://github.com/PHPOffice/PhpSpreadsheet/issues/389)
- `setStrikethrough()` did not set the font - [#403](https://github.com/PHPOffice/PhpSpreadsheet/issues/403)

## [1.1.0] - 2018-01-28

### Added

- Support for PHP 7.2
- Support cell comments in HTML writer and reader - [#308](https://github.com/PHPOffice/PhpSpreadsheet/issues/308)
- Option to stop at a conditional styling, if it matches (only XLSX format) - [#292](https://github.com/PHPOffice/PhpSpreadsheet/pull/292)
- Support for line width for data series when rendering Xlsx - [#329](https://github.com/PHPOffice/PhpSpreadsheet/pull/329)

### Fixed

- Better auto-detection of CSV separators - [#305](https://github.com/PHPOffice/PhpSpreadsheet/issues/305)
- Support for shape style ending with `;` - [#304](https://github.com/PHPOffice/PhpSpreadsheet/issues/304)
- Freeze Panes takes wrong coordinates for XLSX - [#322](https://github.com/PHPOffice/PhpSpreadsheet/issues/322)
- `COLUMNS` and `ROWS` functions crashed in some cases - [#336](https://github.com/PHPOffice/PhpSpreadsheet/issues/336)
- Support XML file without styles - [#331](https://github.com/PHPOffice/PhpSpreadsheet/pull/331)
- Cell coordinates which are already a range cause an exception [#319](https://github.com/PHPOffice/PhpSpreadsheet/issues/319)

## [1.0.0] - 2017-12-25

### Added

- Support to write merged cells in ODS format - [#287](https://github.com/PHPOffice/PhpSpreadsheet/issues/287)
- Able to set the `topLeftCell` in freeze panes - [#261](https://github.com/PHPOffice/PhpSpreadsheet/pull/261)
- Support `DateTimeImmutable` as cell value
- Support migration of prefixed classes

### Fixed

- Can read very small HTML files - [#194](https://github.com/PHPOffice/PhpSpreadsheet/issues/194)
- Written DataValidation was corrupted - [#290](https://github.com/PHPOffice/PhpSpreadsheet/issues/290)
- Date format compatible with both LibreOffice and Excel - [#298](https://github.com/PHPOffice/PhpSpreadsheet/issues/298)

### BREAKING CHANGE

- Constant `TYPE_DOUGHTNUTCHART` is now `TYPE_DOUGHNUTCHART`.

## [1.0.0-beta2] - 2017-11-26

### Added

- Support for chart fill color - @CrazyBite [#158](https://github.com/PHPOffice/PhpSpreadsheet/pull/158)
- Support for read Hyperlink for xml - @GreatHumorist [#223](https://github.com/PHPOffice/PhpSpreadsheet/pull/223)
- Support for cell value validation according to data validation rules - @SailorMax [#257](https://github.com/PHPOffice/PhpSpreadsheet/pull/257)
- Support for custom implementation, or configuration, of PDF libraries - @SailorMax [#266](https://github.com/PHPOffice/PhpSpreadsheet/pull/266)

### Changed

- Merge data-validations to reduce written worksheet size - @billblume [#131](https://github.com/PHPOffice/PhpSpreadSheet/issues/131)
- Throws exception if a XML file is invalid - @GreatHumorist [#222](https://github.com/PHPOffice/PhpSpreadsheet/pull/222)
- Upgrade to mPDF 7.0+ - [#144](https://github.com/PHPOffice/PhpSpreadsheet/issues/144)

### Fixed

- Control characters in cell values are automatically escaped - [#212](https://github.com/PHPOffice/PhpSpreadsheet/issues/212)
- Prevent color changing when copy/pasting xls files written by PhpSpreadsheet to another file - @al-lala [#218](https://github.com/PHPOffice/PhpSpreadsheet/issues/218)
- Add cell reference automatic when there is no cell reference('r' attribute) in Xlsx file. - @GreatHumorist [#225](https://github.com/PHPOffice/PhpSpreadsheet/pull/225) Refer to [issue#201](https://github.com/PHPOffice/PhpSpreadsheet/issues/201)
- `Reader\Xlsx::getFromZipArchive()` function return false if the zip entry could not be located. - @anton-harvey [#268](https://github.com/PHPOffice/PhpSpreadsheet/pull/268)

### BREAKING CHANGE

- Extracted coordinate method to dedicate class [migration guide](./docs/topics/migration-from-PHPExcel.md).
- Column indexes are based on 1, see the [migration guide](./docs/topics/migration-from-PHPExcel.md).
- Standardization of array keys used for style, see the [migration guide](./docs/topics/migration-from-PHPExcel.md).
- Easier usage of PDF writers, and other custom readers and writers, see the [migration guide](./docs/topics/migration-from-PHPExcel.md).
- Easier usage of chart renderers, see the [migration guide](./docs/topics/migration-from-PHPExcel.md).
- Rename a few more classes to keep them in their related namespaces:
    - `CalcEngine` => `Calculation\Engine`
    - `PhpSpreadsheet\Calculation` => `PhpSpreadsheet\Calculation\Calculation`
    - `PhpSpreadsheet\Cell` => `PhpSpreadsheet\Cell\Cell`
    - `PhpSpreadsheet\Chart` => `PhpSpreadsheet\Chart\Chart`
    - `PhpSpreadsheet\RichText` => `PhpSpreadsheet\RichText\RichText`
    - `PhpSpreadsheet\Style` => `PhpSpreadsheet\Style\Style`
    - `PhpSpreadsheet\Worksheet` => `PhpSpreadsheet\Worksheet\Worksheet`

## [1.0.0-beta] - 2017-08-17

### Added

- Initial implementation of SUMIFS() function
- Additional codepages
- MemoryDrawing not working in HTML writer [#808](https://github.com/PHPOffice/PHPExcel/issues/808)
- CSV Reader can auto-detect the separator used in file [#141](https://github.com/PHPOffice/PhpSpreadsheet/pull/141)
- HTML Reader supports some basic inline styles [#180](https://github.com/PHPOffice/PhpSpreadsheet/pull/180)

### Changed

- Start following [SemVer](https://semver.org) properly.

### Fixed

- Fix to getCell() method when cell reference includes a worksheet reference - @MarkBaker
- Ignore inlineStr type if formula element exists - @ncrypthic [#570](https://github.com/PHPOffice/PHPExcel/issues/570)
- Excel 2007 Reader freezes because of conditional formatting - @rentalhost [#575](https://github.com/PHPOffice/PHPExcel/issues/575)
- Readers will now parse files containing worksheet titles over 31 characters [#176](https://github.com/PHPOffice/PhpSpreadsheet/pull/176)

### General

- Whitespace after toRichTextObject() - @MarkBaker [#554](https://github.com/PHPOffice/PHPExcel/issues/554)
- Optimize vlookup() sort - @umpirsky [#548](https://github.com/PHPOffice/PHPExcel/issues/548)
- c:max and c:min elements shall NOT be inside c:orientation elements - @vitalyrepin [#869](https://github.com/PHPOffice/PHPExcel/pull/869)
- Implement actual timezone adjustment into PHPExcel_Shared_Date::PHPToExcel - @sim642 [#489](https://github.com/PHPOffice/PHPExcel/pull/489)

### BREAKING CHANGE

- Introduction of namespaces for all classes, eg: `PHPExcel_Calculation_Functions` becomes `PhpOffice\PhpSpreadsheet\Calculation\Functions`
- Some classes were renamed for clarity and/or consistency:

For a comprehensive list of all class changes, and a semi-automated migration path, read the [migration guide](./docs/topics/migration-from-PHPExcel.md).

- Dropped `PHPExcel_Calculation_Functions::VERSION()`. Composer or git should be used to know the version.
- Dropped `PHPExcel_Settings::setPdfRenderer()` and `PHPExcel_Settings::setPdfRenderer()`. Composer should be used to autoload PDF libs.
- Dropped support for HHVM

## Previous versions of PHPExcel

The changelog for the project when it was called PHPExcel is [still available](./CHANGELOG.PHPExcel.md).
