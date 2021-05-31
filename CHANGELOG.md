# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com)
and this project adheres to [Semantic Versioning](https://semver.org).

## 1.18.0 - 2021-05-31

### Added
- Enhancements to CSV Reader, allowing options to be set when using `IOFactory::load()` with a callback to set delimiter, enclosure, charset etc. [PR #2103](https://github.com/PHPOffice/PhpSpreadsheet/pull/2103) - See [documentation](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/docs/topics/reading-and-writing-to-file.md#csv-comma-separated-values) for details.
- Implemented basic AutoFiltering for Ods Reader and Writer [PR #2053](https://github.com/PHPOffice/PhpSpreadsheet/pull/2053)
- Implemented basic AutoFiltering for Gnumeric Reader [PR #2055](https://github.com/PHPOffice/PhpSpreadsheet/pull/2055)
- Improved support for Row and Column ranges in formulae [Issue #1755](https://github.com/PHPOffice/PhpSpreadsheet/issues/1755) [PR #2028](https://github.com/PHPOffice/PhpSpreadsheet/pull/2028)
- Implemented URLENCODE() Web Function
- Implemented the CHITEST(), CHISQ.DIST() and CHISQ.INV() and equivalent Statistical functions, for both left- and right-tailed distributions.
- Support for ActiveSheet and SelectedCells in the ODS Reader and Writer. [PR #1908](https://github.com/PHPOffice/PhpSpreadsheet/pull/1908)
- Support for notContainsText Conditional Style in xlsx [Issue #984](https://github.com/PHPOffice/PhpSpreadsheet/issues/984)

### Changed

- Use of `nb` rather than `no` as the locale code for Norsk Bokmål.

### Deprecated

- All Excel Function implementations in `Calculation\Database`, `Calculation\DateTime`, `Calculation\Engineering`, `Calculation\Financial`, `Calculation\Logical`, `Calculation\LookupRef`, `Calculation\MathTrig`, `Calculation\Statistical`, `Calculation\TextData` and `Calculation\Web` have been moved to dedicated classes for individual functions or groups of related functions. See the docblocks against all the deprecated methods for details of the new methods to call instead. At some point, these old classes will be deleted.

### Removed

- Use of `nb` rather than `no` as the locale language code for Norsk Bokmål.

### Fixed
- Fixed error in COUPNCD() calculation for end of month [Issue #2116](https://github.com/PHPOffice/PhpSpreadsheet/issues/2116) - [PR #2119](https://github.com/PHPOffice/PhpSpreadsheet/pull/2119)
- Resolve default values when a null argument is passed for HLOOKUP(), VLOOKUP() and ADDRESS() functions [Issue #2120](https://github.com/PHPOffice/PhpSpreadsheet/issues/2120) - [PR #2121](https://github.com/PHPOffice/PhpSpreadsheet/pull/2121)
- Fixed incorrect R1C1 to A1 subtraction formula conversion (`R[-2]C-R[2]C`) [Issue #2076](https://github.com/PHPOffice/PhpSpreadsheet/pull/2076) [PR #2086](https://github.com/PHPOffice/PhpSpreadsheet/pull/2086)
- Correctly handle absolute A1 references when converting to R1C1 format [PR #2060](https://github.com/PHPOffice/PhpSpreadsheet/pull/2060)
- Correct default fill style for conditional without a pattern defined [Issue #2035](https://github.com/PHPOffice/PhpSpreadsheet/issues/2035) [PR #2050](https://github.com/PHPOffice/PhpSpreadsheet/pull/2050)
- Fixed issue where array key check for existince before accessing arrays in Xlsx.php. [PR #1970](https://github.com/PHPOffice/PhpSpreadsheet/pull/1970)
- Fixed issue with quoted strings in number format mask rendered with toFormattedString() [Issue 1972#](https://github.com/PHPOffice/PhpSpreadsheet/issues/1972) [PR #1978](https://github.com/PHPOffice/PhpSpreadsheet/pull/1978)
- Fixed issue with percentage formats in number format mask rendered with toFormattedString() [Issue 1929#](https://github.com/PHPOffice/PhpSpreadsheet/issues/1929) [PR #1928](https://github.com/PHPOffice/PhpSpreadsheet/pull/1928)
- Fixed issue with _ spacing character in number format mask corrupting output from toFormattedString() [Issue 1924#](https://github.com/PHPOffice/PhpSpreadsheet/issues/1924) [PR #1927](https://github.com/PHPOffice/PhpSpreadsheet/pull/1927)
- Fix for [Issue #1887](https://github.com/PHPOffice/PhpSpreadsheet/issues/1887) - Lose Track of Selected Cells After Save
- Fixed issue with Xlsx@listWorksheetInfo not returning any data
- Fixed invalid arguments triggering mb_substr() error in LEFT(), MID() and RIGHT() text functions. [Issue #640](https://github.com/PHPOffice/PhpSpreadsheet/issues/640)
- Fix for [Issue #1916](https://github.com/PHPOffice/PhpSpreadsheet/issues/1916) - Invalid signature check for XML files
- Fix change in `Font::setSize()` behavior for PHP8. [PR #2100](https://github.com/PHPOffice/PhpSpreadsheet/pull/2100)

## 1.17.1 - 2021-03-01

### Added

- Implementation of the Excel `AVERAGEIFS()` functions as part of a restructuring of Database functions and Conditional Statistical functions.
- Support for date values and percentages in query parameters for Database functions, and the IF expressions in functions like COUNTIF() and AVERAGEIF(). [#1875](https://github.com/PHPOffice/PhpSpreadsheet/pull/1875)
- Support for booleans, and for wildcard text search in query parameters for Database functions, and the IF expressions in functions like COUNTIF() and AVERAGEIF(). [#1876](https://github.com/PHPOffice/PhpSpreadsheet/pull/1876)
- Implemented DataBar for conditional formatting in Xlsx, providing read/write and creation of (type, value, direction, fills, border, axis position, color settings) as DataBar options in Excel. [#1754](https://github.com/PHPOffice/PhpSpreadsheet/pull/1754)
- Alignment for ODS Writer [#1796](https://github.com/PHPOffice/PhpSpreadsheet/issues/1796)
- Basic implementation of the PERMUTATIONA() Statistical Function

### Changed

- Formula functions that previously called PHP functions directly are now processed through the Excel Functions classes; resolving issues with PHP8 stricter typing. [#1789](https://github.com/PHPOffice/PhpSpreadsheet/issues/1789)

  The following MathTrig functions are affected:
  `ABS()`, `ACOS()`, `ACOSH()`, `ASIN()`, `ASINH()`, `ATAN()`, `ATANH()`,
  `COS()`, `COSH()`, `DEGREES()` (rad2deg), `EXP()`, `LN()` (log), `LOG10()`,
  `RADIANS()` (deg2rad), `SIN()`, `SINH()`, `SQRT()`, `TAN()`, `TANH()`.
  
  One TextData function is also affected: `REPT()` (str_repeat).
- `formatAsDate` correctly matches language metadata, reverting c55272e
- Formulae that previously crashed on sub function call returning excel error value now return said value.
  The following functions are affected `CUMPRINC()`, `CUMIPMT()`, `AMORLINC()`,
  `AMORDEGRC()`.
- Adapt some function error return value to match excel's error.
  The following functions are affected `PPMT()`, `IPMT()`.

### Deprecated

- Calling many of the Excel formula functions directly rather than through the Calculation Engine.

  The logic for these Functions is now being moved out of the categorised `Database`, `DateTime`, `Engineering`, `Financial`, `Logical`, `LookupRef`, `MathTrig`, `Statistical`, `TextData` and `Web` classes into small, dedicated classes for individual functions or related groups of functions.

  This makes the logic in these classes easier to maintain; and will reduce the memory footprint required to execute formulae when calling these functions.

### Removed

- Nothing.

### Fixed

- Avoid Duplicate Titles When Reading Multiple HTML Files.[Issue #1823](https://github.com/PHPOffice/PhpSpreadsheet/issues/1823) [PR #1829](https://github.com/PHPOffice/PhpSpreadsheet/pull/1829)
- Fixed issue with Worksheet's `getCell()` method when trying to get a cell by defined name. [#1858](https://github.com/PHPOffice/PhpSpreadsheet/issues/1858)
- Fix possible endless loop in NumberFormat Masks [#1792](https://github.com/PHPOffice/PhpSpreadsheet/issues/1792)
- Fix problem resulting from  literal dot inside quotes in number format masks. [PR #1830](https://github.com/PHPOffice/PhpSpreadsheet/pull/1830)
- Resolve Google Sheets Xlsx charts issue. Google Sheets uses oneCellAnchor positioning and does not include *Cache values in the exported Xlsx. [PR #1761](https://github.com/PHPOffice/PhpSpreadsheet/pull/1761)
- Fix for Xlsx Chart axis titles mapping to correct X or Y axis label when only one is present. [PR #1760](https://github.com/PHPOffice/PhpSpreadsheet/pull/1760)
- Fix For Null Exception on ODS Read of Page Settings. [#1772](https://github.com/PHPOffice/PhpSpreadsheet/issues/1772)
- Fix Xlsx reader overriding manually set number format with builtin number format. [PR #1805](https://github.com/PHPOffice/PhpSpreadsheet/pull/1805)
- Fix Xlsx reader cell alignment. [PR #1710](https://github.com/PHPOffice/PhpSpreadsheet/pull/1710)
- Fix for not yet implemented data-types in Open Document writer [Issue #1674](https://github.com/PHPOffice/PhpSpreadsheet/issues/1674)
- Fix XLSX reader when having a corrupt numeric cell data type [PR #1664](https://github.com/phpoffice/phpspreadsheet/pull/1664)
- Fix on `CUMPRINC()`, `CUMIPMT()`, `AMORLINC()`, `AMORDEGRC()` usage. When those functions called one of `YEARFRAC()`, `PPMT()`, `IPMT()` and they would get back an error value (represented as a string), trying to use numeral operands (`+`, `/`, `-`, `*`) on said return value and a number (`float or `int`) would fail.

## 1.16.0 - 2020-12-31

### Added

- CSV Reader - Best Guess for Encoding, and Handle Null-string Escape [#1647](https://github.com/PHPOffice/PhpSpreadsheet/issues/1647)

### Changed

- Updated the CONVERT() function to support all current MS Excel categories and Units of Measure.

### Deprecated

- All Excel Function implementations in `Calculation\Database`, `Calculation\DateTime`, `Calculation\Engineering`, `Calculation\Financial`, `Calculation\Logical`, `Calculation\LookupRef`, `Calculation\MathTrig`, `Calculation\Statistical`, `Calculation\TextData` and `Calculation\Web` have been moved to dedicated classes for individual functions or groups of related functions. See the docblocks against all the deprecated methods for details of the new methods to call instead. At some point, these old classes will be deleted.

### Removed

- Nothing.

### Fixed

- Fixed issue with absolute path in worksheets' Target. [PR #1769](https://github.com/PHPOffice/PhpSpreadsheet/pull/1769)
- Fix for Xls Reader when SST has a bad length [#1592](https://github.com/PHPOffice/PhpSpreadsheet/issues/1592)
- Resolve Xlsx loader issue whe hyperlinks don't have a destination
- Resolve issues when printer settings resources IDs clash with drawing IDs
- Resolve issue with SLK long filenames [#1612](https://github.com/PHPOffice/PhpSpreadsheet/issues/1612)
- ROUNDUP and ROUNDDOWN return incorrect results for values of 0 [#1627](https://github.com/phpoffice/phpspreadsheet/pull/1627)
- Apply Column and Row Styles to Existing Cells [#1712](https://github.com/PHPOffice/PhpSpreadsheet/issues/1712) [PR #1721](https://github.com/PHPOffice/PhpSpreadsheet/pull/1721)
- Resolve issues with defined names where worksheet doesn't exist (#1686)[https://github.com/PHPOffice/PhpSpreadsheet/issues/1686] and [#1723](https://github.com/PHPOffice/PhpSpreadsheet/issues/1723) - [PR #1742](https://github.com/PHPOffice/PhpSpreadsheet/pull/1742)
- Fix for issue [#1735](https://github.com/PHPOffice/PhpSpreadsheet/issues/1735) Incorrect activeSheetIndex after RemoveSheetByIndex - [PR #1743](https://github.com/PHPOffice/PhpSpreadsheet/pull/1743)
- Ensure that the list of shared formulae is maintained when an xlsx file is chunked with readFilter[Issue #169](https://github.com/PHPOffice/PhpSpreadsheet/issues/1669).
- Fix for notice during accessing "cached magnification factor" offset [#1354](https://github.com/PHPOffice/PhpSpreadsheet/pull/1354)
- Fix compatibility with ext-gd on php 8

### Security Fix (CVE-2020-7776)

- Prevent XSS through cell comments in the HTML Writer.

## 1.15.0 - 2020-10-11

### Added

- Implemented Page Order for Xlsx and Xls Readers, and provided Page Settings (Orientation, Scale, Horizontal/Vertical Centering, Page Order, Margins) support for Ods, Gnumeric and Xls Readers [#1559](https://github.com/PHPOffice/PhpSpreadsheet/pull/1559)
- Implementation of the Excel `LOGNORM.DIST()`, `NORM.S.DIST()`, `GAMMA()` and `GAUSS()` functions. [#1588](https://github.com/PHPOffice/PhpSpreadsheet/pull/1588)
- Named formula implementation, and improved handling of Defined Names generally [#1535](https://github.com/PHPOffice/PhpSpreadsheet/pull/1535)
  - Defined Names are now case-insensitive
  - Distinction between named ranges and named formulae
  - Correct handling of union and intersection operators in named ranges
  - Correct evaluation of named range operators in calculations
  - fix resolution of relative named range values in the calculation engine; previously all named range values had been treated as absolute.
  - Calculation support for named formulae
  - Support for nested ranges and formulae (named ranges and formulae that reference other named ranges/formulae) in calculations
  - Introduction of a helper to convert address formats between R1C1 and A1 (and the reverse)
  - Proper support for both named ranges and named formulae in all appropriate Readers
    - **Xlsx** (Previously only simple named ranges were supported)
    - **Xls** (Previously only simple named ranges were supported)
    - **Gnumeric** (Previously neither named ranges nor formulae were supported)
    - **Ods** (Previously neither named ranges nor formulae were supported)
    - **Xml** (Previously neither named ranges nor formulae were supported)
  - Proper support for named ranges and named formulae in all appropriate Writers
    - **Xlsx** (Previously only simple named ranges were supported)
    - **Xls** (Previously neither named ranges nor formulae were supported) - Still not supported, but some parser issues resolved that previously failed to differentiate between a defined name and a function name
    - **Ods** (Previously neither named ranges nor formulae were supported)
- Support for PHP 8.0

### Changed

- Improve Coverage for ODS Reader [#1545](https://github.com/phpoffice/phpspreadsheet/pull/1545)
- Named formula implementation, and improved handling of Defined Names generally [#1535](https://github.com/PHPOffice/PhpSpreadsheet/pull/1535)
- fix resolution of relative named range values in the calculation engine; previously all named range values had been treated as absolute.
- Drop $this->spreadSheet null check from Xlsx Writer [#1646](https://github.com/phpoffice/phpspreadsheet/pull/1646)
- Improving Coverage for Excel2003 XML Reader [#1557](https://github.com/phpoffice/phpspreadsheet/pull/1557)

### Deprecated

- **IMPORTANT NOTE:** This Introduces a **BC break** in the handling of named ranges. Previously, a named range cell reference of `B2` would be treated identically to a named range cell reference of `$B2` or `B$2` or `$B$2` because the calculation engine treated then all as absolute references. These changes "fix" that, so the calculation engine now handles relative references in named ranges correctly.
  This change that resolves previously incorrect behaviour in the calculation may affect users who have dynamically defined named ranges using relative references when they should have used absolute references.

### Removed

- Nothing.

### Fixed

- PrintArea causes exception [#1544](https://github.com/phpoffice/phpspreadsheet/pull/1544)
- Calculation/DateTime Failure With PHP8 [#1661](https://github.com/phpoffice/phpspreadsheet/pull/1661)
- Reader/Gnumeric Failure with PHP8 [#1662](https://github.com/phpoffice/phpspreadsheet/pull/1662)
- ReverseSort bug, exposed but not caused by PHP8 [#1660](https://github.com/phpoffice/phpspreadsheet/pull/1660)
- Bug setting Superscript/Subscript to false [#1567](https://github.com/phpoffice/phpspreadsheet/pull/1567)

## 1.14.1 - 2020-07-19

### Added

- nothing

### Fixed

- WEBSERVICE is HTTP client agnostic and must be configured via `Settings::setHttpClient()` [#1562](https://github.com/PHPOffice/PhpSpreadsheet/issues/1562)
- Borders were not complete on rowspanned columns using HTML reader [#1473](https://github.com/PHPOffice/PhpSpreadsheet/pull/1473)

### Changed

## 1.14.0 - 2020-06-29

### Added

- Add support for IFS() logical function [#1442](https://github.com/PHPOffice/PhpSpreadsheet/pull/1442)
- Add Cell Address Helper to provide conversions between the R1C1 and A1 address formats [#1558](https://github.com/PHPOffice/PhpSpreadsheet/pull/1558)
- Add ability to edit Html/Pdf before saving [#1499](https://github.com/PHPOffice/PhpSpreadsheet/pull/1499)
- Add ability to set codepage explicitly for BIFF5 [#1018](https://github.com/PHPOffice/PhpSpreadsheet/issues/1018)
- Added support for the WEBSERVICE function [#1409](https://github.com/PHPOffice/PhpSpreadsheet/pull/1409)

### Fixed

- Resolve evaluation of utf-8 named ranges in calculation engine [#1522](https://github.com/PHPOffice/PhpSpreadsheet/pull/1522)
- Fix HLOOKUP on single row [#1512](https://github.com/PHPOffice/PhpSpreadsheet/pull/1512)
- Fix MATCH when comparing different numeric types [#1521](https://github.com/PHPOffice/PhpSpreadsheet/pull/1521)
- Fix exact MATCH on ranges with empty cells [#1520](https://github.com/PHPOffice/PhpSpreadsheet/pull/1520)
- Fix for Issue [#1516](https://github.com/PHPOffice/PhpSpreadsheet/issues/1516) (Cloning worksheet makes corrupted Xlsx) [#1530](https://github.com/PHPOffice/PhpSpreadsheet/pull/1530)
- Fix For Issue [#1509](https://github.com/PHPOffice/PhpSpreadsheet/issues/1509) (Can not set empty enclosure for CSV) [#1518](https://github.com/PHPOffice/PhpSpreadsheet/pull/1518)
- Fix for Issue [#1505](https://github.com/PHPOffice/PhpSpreadsheet/issues/1505) (TypeError : Argument 4 passed to PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet::writeAttributeIf() must be of the type string) [#1525](https://github.com/PHPOffice/PhpSpreadsheet/pull/1525)
- Fix for Issue [#1495](https://github.com/PHPOffice/PhpSpreadsheet/issues/1495) (Sheet index being changed when multiple sheets are used in formula) [#1500]((https://github.com/PHPOffice/PhpSpreadsheet/pull/1500))
- Fix for Issue [#1533](https://github.com/PHPOffice/PhpSpreadsheet/issues/1533) (A reference to a cell containing a string starting with "#" leads to errors in the generated xlsx.) [#1534](https://github.com/PHPOffice/PhpSpreadsheet/pull/1534)
- Xls Writer - Correct Timestamp Bug [#1493](https://github.com/PHPOffice/PhpSpreadsheet/pull/1493)
- Don't ouput row and columns without any cells in HTML writer [#1235](https://github.com/PHPOffice/PhpSpreadsheet/issues/1235)

## 1.13.0 - 2020-05-31

### Added

- Support writing to streams in all writers [#1292](https://github.com/PHPOffice/PhpSpreadsheet/issues/1292)
- Support CSV files with data wrapping a lot of lines [#1468](https://github.com/PHPOffice/PhpSpreadsheet/pull/1468)
- Support protection of worksheet by a specific hash algorithm [#1485](https://github.com/PHPOffice/PhpSpreadsheet/pull/1485)

### Fixed

- Fix Chart samples by updating chart parameter from 0 to DataSeries::EMPTY_AS_GAP [#1448](https://github.com/PHPOffice/PhpSpreadsheet/pull/1448)
- Fix return type in docblock for the Cells::get() [#1398](https://github.com/PHPOffice/PhpSpreadsheet/pull/1398)
- Fix RATE, PRICE, XIRR, and XNPV Functions [#1456](https://github.com/PHPOffice/PhpSpreadsheet/pull/1456)
- Save Excel 2010+ functions properly in XLSX [#1461](https://github.com/PHPOffice/PhpSpreadsheet/pull/1461)
- Several improvements in HTML writer [#1464](https://github.com/PHPOffice/PhpSpreadsheet/pull/1464)
- Fix incorrect behaviour when saving XLSX file with drawings [#1462](https://github.com/PHPOffice/PhpSpreadsheet/pull/1462),
- Fix Crash while trying setting a cell the value "123456\n" [#1476](https://github.com/PHPOffice/PhpSpreadsheet/pull/1481)
- Improved DATEDIF() function and reduced errors for Y and YM units [#1466](https://github.com/PHPOffice/PhpSpreadsheet/pull/1466)
- Stricter typing for mergeCells [#1494](https://github.com/PHPOffice/PhpSpreadsheet/pull/1494)

### Changed

- Drop support for PHP 7.1, according to https://phpspreadsheet.readthedocs.io/en/latest/#php-version-support
- Drop partial migration tool in favor of complete migration via RectorPHP [#1445](https://github.com/PHPOffice/PhpSpreadsheet/issues/1445)
- Limit composer package to `src/` [#1424](https://github.com/PHPOffice/PhpSpreadsheet/pull/1424)

## 1.12.0 - 2020-04-27

### Added

- Improved the ARABIC function to also handle short-hand roman numerals
- Added support for the FLOOR.MATH and FLOOR.PRECISE functions [#1351](https://github.com/PHPOffice/PhpSpreadsheet/pull/1351)

### Fixed

- Fix ROUNDUP and ROUNDDOWN for floating-point rounding error [#1404](https://github.com/PHPOffice/PhpSpreadsheet/pull/1404)
- Fix ROUNDUP and ROUNDDOWN for negative number [#1417](https://github.com/PHPOffice/PhpSpreadsheet/pull/1417)
- Fix loading styles from vmlDrawings when containing whitespace [#1347](https://github.com/PHPOffice/PhpSpreadsheet/issues/1347)
- Fix incorrect behavior when removing last row [#1365](https://github.com/PHPOffice/PhpSpreadsheet/pull/1365)
- MATCH with a static array should return the position of the found value based on the values submitted [#1332](https://github.com/PHPOffice/PhpSpreadsheet/pull/1332)
- Fix Xlsx Reader's handling of undefined fill color [#1353](https://github.com/PHPOffice/PhpSpreadsheet/pull/1353)

## 1.11.0 - 2020-03-02

### Added

- Added support for the BASE function
- Added support for the ARABIC function
- Conditionals - Extend Support for (NOT)CONTAINSBLANKS [#1278](https://github.com/PHPOffice/PhpSpreadsheet/pull/1278)

### Fixed

- Handle Error in Formula Processing Better for Xls [#1267](https://github.com/PHPOffice/PhpSpreadsheet/pull/1267)
- Handle ConditionalStyle NumberFormat When Reading Xlsx File [#1296](https://github.com/PHPOffice/PhpSpreadsheet/pull/1296)
- Fix Xlsx Writer's handling of decimal commas [#1282](https://github.com/PHPOffice/PhpSpreadsheet/pull/1282)
- Fix for issue by removing test code mistakenly left in [#1328](https://github.com/PHPOffice/PhpSpreadsheet/pull/1328)
- Fix for Xls writer wrong selected cells and active sheet [#1256](https://github.com/PHPOffice/PhpSpreadsheet/pull/1256)
- Fix active cell when freeze pane is used [#1323](https://github.com/PHPOffice/PhpSpreadsheet/pull/1323)
- Fix XLSX file loading with autofilter containing '$' [#1326](https://github.com/PHPOffice/PhpSpreadsheet/pull/1326)
- PHPDoc - Use `@return $this` for fluent methods [#1362](https://github.com/PHPOffice/PhpSpreadsheet/pull/1362)

## 1.10.1 - 2019-12-02

### Changed

- PHP 7.4 compatibility

### Fixed

- FLOOR() function accept negative number and negative significance [#1245](https://github.com/PHPOffice/PhpSpreadsheet/pull/1245)
- Correct column style even when using rowspan [#1249](https://github.com/PHPOffice/PhpSpreadsheet/pull/1249)
- Do not confuse defined names and cell refs [#1263](https://github.com/PHPOffice/PhpSpreadsheet/pull/1263)
- XLSX reader/writer keep decimal for floats with a zero decimal part [#1262](https://github.com/PHPOffice/PhpSpreadsheet/pull/1262)
- ODS writer prevent invalid numeric value if locale decimal separator is comma [#1268](https://github.com/PHPOffice/PhpSpreadsheet/pull/1268)
- Xlsx writer actually writes plotVisOnly and dispBlanksAs from chart properties [#1266](https://github.com/PHPOffice/PhpSpreadsheet/pull/1266)

## 1.10.0 - 2019-11-18

### Changed

- Change license from LGPL 2.1 to MIT [#140](https://github.com/PHPOffice/PhpSpreadsheet/issues/140)

### Added

- Implementation of IFNA() logical function
- Support "showZeros" worksheet option to change how Excel shows and handles "null" values returned from a calculation
- Allow HTML Reader to accept HTML as a string into an existing spreadsheet [#1212](https://github.com/PHPOffice/PhpSpreadsheet/pull/1212)

### Fixed

- IF implementation properly handles the value `#N/A` [#1165](https://github.com/PHPOffice/PhpSpreadsheet/pull/1165)
- Formula Parser: Wrong line count for stuff like "MyOtherSheet!A:D" [#1215](https://github.com/PHPOffice/PhpSpreadsheet/issues/1215)
- Call garbage collector after removing a column to prevent stale cached values
- Trying to remove a column that doesn't exist deletes the latest column
- Keep big integer as integer instead of lossely casting to float [#874](https://github.com/PHPOffice/PhpSpreadsheet/pull/874)
- Fix branch pruning handling of non boolean conditions [#1167](https://github.com/PHPOffice/PhpSpreadsheet/pull/1167)
- Fix ODS Reader when no DC namespace are defined [#1182](https://github.com/PHPOffice/PhpSpreadsheet/pull/1182)
- Fixed Functions->ifCondition for allowing <> and empty condition [#1206](https://github.com/PHPOffice/PhpSpreadsheet/pull/1206)
- Validate XIRR inputs and return correct error values [#1120](https://github.com/PHPOffice/PhpSpreadsheet/issues/1120)
- Allow to read xlsx files with exotic workbook names like "workbook2.xml" [#1183](https://github.com/PHPOffice/PhpSpreadsheet/pull/1183)

## 1.9.0 - 2019-08-17

### Changed

- Drop support for PHP 5.6 and 7.0, according to https://phpspreadsheet.readthedocs.io/en/latest/#php-version-support

### Added

- When &lt;br&gt; appears in a table cell, set the cell to wrap [#1071](https://github.com/PHPOffice/PhpSpreadsheet/issues/1071) and [#1070](https://github.com/PHPOffice/PhpSpreadsheet/pull/1070)
- Add MAXIFS, MINIFS, COUNTIFS and Remove MINIF, MAXIF [#1056](https://github.com/PHPOffice/PhpSpreadsheet/issues/1056)
- HLookup needs an ordered list even if range_lookup is set to false [#1055](https://github.com/PHPOffice/PhpSpreadsheet/issues/1055) and [#1076](https://github.com/PHPOffice/PhpSpreadsheet/pull/1076)
- Improve performance of IF function calls via ranch pruning to avoid resolution of every branches [#844](https://github.com/PHPOffice/PhpSpreadsheet/pull/844)
- MATCH function supports `*?~` Excel functionality, when match_type=0 [#1116](https://github.com/PHPOffice/PhpSpreadsheet/issues/1116)
- Allow HTML Reader to accept HTML as a string [#1136](https://github.com/PHPOffice/PhpSpreadsheet/pull/1136)

### Fixed

- Fix to AVERAGEIF() function when called with a third argument
- Eliminate duplicate fill none style entries [#1066](https://github.com/PHPOffice/PhpSpreadsheet/issues/1066)
- Fix number format masks containing literal (non-decimal point) dots [#1079](https://github.com/PHPOffice/PhpSpreadsheet/issues/1079)
- Fix number format masks containing named colours that were being misinterpreted as date formats; and add support for masks that fully replace the value with a full text string [#1009](https://github.com/PHPOffice/PhpSpreadsheet/issues/1009)
- Stricter-typed comparison testing in COUNTIF() and COUNTIFS() evaluation [#1046](https://github.com/PHPOffice/PhpSpreadsheet/issues/1046)
- COUPNUM should not return zero when settlement is in the last period [#1020](https://github.com/PHPOffice/PhpSpreadsheet/issues/1020) and [#1021](https://github.com/PHPOffice/PhpSpreadsheet/pull/1021)
- Fix handling of named ranges referencing sheets with spaces or "!" in their title
- Cover `getSheetByName()` with tests for name with quote and spaces [#739](https://github.com/PHPOffice/PhpSpreadsheet/issues/739)
- Best effort to support invalid colspan values in HTML reader - [#878](https://github.com/PHPOffice/PhpSpreadsheet/pull/878)
- Fixes incorrect rows deletion [#868](https://github.com/PHPOffice/PhpSpreadsheet/issues/868)
- MATCH function fix (value search by type, stop search when match_type=-1 and unordered element encountered) [#1116](https://github.com/PHPOffice/PhpSpreadsheet/issues/1116)
- Fix `getCalculatedValue()` error with more than two INDIRECT [#1115](https://github.com/PHPOffice/PhpSpreadsheet/pull/1115)
- Writer\Html did not hide columns [#985](https://github.com/PHPOffice/PhpSpreadsheet/pull/985)

## 1.8.2 - 2019-07-08

### Fixed

- Uncaught error when opening ods file and properties aren't defined [#1047](https://github.com/PHPOffice/PhpSpreadsheet/issues/1047)
- Xlsx Reader Cell datavalidations bug [#1052](https://github.com/PHPOffice/PhpSpreadsheet/pull/1052)

## 1.8.1 - 2019-07-02

### Fixed

- Allow nullable theme for Xlsx Style Reader class [#1043](https://github.com/PHPOffice/PhpSpreadsheet/issues/1043)

## 1.8.0 - 2019-07-01

### Security Fix (CVE-2019-12331)

- Detect double-encoded xml in the Security scanner, and reject as suspicious.
- This change also broadens the scope of the `libxml_disable_entity_loader` setting when reading XML-based formats, so that it is enabled while the xml is being parsed and not simply while it is loaded.
  On some versions of PHP, this can cause problems because it is not thread-safe, and can affect other PHP scripts running on the same server. This flag is set to true when instantiating a loader, and back to its original setting when the Reader is no longer in scope, or manually unset.
- Provide a check to identify whether libxml_disable_entity_loader is thread-safe or not.

  `XmlScanner::threadSafeLibxmlDisableEntityLoaderAvailability()`
- Provide an option to disable the libxml_disable_entity_loader call through settings. This is not recommended as it reduces the security of the XML-based readers, and should only be used if you understand the consequences and have no other choice.

### Added

- Added support for the SWITCH function [#963](https://github.com/PHPOffice/PhpSpreadsheet/issues/963) and [#983](https://github.com/PHPOffice/PhpSpreadsheet/pull/983)
- Add accounting number format style [#974](https://github.com/PHPOffice/PhpSpreadsheet/pull/974)

### Fixed

- Whitelist `tsv` extension when opening CSV files [#429](https://github.com/PHPOffice/PhpSpreadsheet/issues/429)
- Fix a SUMIF warning with some versions of PHP when having different length of arrays provided as input [#873](https://github.com/PHPOffice/PhpSpreadsheet/pull/873)
- Fix incorrectly handled backslash-escaped space characters in number format

## 1.7.0 - 2019-05-26

- Added support for inline styles in Html reader (borders, alignment, width, height)
- QuotedText cells no longer treated as formulae if the content begins with a `=`
- Clean handling for DDE in formulae

### Fixed

- Fix handling for escaped enclosures and new lines in CSV Separator Inference
- Fix MATCH an error was appearing when comparing strings against 0 (always true)
- Fix wrong calculation of highest column with specified row [#700](https://github.com/PHPOffice/PhpSpreadsheet/issues/700)
- Fix VLOOKUP
- Fix return type hint

## 1.6.0 - 2019-01-02

### Added

- Refactored Matrix Functions to use external Matrix library
- Possibility to specify custom colors of values for pie and donut charts [#768](https://github.com/PHPOffice/PhpSpreadsheet/pull/768)

### Fixed

- Improve XLSX parsing speed if no readFilter is applied [#772](https://github.com/PHPOffice/PhpSpreadsheet/issues/772)
- Fix column names if read filter calls in XLSX reader skip columns [#777](https://github.com/PHPOffice/PhpSpreadsheet/pull/777)
- XLSX reader can now ignore blank cells, using the setReadEmptyCells(false) method. [#810](https://github.com/PHPOffice/PhpSpreadsheet/issues/810)
- Fix LOOKUP function which was breaking on edge cases [#796](https://github.com/PHPOffice/PhpSpreadsheet/issues/796)
- Fix VLOOKUP with exact matches [#809](https://github.com/PHPOffice/PhpSpreadsheet/pull/809)
- Support COUNTIFS multiple arguments [#830](https://github.com/PHPOffice/PhpSpreadsheet/pull/830)
- Change `libxml_disable_entity_loader()` as shortly as possible [#819](https://github.com/PHPOffice/PhpSpreadsheet/pull/819)
- Improved memory usage and performance when loading large spreadsheets [#822](https://github.com/PHPOffice/PhpSpreadsheet/pull/822)
- Improved performance when loading large spreadsheets [#825](https://github.com/PHPOffice/PhpSpreadsheet/pull/825)
- Improved performance when loading large spreadsheets [#824](https://github.com/PHPOffice/PhpSpreadsheet/pull/824)
- Fix color from CSS when reading from HTML [#831](https://github.com/PHPOffice/PhpSpreadsheet/pull/831)
- Fix infinite loop when reading invalid ODS files [#832](https://github.com/PHPOffice/PhpSpreadsheet/pull/832)
- Fix time format for duration is incorrect [#666](https://github.com/PHPOffice/PhpSpreadsheet/pull/666)
- Fix iconv unsupported `//IGNORE//TRANSLIT` on IBM i [#791](https://github.com/PHPOffice/PhpSpreadsheet/issues/791)

### Changed

- `master` is the new default branch, `develop` does not exist anymore

## 1.5.2 - 2018-11-25

### Security

- Improvements to the design of the XML Security Scanner [#771](https://github.com/PHPOffice/PhpSpreadsheet/issues/771)

## 1.5.1 - 2018-11-20

### Security

- Fix and improve XXE security scanning for XML-based and HTML Readers [#771](https://github.com/PHPOffice/PhpSpreadsheet/issues/771)

### Added

- Support page margin in mPDF [#750](https://github.com/PHPOffice/PhpSpreadsheet/issues/750)

### Fixed

- Support numeric condition in SUMIF, SUMIFS, AVERAGEIF, COUNTIF, MAXIF and MINIF [#683](https://github.com/PHPOffice/PhpSpreadsheet/issues/683)
- SUMIFS containing multiple conditions [#704](https://github.com/PHPOffice/PhpSpreadsheet/issues/704)
- Csv reader avoid notice when the file is empty [#743](https://github.com/PHPOffice/PhpSpreadsheet/pull/743)
- Fix print area parser for XLSX reader [#734](https://github.com/PHPOffice/PhpSpreadsheet/pull/734)
- Support overriding `DefaultValueBinder::dataTypeForValue()` without overriding `DefaultValueBinder::bindValue()` [#735](https://github.com/PHPOffice/PhpSpreadsheet/pull/735)
- Mpdf export can exceed pcre.backtrack_limit [#637](https://github.com/PHPOffice/PhpSpreadsheet/issues/637)
- Fix index overflow on data values array [#748](https://github.com/PHPOffice/PhpSpreadsheet/pull/748)

## 1.5.0 - 2018-10-21

### Added

- PHP 7.3 support
- Add the DAYS() function [#594](https://github.com/PHPOffice/PhpSpreadsheet/pull/594)

### Fixed

- Sheet title can contain exclamation mark [#325](https://github.com/PHPOffice/PhpSpreadsheet/issues/325)
- Xls file cause the exception during open by Xls reader [#402](https://github.com/PHPOffice/PhpSpreadsheet/issues/402)
- Skip non numeric value in SUMIF [#618](https://github.com/PHPOffice/PhpSpreadsheet/pull/618)
- OFFSET should allow omitted height and width [#561](https://github.com/PHPOffice/PhpSpreadsheet/issues/561)
- Correctly determine delimiter when CSV contains line breaks inside enclosures [#716](https://github.com/PHPOffice/PhpSpreadsheet/issues/716)

## 1.4.1 - 2018-09-30

### Fixed

- Remove locale from formatting string [#644](https://github.com/PHPOffice/PhpSpreadsheet/pull/644)
- Allow iterators to go out of bounds with prev [#587](https://github.com/PHPOffice/PhpSpreadsheet/issues/587)
- Fix warning when reading xlsx without styles [#631](https://github.com/PHPOffice/PhpSpreadsheet/pull/631)
- Fix broken sample links on windows due to $baseDir having backslash [#653](https://github.com/PHPOffice/PhpSpreadsheet/pull/653)

## 1.4.0 - 2018-08-06

### Added

- Add excel function EXACT(value1, value2) support [#595](https://github.com/PHPOffice/PhpSpreadsheet/pull/595)
- Support workbook view attributes for Xlsx format [#523](https://github.com/PHPOffice/PhpSpreadsheet/issues/523)
- Read and write hyperlink for drawing image [#490](https://github.com/PHPOffice/PhpSpreadsheet/pull/490)
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
- Xlsx reader crashed when reading a file with workbook protection [#553](https://github.com/PHPOffice/PhpSpreadsheet/pull/553)
- Cell formats with escaped spaces were causing incorrect date formatting [#557](https://github.com/PHPOffice/PhpSpreadsheet/issues/557)
- Could not open CSV file containing HTML fragment [#564](https://github.com/PHPOffice/PhpSpreadsheet/issues/564)
- Exclude the vendor folder in migration [#481](https://github.com/PHPOffice/PhpSpreadsheet/issues/481)
- Chained operations on cell ranges involving borders operated on last cell only [#428](https://github.com/PHPOffice/PhpSpreadsheet/issues/428)
- Avoid memory exhaustion when cloning worksheet with a drawing [#437](https://github.com/PHPOffice/PhpSpreadsheet/issues/437)
- Migration tool keep variables containing $PHPExcel untouched [#598](https://github.com/PHPOffice/PhpSpreadsheet/issues/598)
- Rowspans/colspans were incorrect when adding worksheet using loadIntoExisting [#619](https://github.com/PHPOffice/PhpSpreadsheet/issues/619)

## 1.3.1 - 2018-06-12

### Fixed

- Ranges across Z and AA columns incorrectly threw an exception [#545](https://github.com/PHPOffice/PhpSpreadsheet/issues/545)

## 1.3.0 - 2018-06-10

### Added

- Support to read Xlsm templates with form elements, macros, printer settings, protected elements and back compatibility drawing, and save result without losing important elements of document [#435](https://github.com/PHPOffice/PhpSpreadsheet/issues/435)
- Expose sheet title maximum length as `Worksheet::SHEET_TITLE_MAXIMUM_LENGTH` [#482](https://github.com/PHPOffice/PhpSpreadsheet/issues/482)
- Allow escape character to be set in CSV reader [#492](https://github.com/PHPOffice/PhpSpreadsheet/issues/492)

### Fixed

- Subtotal 9 in a group that has other subtotals 9 exclude the totals of the other subtotals in the range [#332](https://github.com/PHPOffice/PhpSpreadsheet/issues/332)
- `Helper\Html` support UTF-8 HTML input [#444](https://github.com/PHPOffice/PhpSpreadsheet/issues/444)
- Xlsx loaded an extra empty comment for each real comment [#375](https://github.com/PHPOffice/PhpSpreadsheet/issues/375)
- Xlsx reader do not read rows and columns filtered out in readFilter at all [#370](https://github.com/PHPOffice/PhpSpreadsheet/issues/370)
- Make newer Excel versions properly recalculate formulas on document open [#456](https://github.com/PHPOffice/PhpSpreadsheet/issues/456)
- `Coordinate::extractAllCellReferencesInRange()` throws an exception for an invalid range [#519](https://github.com/PHPOffice/PhpSpreadsheet/issues/519)
- Fixed parsing of conditionals in COUNTIF functions [#526](https://github.com/PHPOffice/PhpSpreadsheet/issues/526)
- Corruption errors for saved Xlsx docs with frozen panes [#532](https://github.com/PHPOffice/PhpSpreadsheet/issues/532)

## 1.2.1 - 2018-04-10

### Fixed

- Plain text and richtext mixed in same cell can be read [#442](https://github.com/PHPOffice/PhpSpreadsheet/issues/442)

## 1.2.0 - 2018-03-04

### Added

- HTML writer creates a generator meta tag [#312](https://github.com/PHPOffice/PhpSpreadsheet/issues/312)
- Support invalid zoom value in XLSX format [#350](https://github.com/PHPOffice/PhpSpreadsheet/pull/350)
- Support for `_xlfn.` prefixed functions and `ISFORMULA`, `MODE.SNGL`, `STDEV.S`, `STDEV.P` [#390](https://github.com/PHPOffice/PhpSpreadsheet/pull/390)

### Fixed

- Avoid potentially unsupported PSR-16 cache keys [#354](https://github.com/PHPOffice/PhpSpreadsheet/issues/354)
- Check for MIME type to know if CSV reader can read a file [#167](https://github.com/PHPOffice/PhpSpreadsheet/issues/167)
- Use proper € symbol for currency format [#379](https://github.com/PHPOffice/PhpSpreadsheet/pull/379)
- Read printing area correctly when skipping some sheets [#371](https://github.com/PHPOffice/PhpSpreadsheet/issues/371)
- Avoid incorrectly overwriting calculated value type [#394](https://github.com/PHPOffice/PhpSpreadsheet/issues/394)
- Select correct cell when calling freezePane [#389](https://github.com/PHPOffice/PhpSpreadsheet/issues/389)
- `setStrikethrough()` did not set the font [#403](https://github.com/PHPOffice/PhpSpreadsheet/issues/403)

## 1.1.0 - 2018-01-28

### Added

- Support for PHP 7.2
- Support cell comments in HTML writer and reader [#308](https://github.com/PHPOffice/PhpSpreadsheet/issues/308)
- Option to stop at a conditional styling, if it matches (only XLSX format) [#292](https://github.com/PHPOffice/PhpSpreadsheet/pull/292)
- Support for line width for data series when rendering Xlsx [#329](https://github.com/PHPOffice/PhpSpreadsheet/pull/329)

### Fixed

- Better auto-detection of CSV separators [#305](https://github.com/PHPOffice/PhpSpreadsheet/issues/305)
- Support for shape style ending with `;` [#304](https://github.com/PHPOffice/PhpSpreadsheet/issues/304)
- Freeze Panes takes wrong coordinates for XLSX [#322](https://github.com/PHPOffice/PhpSpreadsheet/issues/322)
- `COLUMNS` and `ROWS` functions crashed in some cases [#336](https://github.com/PHPOffice/PhpSpreadsheet/issues/336)
- Support XML file without styles [#331](https://github.com/PHPOffice/PhpSpreadsheet/pull/331)
- Cell coordinates which are already a range cause an exception [#319](https://github.com/PHPOffice/PhpSpreadsheet/issues/319)

## 1.0.0 - 2017-12-25

### Added

- Support to write merged cells in ODS format [#287](https://github.com/PHPOffice/PhpSpreadsheet/issues/287)
- Able to set the `topLeftCell` in freeze panes [#261](https://github.com/PHPOffice/PhpSpreadsheet/pull/261)
- Support `DateTimeImmutable` as cell value
- Support migration of prefixed classes

### Fixed

- Can read very small HTML files [#194](https://github.com/PHPOffice/PhpSpreadsheet/issues/194)
- Written DataValidation was corrupted [#290](https://github.com/PHPOffice/PhpSpreadsheet/issues/290)
- Date format compatible with both LibreOffice and Excel [#298](https://github.com/PHPOffice/PhpSpreadsheet/issues/298)

### BREAKING CHANGE

- Constant `TYPE_DOUGHTNUTCHART` is now `TYPE_DOUGHNUTCHART`.

## 1.0.0-beta2 - 2017-11-26

### Added

- Support for chart fill color - @CrazyBite [#158](https://github.com/PHPOffice/PhpSpreadsheet/pull/158)
- Support for read Hyperlink for xml - @GreatHumorist [#223](https://github.com/PHPOffice/PhpSpreadsheet/pull/223)
- Support for cell value validation according to data validation rules - @SailorMax [#257](https://github.com/PHPOffice/PhpSpreadsheet/pull/257)
- Support for custom implementation, or configuration, of PDF libraries - @SailorMax [#266](https://github.com/PHPOffice/PhpSpreadsheet/pull/266)

### Changed

- Merge data-validations to reduce written worksheet size - @billblume [#131](https://github.com/PHPOffice/PhpSpreadSheet/issues/131)
- Throws exception if a XML file is invalid - @GreatHumorist [#222](https://github.com/PHPOffice/PhpSpreadsheet/pull/222)
- Upgrade to mPDF 7.0+ [#144](https://github.com/PHPOffice/PhpSpreadsheet/issues/144)

### Fixed

- Control characters in cell values are automatically escaped [#212](https://github.com/PHPOffice/PhpSpreadsheet/issues/212)
- Prevent color changing when copy/pasting xls files written by PhpSpreadsheet to another file - @al-lala [#218](https://github.com/PHPOffice/PhpSpreadsheet/issues/218)
- Add cell reference automatic when there is no cell reference('r' attribute) in Xlsx file. - @GreatHumorist [#225](https://github.com/PHPOffice/PhpSpreadsheet/pull/225) Refer to [#201](https://github.com/PHPOffice/PhpSpreadsheet/issues/201)
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

## 1.0.0-beta - 2017-08-17

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
- Fixed PHP8 deprecation warning for libxml_disable_entity_loader() [#1625](https://github.com/phpoffice/phpspreadsheet/pull/1625) 

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
