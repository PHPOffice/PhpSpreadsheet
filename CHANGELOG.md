# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com)
and this project adheres to [Semantic Versioning](https://semver.org).

## 2024-07-29 - 2.2.1

### Security Fix

- Prevent XXE when loading files [PR #4119](https://github.com/PHPOffice/PhpSpreadsheet/pull/4119)

### Fixed

- Add Sheet may leave Active Sheet uninitialized. [Issue #4112](https://github.com/PHPOffice/PhpSpreadsheet/issues/4112) [PR #4114](https://github.com/PHPOffice/PhpSpreadsheet/pull/4114)
- Reference to Defined Name Specifying Worksheet. [Issue #206](https://github.com/PHPOffice/PhpSpreadsheet/issues/296) [PR #4096](https://github.com/PHPOffice/PhpSpreadsheet/pull/4096)
- Xls Reader Print/Show Gridlines. [Issue #912](https://github.com/PHPOffice/PhpSpreadsheet/issues/912) [PR #4098](https://github.com/PHPOffice/PhpSpreadsheet/pull/4098)
- ODS Reader Allow Omission of Page Settings Tags. [Issue #4099](https://github.com/PHPOffice/PhpSpreadsheet/issues/4099) [PR #4101](https://github.com/PHPOffice/PhpSpreadsheet/pull/4101)

## 2024-07-24 - 2.2.0

### Added

- Xlsx Reader Optionally Ignore Rows With No Cells. [Issue #3982](https://github.com/PHPOffice/PhpSpreadsheet/issues/3982) [PR #4035](https://github.com/PHPOffice/PhpSpreadsheet/pull/4035)
- Means to change style without affecting current cell/sheet. [PR #4073](https://github.com/PHPOffice/PhpSpreadsheet/pull/4073)
- Option for CSV output file to have varying numbers of columns for each row. [Issue #1415](https://github.com/PHPOffice/PhpSpreadsheet/issues/1415) [PR #4076](https://github.com/PHPOffice/PhpSpreadsheet/pull/4076)

### Changed

- On read, Xlsx Reader had been breaking up union ranges into separate individual ranges. It will now try to preserve range as it was read in. [PR #4042](https://github.com/PHPOffice/PhpSpreadsheet/pull/4042)
- Xlsx/Xls spreadsheet calculation and formatting of dates will use base date of spreadsheet even when spreadsheets with different base dates are simultaneously open. [Issue #1036](https://github.com/PHPOffice/PhpSpreadsheet/issues/1036) [Issue #1635](https://github.com/PHPOffice/PhpSpreadsheet/issues/1635) [PR #4071](https://github.com/PHPOffice/PhpSpreadsheet/pull/4071) 

### Deprecated

- Writer\Xls\Style\ColorMap is no longer needed.

### Moved

- Nothing

### Fixed

- Incorrect Reader CSV with BOM. [Issue #4028](https://github.com/PHPOffice/PhpSpreadsheet/issues/4028) [PR #4029](https://github.com/PHPOffice/PhpSpreadsheet/pull/4029)
- POWER Null/Bool Args. [PR #4031](https://github.com/PHPOffice/PhpSpreadsheet/pull/4031)
- Do Not Output Alignment and Protection for Conditional Format. [Issue #4025](https://github.com/PHPOffice/PhpSpreadsheet/issues/4025) [PR #4027](https://github.com/PHPOffice/PhpSpreadsheet/pull/4027)
- Conditional Color Scale Improvements. [Issue #4049](https://github.com/PHPOffice/PhpSpreadsheet/issues/4049) [PR #4050](https://github.com/PHPOffice/PhpSpreadsheet/pull/4050)
- Mpdf and Tcpdf Borders on Merged Cells. [Issue #3557](https://github.com/PHPOffice/PhpSpreadsheet/issues/3557) [PR #4047](https://github.com/PHPOffice/PhpSpreadsheet/pull/4047)
- Xls Conditional Format Improvements. [PR #4030](https://github.com/PHPOffice/PhpSpreadsheet/pull/4030) [PR #4033](https://github.com/PHPOffice/PhpSpreadsheet/pull/4033)
- Conditional Range Unions and Intersections [Issue #4039](https://github.com/PHPOffice/PhpSpreadsheet/issues/4039) [PR #4042](https://github.com/PHPOffice/PhpSpreadsheet/pull/4042)
- Ods comments with newlines. [Issue #4081](https://github.com/PHPOffice/PhpSpreadsheet/issues/4081) [PR #4086](https://github.com/PHPOffice/PhpSpreadsheet/pull/4086)
- Propagate errors in Text functions. [Issue #2581](https://github.com/PHPOffice/PhpSpreadsheet/issues/2581) [PR #4080](https://github.com/PHPOffice/PhpSpreadsheet/pull/4080)
- Csv Reader allow use of html mimetype. [Issue #4036](https://github.com/PHPOffice/PhpSpreadsheet/issues/4036) [PR #4040](https://github.com/PHPOffice/PhpSpreadsheet/pull/4040)
- Problem rendering line chart with missing plot label. [PR #4074](https://github.com/PHPOffice/PhpSpreadsheet/pull/4074)
- More RTL in Xlsx/Html Comments [Issue #4004](https://github.com/PHPOffice/PhpSpreadsheet/issues/4004) [PR #4065](https://github.com/PHPOffice/PhpSpreadsheet/pull/4065)
- Empty String in sharedStrings. [Issue #4063](https://github.com/PHPOffice/PhpSpreadsheet/issues/4063) [PR #4064](https://github.com/PHPOffice/PhpSpreadsheet/pull/4064)
- Xlsx Writer RichText and TYPE_STRING. [Issue #476](https://github.com/PHPOffice/PhpSpreadsheet/issues/476) [PR #4094](https://github.com/PHPOffice/PhpSpreadsheet/pull/4094)
- Ods boolean data. [Issue #460](https://github.com/PHPOffice/PhpSpreadsheet/issues/460) [PR #4093](https://github.com/PHPOffice/PhpSpreadsheet/pull/4093)
- Html Writer Minor Fixes. [PR #4089](https://github.com/PHPOffice/PhpSpreadsheet/pull/4089)
- Changes to INDEX function. [Issue #64](https://github.com/PHPOffice/PhpSpreadsheet/issues/64) [PR #4088](https://github.com/PHPOffice/PhpSpreadsheet/pull/4088)
- Ods Reader and Whitespace Text Nodes. [Issue #804](https://github.com/PHPOffice/PhpSpreadsheet/issues/804) [PR #4087](https://github.com/PHPOffice/PhpSpreadsheet/pull/4087)
- Ods Xml Reader and Whitespace Text Nodes. [Issue #804](https://github.com/PHPOffice/PhpSpreadsheet/issues/804) [PR #4087](https://github.com/PHPOffice/PhpSpreadsheet/pull/4087)
- Treat invalid formulas as strings. [Issue #1310](https://github.com/PHPOffice/PhpSpreadsheet/issues/1310) [PR #4073](https://github.com/PHPOffice/PhpSpreadsheet/pull/4073)

## 2024-05-11 - 2.1.0

### MINOR BREAKING CHANGE

- Writing of cell comments to Html will now sanitize all Html tags within the comment, so the tags will be rendered as plaintext and have no other effects when rendered. Styling can be achieved by using the Font property of of the TextRuns which make up the comment, as is already the cases for Xlsx. [PR #3957](https://github.com/PHPOffice/PhpSpreadsheet/pull/3957)

### Added

- Default Style Alignment Property (workaround for bug in non-Excel spreadsheet apps) [Issue #3918](https://github.com/PHPOffice/PhpSpreadsheet/issues/3918) [PR #3924](https://github.com/PHPOffice/PhpSpreadsheet/pull/3924)
- Additional Support for Date/Time Styles [PR #3939](https://github.com/PHPOffice/PhpSpreadsheet/pull/3939)

### Changed

- Nothing

### Deprecated

- Reader/Xml trySimpleXMLLoadString should not have had public visibility, and will be removed.

### Removed

- Nothing

### Fixed

- IF Empty Arguments. [Issue #3875](https://github.com/PHPOffice/PhpSpreadsheet/issues/3875) [Issue #2146](https://github.com/PHPOffice/PhpSpreadsheet/issues/2146) [PR #3879](https://github.com/PHPOffice/PhpSpreadsheet/pull/3879)
- Changes to floating point in Php8.4. [Issue #3896](https://github.com/PHPOffice/PhpSpreadsheet/issues/3896) [PR #3897](https://github.com/PHPOffice/PhpSpreadsheet/pull/3897)
- Handling User-supplied Decimal and Thousands Separators. [Issue #3900](https://github.com/PHPOffice/PhpSpreadsheet/issues/3900) [PR #3903](https://github.com/PHPOffice/PhpSpreadsheet/pull/3903)
- Improve Performance of CSV Writer. [Issue #3904](https://github.com/PHPOffice/PhpSpreadsheet/issues/3904) [PR #3906](https://github.com/PHPOffice/PhpSpreadsheet/pull/3906)
- Fix issue with prepending zero in percentage [Issue #3920](https://github.com/PHPOffice/PhpSpreadsheet/issues/3920) [PR #3921](https://github.com/PHPOffice/PhpSpreadsheet/pull/3921)
- Incorrect SUMPRODUCT Calculation [Issue #3909](https://github.com/PHPOffice/PhpSpreadsheet/issues/3909) [PR #3916](https://github.com/PHPOffice/PhpSpreadsheet/pull/3916)
- Formula Misidentifying Text as Cell After Insertion/Deletion [Issue #3907](https://github.com/PHPOffice/PhpSpreadsheet/issues/3907) [PR #3915](https://github.com/PHPOffice/PhpSpreadsheet/pull/3915)
- Unexpected Absolute Address in Xlsx Rels [Issue #3730](https://github.com/PHPOffice/PhpSpreadsheet/issues/3730) [PR #3923](https://github.com/PHPOffice/PhpSpreadsheet/pull/3923)
- Unallocated Cells Affected by Column/Row Insert/Delete [Issue #3933](https://github.com/PHPOffice/PhpSpreadsheet/issues/3933) [PR #3940](https://github.com/PHPOffice/PhpSpreadsheet/pull/3940)
- Invalid Builtin Defined Name in Xls Reader [Issue #3935](https://github.com/PHPOffice/PhpSpreadsheet/issues/3935) [PR #3942](https://github.com/PHPOffice/PhpSpreadsheet/pull/3942)
- Hidden Rows and Columns Tcpdf/Mpdf [PR #3945](https://github.com/PHPOffice/PhpSpreadsheet/pull/3945)
- RTL Text Alignment in Xlsx Comments [Issue #4004](https://github.com/PHPOffice/PhpSpreadsheet/issues/4004) [PR #4006](https://github.com/PHPOffice/PhpSpreadsheet/pull/4006)
- Protect Sheet But Allow Sort [Issue #3951](https://github.com/PHPOffice/PhpSpreadsheet/issues/3951) [PR #3956](https://github.com/PHPOffice/PhpSpreadsheet/pull/3956)
- Default Value for Conditional::$text [PR #3946](https://github.com/PHPOffice/PhpSpreadsheet/pull/3946)
- Table Filter Buttons [Issue #3988](https://github.com/PHPOffice/PhpSpreadsheet/issues/3988) [PR #3992](https://github.com/PHPOffice/PhpSpreadsheet/pull/3992)
- Improvements to Xml Reader [Issue #3999](https://github.com/PHPOffice/PhpSpreadsheet/issues/3999) [Issue #4000](https://github.com/PHPOffice/PhpSpreadsheet/issues/4000) [Issue #4001](https://github.com/PHPOffice/PhpSpreadsheet/issues/4001) [Issue #4002](https://github.com/PHPOffice/PhpSpreadsheet/issues/4002) [PR #4003](https://github.com/PHPOffice/PhpSpreadsheet/pull/4003) [PR #4007](https://github.com/PHPOffice/PhpSpreadsheet/pull/4007)
- Html Reader non-UTF8 [Issue #3995](https://github.com/PHPOffice/PhpSpreadsheet/issues/3995) [Issue #866](https://github.com/PHPOffice/PhpSpreadsheet/issues/866) [Issue #1681](https://github.com/PHPOffice/PhpSpreadsheet/issues/1681) [PR #4019](https://github.com/PHPOffice/PhpSpreadsheet/pull/4019)

## 2.0.0 - 2024-01-04

### BREAKING CHANGE

- Typing was strengthened by leveraging native typing. This should not change any behavior. However, if you implement
  any interfaces or inherit from any classes, you will need to adapt your typing accordingly. If you use static analysis 
  tools such as PHPStan or Psalm, new errors might be found. If you find actual bugs because of the new typing, please
  open a PR that fixes it with a **detailed** explanation of the reason. We'll try to merge and release typing-related
  fixes quickly in the coming days. [PR #3718](https://github.com/PHPOffice/PhpSpreadsheet/pull/3718)
- All deprecated things have been removed, for details, see [816b91d0b4](https://github.com/PHPOffice/PhpSpreadsheet/commit/816b91d0b4a0c7285a9e3fc88c58f7730d922044)

### Added

- Split screens (Xlsx and Xml only, not 100% complete). [Issue #3601](https://github.com/PHPOffice/PhpSpreadsheet/issues/3601) [PR #3622](https://github.com/PHPOffice/PhpSpreadsheet/pull/3622)
- Permit Meta Viewport in Html. [Issue #3565](https://github.com/PHPOffice/PhpSpreadsheet/issues/3565) [PR #3623](https://github.com/PHPOffice/PhpSpreadsheet/pull/3623)
- Hyperlink support for Ods. [Issue #3660](https://github.com/PHPOffice/PhpSpreadsheet/issues/3660) [PR #3669](https://github.com/PHPOffice/PhpSpreadsheet/pull/3669)
- ListWorksheetInfo/Names for Html/Csv/Slk. [Issue #3706](https://github.com/PHPOffice/PhpSpreadsheet/issues/3706) [PR #3709](https://github.com/PHPOffice/PhpSpreadsheet/pull/3709)
- Methods to determine if cell is actually locked, or hidden on formula bar. [PR #3722](https://github.com/PHPOffice/PhpSpreadsheet/pull/3722)
- Add iterateOnlyExistingCells to Constructors. [Issue #3721](https://github.com/PHPOffice/PhpSpreadsheet/issues/3721) [PR #3727](https://github.com/PHPOffice/PhpSpreadsheet/pull/3727)
- Support for Conditional Formatting Color Scale. [PR #3738](https://github.com/PHPOffice/PhpSpreadsheet/pull/3738)
- Support Additional Tags in Helper/Html. [Issue #3751](https://github.com/PHPOffice/PhpSpreadsheet/issues/3751) [PR #3752](https://github.com/PHPOffice/PhpSpreadsheet/pull/3752)
- Writer ODS : Write Border Style for cells [Issue #3690](https://github.com/PHPOffice/PhpSpreadsheet/issues/3690) [PR #3693](https://github.com/PHPOffice/PhpSpreadsheet/pull/3693)
- Sheet Background Images [Issue #1649](https://github.com/PHPOffice/PhpSpreadsheet/issues/1649) [PR #3795](https://github.com/PHPOffice/PhpSpreadsheet/pull/3795)
- Check if Coordinate is Inside Range [PR #3779](https://github.com/PHPOffice/PhpSpreadsheet/pull/3779)
- Flipping Images [Issue #731](https://github.com/PHPOffice/PhpSpreadsheet/issues/731) [PR #3801](https://github.com/PHPOffice/PhpSpreadsheet/pull/3801)
- Chart Dynamic Title and Font Properties [Issue #3797](https://github.com/PHPOffice/PhpSpreadsheet/issues/3797) [PR #3800](https://github.com/PHPOffice/PhpSpreadsheet/pull/3800)
- Chart Axis Display Units and Logarithmic Scale. [Issue #3833](https://github.com/PHPOffice/PhpSpreadsheet/issues/3833) [PR #3836](https://github.com/PHPOffice/PhpSpreadsheet/pull/3836)
- Partial Support of Fill Handles. [Discussion #3847](https://github.com/PHPOffice/PhpSpreadsheet/discussions/3847) [PR #3855](https://github.com/PHPOffice/PhpSpreadsheet/pull/3855)

### Changed

- **Drop support for PHP 7.4**, according to https://phpspreadsheet.readthedocs.io/en/latest/#php-version-support [PR #3713](https://github.com/PHPOffice/PhpSpreadsheet/pull/3713)
- RLM Added to NumberFormatter Currency. This happens depending on release of ICU which Php is using (it does not yet happen with any official release). PhpSpreadsheet will continue to use the value returned by Php, but a method is added to keep the result unchanged from release to release. [Issue #3571](https://github.com/PHPOffice/PhpSpreadsheet/issues/3571) [PR #3640](https://github.com/PHPOffice/PhpSpreadsheet/pull/3640)
- `toFormattedString` will now always return a string. This was introduced with 1.28.0, but was not properly documented at the time. This can affect the results of `toArray`, `namedRangeToArray`, and `rangeToArray`. [PR #3304](https://github.com/PHPOffice/PhpSpreadsheet/pull/3304)
- Value of constants FORMAT_CURRENCY_EUR and FORMAT_CURRENCY_USD was changed in 1.28.0, but was not properly documented at the time. [Issue #3577](https://github.com/PHPOffice/PhpSpreadsheet/issues/3577)
- Html Writer will attempt to use Chart coordinates to determine image size. [Issue #3783](https://github.com/PHPOffice/PhpSpreadsheet/issues/3783) [PR #3787](https://github.com/PHPOffice/PhpSpreadsheet/pull/3787)

### Deprecated

- Functions `_translateFormulaToLocale` and `_translateFormulaEnglish` are replaced by versions without leading underscore. [PR #3828](https://github.com/PHPOffice/PhpSpreadsheet/pull/3828)

### Removed

- Nothing

### Fixed

- Take advantage of mitoteam/jpgraph Extended mode to enable rendering of more graphs. [PR #3603](https://github.com/PHPOffice/PhpSpreadsheet/pull/3603)
- Column widths, especially for ODS. [Issue #3609](https://github.com/PHPOffice/PhpSpreadsheet/issues/3609) [PR #3610](https://github.com/PHPOffice/PhpSpreadsheet/pull/3610)
- Avoid NULL in String Function call (partial solution). [Issue #3613](https://github.com/PHPOffice/PhpSpreadsheet/issues/3613) [PR #3617](https://github.com/PHPOffice/PhpSpreadsheet/pull/3617)
- Preserve transparency in Memory Drawing. [Issue #3624](https://github.com/PHPOffice/PhpSpreadsheet/issues/3624) [PR #3627](https://github.com/PHPOffice/PhpSpreadsheet/pull/3627)
- Customizable padding for Exact Column Width. [Issue #3626](https://github.com/PHPOffice/PhpSpreadsheet/issues/3626) [PR #3628](https://github.com/PHPOffice/PhpSpreadsheet/pull/3628)
- Ensure ROW function returns int (problem exposed in unreleased Php). [PR #3641](https://github.com/PHPOffice/PhpSpreadsheet/pull/3641)
- Minor changes to Mpdf and Html Writers. [PR #3645](https://github.com/PHPOffice/PhpSpreadsheet/pull/3645)
- Xlsx Reader Namespacing for Tables, Autofilters. [Issue #3665](https://github.com/PHPOffice/PhpSpreadsheet/issues/3665) [PR #3668](https://github.com/PHPOffice/PhpSpreadsheet/pull/3668)
- Read Code Page for Xls ListWorksheetInfo/Names BIFF5. [Issue #3671](https://github.com/PHPOffice/PhpSpreadsheet/issues/3671) [PR #3672](https://github.com/PHPOffice/PhpSpreadsheet/pull/3672)
- Read Data from Table on Different Sheet. [Issue #3635](https://github.com/PHPOffice/PhpSpreadsheet/issues/3635) [PR #3659](https://github.com/PHPOffice/PhpSpreadsheet/pull/3659)
- Html Writer Styles Using Inline Css. [Issue #3678](https://github.com/PHPOffice/PhpSpreadsheet/issues/3678) [PR #3680](https://github.com/PHPOffice/PhpSpreadsheet/pull/3680)
- Xlsx Read Ignoring Some Comments. [Issue #3654](https://github.com/PHPOffice/PhpSpreadsheet/issues/3654) [PR #3655](https://github.com/PHPOffice/PhpSpreadsheet/pull/3655)
- Fractional Seconds in Date/Time Values. [PR #3677](https://github.com/PHPOffice/PhpSpreadsheet/pull/3677)
- SetCalculatedValue Avoid Casting String to Numeric. [Issue #3658](https://github.com/PHPOffice/PhpSpreadsheet/issues/3658) [PR #3685](https://github.com/PHPOffice/PhpSpreadsheet/pull/3685)
- Several Problems in a Very Complicated Spreadsheet. [Issue #3679](https://github.com/PHPOffice/PhpSpreadsheet/issues/3679) [PR #3681](https://github.com/PHPOffice/PhpSpreadsheet/pull/3681)
- Inconsistent String Handling for Sum Functions. [Issue #3652](https://github.com/PHPOffice/PhpSpreadsheet/issues/3652) [PR #3653](https://github.com/PHPOffice/PhpSpreadsheet/pull/3653)
- Recomputation of Relative Addresses in Defined Names. [Issue #3661](https://github.com/PHPOffice/PhpSpreadsheet/issues/3661) [PR #3673](https://github.com/PHPOffice/PhpSpreadsheet/pull/3673)
- Writer Xls Characters Outside BMP (emojis). [Issue #642](https://github.com/PHPOffice/PhpSpreadsheet/issues/642) [PR #3696](https://github.com/PHPOffice/PhpSpreadsheet/pull/3696)
- Xlsx Reader Improve Handling of Row and Column Styles. [Issue #3533](https://github.com/PHPOffice/PhpSpreadsheet/issues/3533) [Issue #3534](https://github.com/PHPOffice/PhpSpreadsheet/issues/3534) [PR #3688](https://github.com/PHPOffice/PhpSpreadsheet/pull/3688)
- Avoid Allocating RowDimension Unneccesarily. [PR #3686](https://github.com/PHPOffice/PhpSpreadsheet/pull/3686)
- Use Column Style when Row Dimension Exists Without Style. [Issue #3534](https://github.com/PHPOffice/PhpSpreadsheet/issues/3534) [PR #3688](https://github.com/PHPOffice/PhpSpreadsheet/pull/3688)
- Inconsistency Between Cell Data and Explicitly Declared Type. [Issue #3711](https://github.com/PHPOffice/PhpSpreadsheet/issues/3711) [PR #3715](https://github.com/PHPOffice/PhpSpreadsheet/pull/3715)
- Unexpected Namespacing in rels File. [Issue #3720](https://github.com/PHPOffice/PhpSpreadsheet/issues/3720) [PR #3722](https://github.com/PHPOffice/PhpSpreadsheet/pull/3722)
- Break Some Circular References. [PR #3716](https://github.com/PHPOffice/PhpSpreadsheet/pull/3716) [PR #3707](https://github.com/PHPOffice/PhpSpreadsheet/pull/3707)
- Missing Font Index in Some Xls. [PR #3734](https://github.com/PHPOffice/PhpSpreadsheet/pull/3734)
- Load Tables even with READ_DATA_ONLY. [PR #3726](https://github.com/PHPOffice/PhpSpreadsheet/pull/3726)
- Theme File Missing but Referenced in Spreadsheet. [Issue #3770](https://github.com/PHPOffice/PhpSpreadsheet/issues/3770) [PR #3772](https://github.com/PHPOffice/PhpSpreadsheet/pull/3772)
- Slk Shared Formulas. [Issue #2267](https://github.com/PHPOffice/PhpSpreadsheet/issues/2267) [PR #3776](https://github.com/PHPOffice/PhpSpreadsheet/pull/3776)
- Html omitting some charts. [Issue #3767](https://github.com/PHPOffice/PhpSpreadsheet/issues/3767) [PR #3771](https://github.com/PHPOffice/PhpSpreadsheet/pull/3771)
- Case Insensitive Comparison for Sheet Names [PR #3791](https://github.com/PHPOffice/PhpSpreadsheet/pull/3791)
- Performance improvement for Xlsx Reader. [Issue #3683](https://github.com/PHPOffice/PhpSpreadsheet/issues/3683) [PR #3810](https://github.com/PHPOffice/PhpSpreadsheet/pull/3810)
- Prevent loop in Shared/File. [Issue #3807](https://github.com/PHPOffice/PhpSpreadsheet/issues/3807) [PR #3809](https://github.com/PHPOffice/PhpSpreadsheet/pull/3809)
- Consistent handling of decimal/thousands separators between StringHelper and Php setlocale. [Issue #3811](https://github.com/PHPOffice/PhpSpreadsheet/issues/3811) [PR #3815](https://github.com/PHPOffice/PhpSpreadsheet/pull/3815)
- Clone worksheet with tables or charts. [Issue #3820](https://github.com/PHPOffice/PhpSpreadsheet/issues/3820) [PR #3821](https://github.com/PHPOffice/PhpSpreadsheet/pull/3821)
- COUNTIFS Does Not Require xlfn. [Issue #3819](https://github.com/PHPOffice/PhpSpreadsheet/issues/3819) [PR #3827](https://github.com/PHPOffice/PhpSpreadsheet/pull/3827)
- Strip `xlfn.` and `xlws.` from Formula Translations. [Issue #3819](https://github.com/PHPOffice/PhpSpreadsheet/issues/3819) [PR #3828](https://github.com/PHPOffice/PhpSpreadsheet/pull/3828)
- Recurse directories searching for font file. [Issue #2809](https://github.com/PHPOffice/PhpSpreadsheet/issues/2809) [PR #3830](https://github.com/PHPOffice/PhpSpreadsheet/pull/3830)
- Reduce memory consumption of Worksheet::rangeToArray() when many empty rows are read. [Issue #3814](https://github.com/PHPOffice/PhpSpreadsheet/issues/3814) [PR #3834](https://github.com/PHPOffice/PhpSpreadsheet/pull/3834)
- Reduce time used by Worksheet::rangeToArray() when many empty rows are read. [PR #3839](https://github.com/PHPOffice/PhpSpreadsheet/pull/3839)
- Html Reader Tolerate Invalid Sheet Title. [PR #3845](https://github.com/PHPOffice/PhpSpreadsheet/pull/3845)
- Do not include unparsed drawings when new drawing added. [Issue #3843](https://github.com/PHPOffice/PhpSpreadsheet/issues/3843) [PR #3846](https://github.com/PHPOffice/PhpSpreadsheet/pull/3846)
- Do not include unparsed drawings when new drawing added. [Issue #3861](https://github.com/PHPOffice/PhpSpreadsheet/issues/3861) [PR #3862](https://github.com/PHPOffice/PhpSpreadsheet/pull/3862)
- Excel omits `between` operator for data validation. [Issue #3863](https://github.com/PHPOffice/PhpSpreadsheet/issues/3863) [PR #3865](https://github.com/PHPOffice/PhpSpreadsheet/pull/3865)
- Use less space when inserting rows and columns. [Issue #3687](https://github.com/PHPOffice/PhpSpreadsheet/issues/3687) [PR #3856](https://github.com/PHPOffice/PhpSpreadsheet/pull/3856)
- Excel inconsistent handling of MIN/MAX/MINA/MAXA. [Issue #3866](https://github.com/PHPOffice/PhpSpreadsheet/issues/3866) [PR #3868](https://github.com/PHPOffice/PhpSpreadsheet/pull/3868)

## 1.29.0 - 2023-06-15

### Added

- Wizards for defining Number Format masks for Dates and Times, including Durations/Intervals. [PR #3458](https://github.com/PHPOffice/PhpSpreadsheet/pull/3458)
- Specify data type in html tags. [Issue #3444](https://github.com/PHPOffice/PhpSpreadsheet/issues/3444) [PR #3445](https://github.com/PHPOffice/PhpSpreadsheet/pull/3445)
- Provide option to ignore hidden rows/columns in `toArray()` methods. [PR #3494](https://github.com/PHPOffice/PhpSpreadsheet/pull/3494)
- Font/Effects/Theme support for Chart Data Labels and Axis. [PR #3476](https://github.com/PHPOffice/PhpSpreadsheet/pull/3476)
- Font Themes support. [PR #3486](https://github.com/PHPOffice/PhpSpreadsheet/pull/3486)
- Ability to Ignore Cell Errors in Excel. [Issue #1141](https://github.com/PHPOffice/PhpSpreadsheet/issues/1141) [PR #3508](https://github.com/PHPOffice/PhpSpreadsheet/pull/3508)
- Unzipped Gnumeric file [PR #3591](https://github.com/PHPOffice/PhpSpreadsheet/pull/3591)

### Changed

- Xlsx Color schemes read in will be written out (previously Excel 2007-2010 Color scheme was always written); manipulation of those schemes before write, including restoring prior behavior, is provided [PR #3476](https://github.com/PHPOffice/PhpSpreadsheet/pull/3476)
- Memory and speed optimisations for Read Filters with Xlsx Files and Shared Formulae. [PR #3474](https://github.com/PHPOffice/PhpSpreadsheet/pull/3474)
- Allow `CellRange` and `CellAddress` objects for the `range` argument in the `rangeToArray()` method. [PR #3494](https://github.com/PHPOffice/PhpSpreadsheet/pull/3494)
- Stock charts will now read and reproduce `upDownBars` and subsidiary tags; these were previously ignored on read and hard-coded on write. [PR #3515](https://github.com/PHPOffice/PhpSpreadsheet/pull/3515)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Updates Cell formula absolute ranges/references, and Defined Name absolute ranges/references when inserting/deleting rows/columns. [Issue #3368](https://github.com/PHPOffice/PhpSpreadsheet/issues/3368) [PR #3402](https://github.com/PHPOffice/PhpSpreadsheet/pull/3402)
- EOMONTH() and EDATE() Functions should round date value before evaluation. [Issue #3436](https://github.com/PHPOffice/PhpSpreadsheet/issues/3436) [PR #3437](https://github.com/PHPOffice/PhpSpreadsheet/pull/3437)
- NETWORKDAYS function erroneously being converted to NETWORK_xlfn.DAYS in Xlsx Writer. [Issue #3461](https://github.com/PHPOffice/PhpSpreadsheet/issues/3461) [PR #3463](https://github.com/PHPOffice/PhpSpreadsheet/pull/3463)
- Getting a style for a CellAddress instance fails if the worksheet is set in the CellAddress instance. [Issue #3439](https://github.com/PHPOffice/PhpSpreadsheet/issues/3439) [PR #3469](https://github.com/PHPOffice/PhpSpreadsheet/pull/3469)
- Shared Formulae outside the filter range when reading with a filter are not always being identified. [Issue #3473](https://github.com/PHPOffice/PhpSpreadsheet/issues/3473) [PR #3474](https://github.com/PHPOffice/PhpSpreadsheet/pull/3474)
- Xls Reader Conditional Styles. [PR #3400](https://github.com/PHPOffice/PhpSpreadsheet/pull/3400)
- Allow use of # and 0 digit placeholders in fraction masks. [PR #3401](https://github.com/PHPOffice/PhpSpreadsheet/pull/3401)
- Modify Date/Time check in the NumberFormatter for decimal/fractional times. [PR #3413](https://github.com/PHPOffice/PhpSpreadsheet/pull/3413)
- Misplaced Xml Writing Chart Label FillColor. [Issue #3397](https://github.com/PHPOffice/PhpSpreadsheet/issues/3397) [PR #3404](https://github.com/PHPOffice/PhpSpreadsheet/pull/3404)
- TEXT function ignores Time in DateTimeStamp. [Issue #3409](https://github.com/PHPOffice/PhpSpreadsheet/issues/3409) [PR #3411](https://github.com/PHPOffice/PhpSpreadsheet/pull/3411)
- Xlsx Column Autosize Approximate for CJK. [Issue #3405](https://github.com/PHPOffice/PhpSpreadsheet/issues/3405) [PR #3416](https://github.com/PHPOffice/PhpSpreadsheet/pull/3416)
- Correct Xlsx Parsing of quotePrefix="0". [Issue #3435](https://github.com/PHPOffice/PhpSpreadsheet/issues/3435) [PR #3438](https://github.com/PHPOffice/PhpSpreadsheet/pull/3438)
- More Display Options for Chart Axis and Legend. [Issue #3414](https://github.com/PHPOffice/PhpSpreadsheet/issues/3414) [PR #3434](https://github.com/PHPOffice/PhpSpreadsheet/pull/3434)
- Apply strict type checking to Complex suffix. [PR #3452](https://github.com/PHPOffice/PhpSpreadsheet/pull/3452)
- Incorrect Font Color Read Xlsx Rich Text Indexed Color Custom Palette. [Issue #3464](https://github.com/PHPOffice/PhpSpreadsheet/issues/3464) [PR #3465](https://github.com/PHPOffice/PhpSpreadsheet/pull/3465)
- Xlsx Writer Honor Alignment in Default Font. [Issue #3443](https://github.com/PHPOffice/PhpSpreadsheet/issues/3443) [PR #3459](https://github.com/PHPOffice/PhpSpreadsheet/pull/3459)
- Support Border for Charts. [PR #3462](https://github.com/PHPOffice/PhpSpreadsheet/pull/3462)
- Error in "this row" structured reference calculation (cached result from first row when using a range) [Issue #3504](https://github.com/PHPOffice/PhpSpreadsheet/issues/3504) [PR #3505](https://github.com/PHPOffice/PhpSpreadsheet/pull/3505)
- Allow colour palette index references in Number Format masks [Issue #3511](https://github.com/PHPOffice/PhpSpreadsheet/issues/3511) [PR #3512](https://github.com/PHPOffice/PhpSpreadsheet/pull/3512)
- Xlsx Reader formula with quotePrefix [Issue #3495](https://github.com/PHPOffice/PhpSpreadsheet/issues/3495) [PR #3497](https://github.com/PHPOffice/PhpSpreadsheet/pull/3497)
- Handle REF error as part of range [Issue #3453](https://github.com/PHPOffice/PhpSpreadsheet/issues/3453) [PR #3467](https://github.com/PHPOffice/PhpSpreadsheet/pull/3467)
- Handle Absolute Pathnames in Rels File [Issue #3553](https://github.com/PHPOffice/PhpSpreadsheet/issues/3553) [PR #3554](https://github.com/PHPOffice/PhpSpreadsheet/pull/3554)
- Return Page Breaks in Order [Issue #3552](https://github.com/PHPOffice/PhpSpreadsheet/issues/3552) [PR #3555](https://github.com/PHPOffice/PhpSpreadsheet/pull/3555)
- Add position attribute for MemoryDrawing in Html [Issue #3529](https://github.com/PHPOffice/PhpSpreadsheet/issues/3529 [PR #3535](https://github.com/PHPOffice/PhpSpreadsheet/pull/3535)
- Allow Index_number as Array for VLOOKUP/HLOOKUP [Issue #3561](https://github.com/PHPOffice/PhpSpreadsheet/issues/3561 [PR #3570](https://github.com/PHPOffice/PhpSpreadsheet/pull/3570)
- Add Unsupported Options in Xml Spreadsheet [Issue #3566](https://github.com/PHPOffice/PhpSpreadsheet/issues/3566 [Issue #3568](https://github.com/PHPOffice/PhpSpreadsheet/issues/3568 [Issue #3569](https://github.com/PHPOffice/PhpSpreadsheet/issues/3569 [PR #3567](https://github.com/PHPOffice/PhpSpreadsheet/pull/3567)
- Changes to NUMBERVALUE, VALUE, DATEVALUE, TIMEVALUE [Issue #3574](https://github.com/PHPOffice/PhpSpreadsheet/issues/3574 [PR #3575](https://github.com/PHPOffice/PhpSpreadsheet/pull/3575)
- Redo calculation of color tinting [Issue #3550](https://github.com/PHPOffice/PhpSpreadsheet/issues/3550) [PR #3580](https://github.com/PHPOffice/PhpSpreadsheet/pull/3580)
- Accommodate Slash with preg_quote [PR #3582](https://github.com/PHPOffice/PhpSpreadsheet/pull/3582) [PR #3583](https://github.com/PHPOffice/PhpSpreadsheet/pull/3583) [PR #3584](https://github.com/PHPOffice/PhpSpreadsheet/pull/3584)
- HyperlinkBase Property and Html Handling of Properties [Issue #3573](https://github.com/PHPOffice/PhpSpreadsheet/issues/3573) [PR #3589](https://github.com/PHPOffice/PhpSpreadsheet/pull/3589)
- Improvements for Data Validation [Issue #3592](https://github.com/PHPOffice/PhpSpreadsheet/issues/3592) [Issue #3594](https://github.com/PHPOffice/PhpSpreadsheet/issues/3594) [PR #3605](https://github.com/PHPOffice/PhpSpreadsheet/pull/3605)

## 1.28.0 - 2023-02-25

### Added

- Support for configuring a Chart Title's overlay [PR #3325](https://github.com/PHPOffice/PhpSpreadsheet/pull/3325)
- Wizards for defining Number Format masks for Numbers, Percentages, Scientific, Currency and Accounting [PR #3334](https://github.com/PHPOffice/PhpSpreadsheet/pull/3334)
- Support for fixed value divisor in fractional Number Format Masks [PR #3339](https://github.com/PHPOffice/PhpSpreadsheet/pull/3339)
- Allow More Fonts/Fontnames for Exact Width Calculation [PR #3326](https://github.com/PHPOffice/PhpSpreadsheet/pull/3326) [Issue #3190](https://github.com/PHPOffice/PhpSpreadsheet/issues/3190)
- Allow override of the Value Binder when setting a Cell value [PR #3361](https://github.com/PHPOffice/PhpSpreadsheet/pull/3361)

### Changed

- Improved handling for @ placeholder in Number Format Masks [PR #3344](https://github.com/PHPOffice/PhpSpreadsheet/pull/3344)
- Improved handling for ? placeholder in Number Format Masks [PR #3394](https://github.com/PHPOffice/PhpSpreadsheet/pull/3394)
- Improved support for locale settings and currency codes when matching formatted strings to numerics in the Calculation Engine [PR #3373](https://github.com/PHPOffice/PhpSpreadsheet/pull/3373) and [PR #3374](https://github.com/PHPOffice/PhpSpreadsheet/pull/3374) 
- Improved support for locale settings and matching in the Advanced Value Binder [PR #3376](https://github.com/PHPOffice/PhpSpreadsheet/pull/3376)
- `toFormattedString` will now always return a string. This can affect the results of `toArray`, `namedRangeToArray`, and `rangeToArray`. [PR #3304](https://github.com/PHPOffice/PhpSpreadsheet/pull/3304)
- Value of constants FORMAT_CURRENCY_EUR and FORMAT_CURRENCY_USD is changed. [Issue #3577](https://github.com/PHPOffice/PhpSpreadsheet/issues/3577) [PR #3377](https://github.com/PHPOffice/PhpSpreadsheet/pull/3377)

### Deprecated

- Rationalisation of Pre-defined Currency Format Masks [PR #3377](https://github.com/PHPOffice/PhpSpreadsheet/pull/3377)

### Removed

- Nothing

### Fixed

- Calculation Engine doesn't evaluate Defined Name when default cell A1 is quote-prefixed [Issue #3335](https://github.com/PHPOffice/PhpSpreadsheet/issues/3335) [PR #3336](https://github.com/PHPOffice/PhpSpreadsheet/pull/3336)
- XLSX Writer - Array Formulas do not include function prefix [Issue #3337](https://github.com/PHPOffice/PhpSpreadsheet/issues/3337) [PR #3338](https://github.com/PHPOffice/PhpSpreadsheet/pull/3338)
- Permit Max Column for Row Breaks [Issue #3143](https://github.com/PHPOffice/PhpSpreadsheet/issues/3143) [PR #3345](https://github.com/PHPOffice/PhpSpreadsheet/pull/3345)
- AutoSize Columns should allow for dropdown icon when AutoFilter is for a Table [Issue #3356](https://github.com/PHPOffice/PhpSpreadsheet/issues/3356) [PR #3358](https://github.com/PHPOffice/PhpSpreadsheet/pull/3358) and for Center Alignment of Headers [Issue #3395](https://github.com/PHPOffice/PhpSpreadsheet/issues/3395) [PR #3399](https://github.com/PHPOffice/PhpSpreadsheet/pull/3399)
- Decimal Precision for Scientific Number Format Mask [Issue #3381](https://github.com/PHPOffice/PhpSpreadsheet/issues/3381) [PR #3382](https://github.com/PHPOffice/PhpSpreadsheet/pull/3382)
- Xls Writer Parser Handle Boolean Literals as Function Arguments [Issue #3369](https://github.com/PHPOffice/PhpSpreadsheet/issues/3369) [PR #3391](https://github.com/PHPOffice/PhpSpreadsheet/pull/3391)
- Conditional Formatting Improvements for Xlsx [Issue #3370](https://github.com/PHPOffice/PhpSpreadsheet/issues/3370) [Issue #3202](https://github.com/PHPOffice/PhpSpreadsheet/issues/3302) [PR #3372](https://github.com/PHPOffice/PhpSpreadsheet/pull/3372)
- Coerce Bool to Int for Mathematical Operations on Arrays [Issue #3389](https://github.com/PHPOffice/PhpSpreadsheet/issues/3389) [Issue #3396](https://github.com/PHPOffice/PhpSpreadsheet/issues/3396) [PR #3392](https://github.com/PHPOffice/PhpSpreadsheet/pull/3392)

## 1.27.1 - 2023-02-08

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Fix Composer --dev dependency issue with dealerdirect/phpcodesniffer-composer-installer renaming their `master` branch to `main`


## 1.27.0 - 2023-01-24

### Added

- Option to specify a range of columns/rows for the Row/Column `isEmpty()` methods [PR #3315](https://github.com/PHPOffice/PhpSpreadsheet/pull/3315)
- Option for Cell Iterator to return a null value or create and return a new cell when accessing a cell that doesn't exist [PR #3314](https://github.com/PHPOffice/PhpSpreadsheet/pull/3314)
- Support for Structured References in the Calculation Engine [PR #3261](https://github.com/PHPOffice/PhpSpreadsheet/pull/3261)
- Limited Support for Form Controls [PR #3130](https://github.com/PHPOffice/PhpSpreadsheet/pull/3130) [Issue #2396](https://github.com/PHPOffice/PhpSpreadsheet/issues/2396) [Issue #1770](https://github.com/PHPOffice/PhpSpreadsheet/issues/1770) [Issue #2388](https://github.com/PHPOffice/PhpSpreadsheet/issues/2388) [Issue #2904](https://github.com/PHPOffice/PhpSpreadsheet/issues/2904) [Issue #2661](https://github.com/PHPOffice/PhpSpreadsheet/issues/2661)

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Shared/JAMA is removed. [PR #3260](https://github.com/PHPOffice/PhpSpreadsheet/pull/3260)

### Fixed

- Namespace-Aware Code for SheetViewOptions, SheetProtection [PR #3230](https://github.com/PHPOffice/PhpSpreadsheet/pull/3230)
- Additional Method for XIRR if Newton-Raphson Doesn't Converge [Issue #689](https://github.com/PHPOffice/PhpSpreadsheet/issues/689) [PR #3262](https://github.com/PHPOffice/PhpSpreadsheet/pull/3262)
- Better Handling of Composite Charts [Issue #2333](https://github.com/PHPOffice/PhpSpreadsheet/issues/2333) [PR #3265](https://github.com/PHPOffice/PhpSpreadsheet/pull/3265)
- Update Column Reference for Columns Beginning with Y and Z [Issue #3263](https://github.com/PHPOffice/PhpSpreadsheet/issues/3263) [PR #3264](https://github.com/PHPOffice/PhpSpreadsheet/pull/3264)
- Honor Fit to 1-Page Height Html/Pdf [Issue #3266](https://github.com/PHPOffice/PhpSpreadsheet/issues/3266) [PR #3279](https://github.com/PHPOffice/PhpSpreadsheet/pull/3279)
- AND/OR/XOR Handling of Literal Strings [PR #3287](https://github.com/PHPOffice/PhpSpreadsheet/pull/3287)
- Xls Reader Vertical Break and Writer Page Order [Issue #3305](https://github.com/PHPOffice/PhpSpreadsheet/issues/3305) [PR #3306](https://github.com/PHPOffice/PhpSpreadsheet/pull/3306)


## 1.26.0 - 2022-12-21

### Added

- Extended flag options for the Reader `load()` and Writer `save()` methods
- Apply Row/Column limits (1048576 and XFD) in ReferenceHelper [PR #3213](https://github.com/PHPOffice/PhpSpreadsheet/pull/3213)
- Allow the creation of In-Memory Drawings from a string of binary image data, or from a stream. [PR #3157](https://github.com/PHPOffice/PhpSpreadsheet/pull/3157)
- Xlsx Reader support for Pivot Tables [PR #2829](https://github.com/PHPOffice/PhpSpreadsheet/pull/2829)
- Permit Date/Time Entered on Spreadsheet to be calculated as Float [Issue #1416](https://github.com/PHPOffice/PhpSpreadsheet/issues/1416) [PR #3121](https://github.com/PHPOffice/PhpSpreadsheet/pull/3121)

### Changed

- Nothing

### Deprecated

- Direct update of Calculation::suppressFormulaErrors is replaced with setter.
- Font public static variable defaultColumnWidths replaced with constant DEFAULT_COLUMN_WIDTHS.
- ExcelError public static variable errorCodes replaced with constant ERROR_CODES.
- NumberFormat constant FORMAT_DATE_YYYYMMDD2 replaced with existing identical FORMAT_DATE_YYYYMMDD.

### Removed

- Nothing

### Fixed

- Fixed handling for `_xlws` prefixed functions from Office365 [Issue #3245](https://github.com/PHPOffice/PhpSpreadsheet/issues/3245) [PR #3247](https://github.com/PHPOffice/PhpSpreadsheet/pull/3247)
- Conditionals formatting rules applied to rows/columns are removed [Issue #3184](https://github.com/PHPOffice/PhpSpreadsheet/issues/3184) [PR #3213](https://github.com/PHPOffice/PhpSpreadsheet/pull/3213)
- Treat strings containing currency or accounting values as floats in Calculation Engine operations [Issue #3165](https://github.com/PHPOffice/PhpSpreadsheet/issues/3165) [PR #3189](https://github.com/PHPOffice/PhpSpreadsheet/pull/3189)
- Treat strings containing percentage values as floats in Calculation Engine operations [Issue #3155](https://github.com/PHPOffice/PhpSpreadsheet/issues/3155) [PR #3156](https://github.com/PHPOffice/PhpSpreadsheet/pull/3156) and [PR #3164](https://github.com/PHPOffice/PhpSpreadsheet/pull/3164)
- Xlsx Reader Accept Palette of Fewer than 64 Colors [Issue #3093](https://github.com/PHPOffice/PhpSpreadsheet/issues/3093) [PR #3096](https://github.com/PHPOffice/PhpSpreadsheet/pull/3096)
- Use Locale-Independent Float Conversion for Xlsx Writer Custom Property [Issue #3095](https://github.com/PHPOffice/PhpSpreadsheet/issues/3095) [PR #3099](https://github.com/PHPOffice/PhpSpreadsheet/pull/3099)
- Allow setting AutoFilter range on a single cell or row [Issue #3102](https://github.com/PHPOffice/PhpSpreadsheet/issues/3102) [PR #3111](https://github.com/PHPOffice/PhpSpreadsheet/pull/3111)
- Xlsx Reader External Data Validations Flag Missing [Issue #2677](https://github.com/PHPOffice/PhpSpreadsheet/issues/2677) [PR #3078](https://github.com/PHPOffice/PhpSpreadsheet/pull/3078)
- Reduces extra memory usage on `__destruct()` calls [PR #3092](https://github.com/PHPOffice/PhpSpreadsheet/pull/3092)
- Additional properties for Trendlines [Issue #3011](https://github.com/PHPOffice/PhpSpreadsheet/issues/3011) [PR #3028](https://github.com/PHPOffice/PhpSpreadsheet/pull/3028)
- Calculation suppressFormulaErrors fix [Issue #1531](https://github.com/PHPOffice/PhpSpreadsheet/issues/1531) [PR #3092](https://github.com/PHPOffice/PhpSpreadsheet/pull/3092)
- Permit Date/Time Entered on Spreadsheet to be Calculated as Float [Issue #1416](https://github.com/PHPOffice/PhpSpreadsheet/issues/1416) [PR #3121](https://github.com/PHPOffice/PhpSpreadsheet/pull/3121)
- Incorrect Handling of Data Validation Formula Containing Ampersand [Issue #3145](https://github.com/PHPOffice/PhpSpreadsheet/issues/3145) [PR #3146](https://github.com/PHPOffice/PhpSpreadsheet/pull/3146)
- Xlsx Namespace Handling of Drawings, RowAndColumnAttributes, MergeCells [Issue #3138](https://github.com/PHPOffice/PhpSpreadsheet/issues/3138) [PR #3136](https://github.com/PHPOffice/PhpSpreadsheet/pull/3137)
- Generation3 Copy With Image in Footer [Issue #3126](https://github.com/PHPOffice/PhpSpreadsheet/issues/3126) [PR #3140](https://github.com/PHPOffice/PhpSpreadsheet/pull/3140)
- MATCH Function Problems with Int/Float Compare and Wildcards [Issue #3141](https://github.com/PHPOffice/PhpSpreadsheet/issues/3141) [PR #3142](https://github.com/PHPOffice/PhpSpreadsheet/pull/3142)
- Fix ODS Read Filter on number-columns-repeated cell [Issue #3148](https://github.com/PHPOffice/PhpSpreadsheet/issues/3148) [PR #3149](https://github.com/PHPOffice/PhpSpreadsheet/pull/3149)
- Problems Formatting Very Small and Very Large Numbers [Issue #3128](https://github.com/PHPOffice/PhpSpreadsheet/issues/3128) [PR #3152](https://github.com/PHPOffice/PhpSpreadsheet/pull/3152)
- XlsxWrite preserve line styles for y-axis, not just x-axis [PR #3163](https://github.com/PHPOffice/PhpSpreadsheet/pull/3163)
- Xlsx Namespace Handling of Drawings, RowAndColumnAttributes, MergeCells [Issue #3138](https://github.com/PHPOffice/PhpSpreadsheet/issues/3138) [PR #3137](https://github.com/PHPOffice/PhpSpreadsheet/pull/3137)
- More Detail for Cyclic Error Messages [Issue #3169](https://github.com/PHPOffice/PhpSpreadsheet/issues/3169) [PR #3170](https://github.com/PHPOffice/PhpSpreadsheet/pull/3170)
- Improved Documentation for Deprecations - many PRs [Issue #3162](https://github.com/PHPOffice/PhpSpreadsheet/issues/3162)


## 1.25.2 - 2022-09-25

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Composer dependency clash with ezyang/htmlpurifier


## 1.25.0 - 2022-09-25

### Added

- Implementation of the new `TEXTBEFORE()`, `TEXTAFTER()` and `TEXTSPLIT()` Excel Functions
- Implementation of the `ARRAYTOTEXT()` and `VALUETOTEXT()` Excel Functions
- Support for [mitoteam/jpgraph](https://packagist.org/packages/mitoteam/jpgraph) implementation of
  JpGraph library to render charts added.
- Charts: Add Gradients, Transparency, Hidden Axes, Rounded Corners, Trendlines, Date Axes.

### Changed

- Allow variant behaviour when merging cells [Issue #3065](https://github.com/PHPOffice/PhpSpreadsheet/issues/3065)
  - Merge methods now allow an additional `$behaviour` argument. Permitted values are:
    - Worksheet::MERGE_CELL_CONTENT_EMPTY - Empty the content of the hidden cells (the default behaviour)
    - Worksheet::MERGE_CELL_CONTENT_HIDE - Keep the content of the hidden cells
    - Worksheet::MERGE_CELL_CONTENT_MERGE - Move the content of the hidden cells into the first cell

### Deprecated

- Axis getLineProperty deprecated in favor of getLineColorProperty.
- Moved majorGridlines and minorGridlines from Chart to Axis. Setting either in Chart constructor or through Chart methods, or getting either using Chart methods is deprecated.
- Chart::EXCEL_COLOR_TYPE_* copied from Properties to ChartColor; use in Properties is deprecated.
- ChartColor::EXCEL_COLOR_TYPE_ARGB deprecated in favor of EXCEL_COLOR_TYPE_RGB ("A" component was never allowed).
- Misspelled Properties::LINE_STYLE_DASH_SQUERE_DOT deprecated in favor of LINE_STYLE_DASH_SQUARE_DOT.
- Clone not permitted for Spreadsheet. Spreadsheet->copy() can be used instead.

### Removed

- Nothing

### Fixed

- Fix update to defined names when inserting/deleting rows/columns [Issue #3076](https://github.com/PHPOffice/PhpSpreadsheet/issues/3076) [PR #3077](https://github.com/PHPOffice/PhpSpreadsheet/pull/3077)
- Fix DataValidation sqRef when inserting/deleting rows/columns [Issue #3056](https://github.com/PHPOffice/PhpSpreadsheet/issues/3056) [PR #3074](https://github.com/PHPOffice/PhpSpreadsheet/pull/3074)
- Named ranges not usable as anchors in OFFSET function [Issue #3013](https://github.com/PHPOffice/PhpSpreadsheet/issues/3013)
- Fully flatten an array [Issue #2955](https://github.com/PHPOffice/PhpSpreadsheet/issues/2955) [PR #2956](https://github.com/PHPOffice/PhpSpreadsheet/pull/2956)
- cellExists() and getCell() methods should support UTF-8 named cells [Issue #2987](https://github.com/PHPOffice/PhpSpreadsheet/issues/2987) [PR #2988](https://github.com/PHPOffice/PhpSpreadsheet/pull/2988)
- Spreadsheet copy fixed, clone disabled. [PR #2951](https://github.com/PHPOffice/PhpSpreadsheet/pull/2951)
- Fix PDF problems with text rotation and paper size. [Issue #1747](https://github.com/PHPOffice/PhpSpreadsheet/issues/1747) [Issue #1713](https://github.com/PHPOffice/PhpSpreadsheet/issues/1713) [PR #2960](https://github.com/PHPOffice/PhpSpreadsheet/pull/2960)
- Limited support for chart titles as formulas [Issue #2965](https://github.com/PHPOffice/PhpSpreadsheet/issues/2965) [Issue #749](https://github.com/PHPOffice/PhpSpreadsheet/issues/749) [PR #2971](https://github.com/PHPOffice/PhpSpreadsheet/pull/2971)
- Add Gradients, Transparency, and Hidden Axes to Chart [Issue #2257](https://github.com/PHPOffice/PhpSpreadsheet/issues/2257) [Issue #2229](https://github.com/PHPOffice/PhpSpreadsheet/issues/2929) [Issue #2935](https://github.com/PHPOffice/PhpSpreadsheet/issues/2935) [PR #2950](https://github.com/PHPOffice/PhpSpreadsheet/pull/2950)
- Chart Support for Rounded Corners and Trendlines [Issue #2968](https://github.com/PHPOffice/PhpSpreadsheet/issues/2968) [Issue #2815](https://github.com/PHPOffice/PhpSpreadsheet/issues/2815) [PR #2976](https://github.com/PHPOffice/PhpSpreadsheet/pull/2976)
- Add setName Method for Chart [Issue #2991](https://github.com/PHPOffice/PhpSpreadsheet/issues/2991) [PR #3001](https://github.com/PHPOffice/PhpSpreadsheet/pull/3001)
- Eliminate partial dependency on php-intl in StringHelper [Issue #2982](https://github.com/PHPOffice/PhpSpreadsheet/issues/2982) [PR #2994](https://github.com/PHPOffice/PhpSpreadsheet/pull/2994)
- Minor changes for Pdf [Issue #2999](https://github.com/PHPOffice/PhpSpreadsheet/issues/2999) [PR #3002](https://github.com/PHPOffice/PhpSpreadsheet/pull/3002) [PR #3006](https://github.com/PHPOffice/PhpSpreadsheet/pull/3006)
- Html/Pdf Do net set background color for cells using (default) nofill [PR #3016](https://github.com/PHPOffice/PhpSpreadsheet/pull/3016)
- Add support for Date Axis to Chart [Issue #2967](https://github.com/PHPOffice/PhpSpreadsheet/issues/2967) [PR #3018](https://github.com/PHPOffice/PhpSpreadsheet/pull/3018)
- Reconcile Differences Between Css and Excel for Cell Alignment [PR #3048](https://github.com/PHPOffice/PhpSpreadsheet/pull/3048)
- R1C1 Format Internationalization and Better Support for Relative Offsets [Issue #1704](https://github.com/PHPOffice/PhpSpreadsheet/issues/1704) [PR #3052](https://github.com/PHPOffice/PhpSpreadsheet/pull/3052)
- Minor Fix for Percentage Formatting [Issue #1929](https://github.com/PHPOffice/PhpSpreadsheet/issues/1929) [PR #3053](https://github.com/PHPOffice/PhpSpreadsheet/pull/3053)

## 1.24.1 - 2022-07-18

### Added

- Support for SimpleCache Interface versions 1.0, 2.0 and 3.0
- Add Chart Axis Option textRotation [Issue #2705](https://github.com/PHPOffice/PhpSpreadsheet/issues/2705) [PR #2940](https://github.com/PHPOffice/PhpSpreadsheet/pull/2940)

### Changed

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Fix Encoding issue with Html reader (PHP 8.2 deprecation for mb_convert_encoding) [Issue #2942](https://github.com/PHPOffice/PhpSpreadsheet/issues/2942) [PR #2943](https://github.com/PHPOffice/PhpSpreadsheet/pull/2943)
- Additional Chart fixes
  - Pie chart with part separated unwantedly [Issue #2506](https://github.com/PHPOffice/PhpSpreadsheet/issues/2506) [PR #2928](https://github.com/PHPOffice/PhpSpreadsheet/pull/2928)
  - Chart styling is lost on simple load / save process [Issue #1797](https://github.com/PHPOffice/PhpSpreadsheet/issues/1797) [Issue #2077](https://github.com/PHPOffice/PhpSpreadsheet/issues/2077) [PR #2930](https://github.com/PHPOffice/PhpSpreadsheet/pull/2930)
  - Can't create contour chart (surface 2d) [Issue #2931](https://github.com/PHPOffice/PhpSpreadsheet/issues/2931) [PR #2933](https://github.com/PHPOffice/PhpSpreadsheet/pull/2933)
- VLOOKUP Breaks When Array Contains Null Cells [Issue #2934](https://github.com/PHPOffice/PhpSpreadsheet/issues/2934) [PR #2939](https://github.com/PHPOffice/PhpSpreadsheet/pull/2939)

## 1.24.0 - 2022-07-09

Note that this will be the last 1.x branch release before the 2.x release. We will maintain both branches in parallel for a time; but users are requested to update to version 2.0 once that is fully available.

### Added

- Added `removeComment()` method for Worksheet [PR #2875](https://github.com/PHPOffice/PhpSpreadsheet/pull/2875/files)
- Add point size option for scatter charts [Issue #2298](https://github.com/PHPOffice/PhpSpreadsheet/issues/2298) [PR #2801](https://github.com/PHPOffice/PhpSpreadsheet/pull/2801)
- Basic support for Xlsx reading/writing Chart Sheets [PR #2830](https://github.com/PHPOffice/PhpSpreadsheet/pull/2830)

  Note that a ChartSheet is still only written as a normal Worksheet containing a single chart, not as an actual ChartSheet.

- Added Worksheet visibility in Ods Reader [PR #2851](https://github.com/PHPOffice/PhpSpreadsheet/pull/2851) and Gnumeric Reader [PR #2853](https://github.com/PHPOffice/PhpSpreadsheet/pull/2853)
- Added Worksheet visibility in Ods Writer [PR #2850](https://github.com/PHPOffice/PhpSpreadsheet/pull/2850)
- Allow Csv Reader to treat string as contents of file [Issue #1285](https://github.com/PHPOffice/PhpSpreadsheet/issues/1285) [PR #2792](https://github.com/PHPOffice/PhpSpreadsheet/pull/2792)
- Allow Csv Reader to store null string rather than leave cell empty [Issue #2840](https://github.com/PHPOffice/PhpSpreadsheet/issues/2840) [PR #2842](https://github.com/PHPOffice/PhpSpreadsheet/pull/2842)
- Provide new Worksheet methods to identify if a row or column is "empty", making allowance for different definitions of "empty":
  - Treat rows/columns containing no cell records as empty (default)
  - Treat cells containing a null value as empty
  - Treat cells containing an empty string as empty

### Changed

- Modify `rangeBoundaries()`, `rangeDimension()` and `getRangeBoundaries()` Coordinate methods to work with row/column ranges as well as with cell ranges and cells [PR #2926](https://github.com/PHPOffice/PhpSpreadsheet/pull/2926)
- Better enforcement of value modification to match specified datatype when using `setValueExplicit()`
- Relax validation of merge cells to allow merge for a single cell reference [Issue #2776](https://github.com/PHPOffice/PhpSpreadsheet/issues/2776)
- Memory and speed improvements, particularly for the Cell Collection, and the Writers.

  See [the Discussion section on github](https://github.com/PHPOffice/PhpSpreadsheet/discussions/2821) for details of performance across versions
- Improved performance for removing rows/columns from a worksheet

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Xls Reader resolving absolute named ranges to relative ranges [Issue #2826](https://github.com/PHPOffice/PhpSpreadsheet/issues/2826) [PR #2827](https://github.com/PHPOffice/PhpSpreadsheet/pull/2827)
- Null value handling in the Excel Math/Trig PRODUCT() function [Issue #2833](https://github.com/PHPOffice/PhpSpreadsheet/issues/2833) [PR #2834](https://github.com/PHPOffice/PhpSpreadsheet/pull/2834)
- Invalid Print Area defined in Xlsx corrupts internal storage of print area [Issue #2848](https://github.com/PHPOffice/PhpSpreadsheet/issues/2848) [PR #2849](https://github.com/PHPOffice/PhpSpreadsheet/pull/2849)
- Time interval formatting [Issue #2768](https://github.com/PHPOffice/PhpSpreadsheet/issues/2768) [PR #2772](https://github.com/PHPOffice/PhpSpreadsheet/pull/2772)
- Copy from Xls(x) to Html/Pdf loses drawings [PR #2788](https://github.com/PHPOffice/PhpSpreadsheet/pull/2788)
- Html Reader converting cell containing 0 to null string [Issue #2810](https://github.com/PHPOffice/PhpSpreadsheet/issues/2810) [PR #2813](https://github.com/PHPOffice/PhpSpreadsheet/pull/2813)
- Many fixes for Charts, especially, but not limited to, Scatter, Bubble, and Surface charts. [Issue #2762](https://github.com/PHPOffice/PhpSpreadsheet/issues/2762) [Issue #2299](https://github.com/PHPOffice/PhpSpreadsheet/issues/2299) [Issue #2700](https://github.com/PHPOffice/PhpSpreadsheet/issues/2700) [Issue #2817](https://github.com/PHPOffice/PhpSpreadsheet/issues/2817) [Issue #2763](https://github.com/PHPOffice/PhpSpreadsheet/issues/2763) [Issue #2219](https://github.com/PHPOffice/PhpSpreadsheet/issues/2219) [Issue #2863](https://github.com/PHPOffice/PhpSpreadsheet/issues/2863) [PR #2828](https://github.com/PHPOffice/PhpSpreadsheet/pull/2828) [PR #2841](https://github.com/PHPOffice/PhpSpreadsheet/pull/2841) [PR #2846](https://github.com/PHPOffice/PhpSpreadsheet/pull/2846) [PR #2852](https://github.com/PHPOffice/PhpSpreadsheet/pull/2852) [PR #2856](https://github.com/PHPOffice/PhpSpreadsheet/pull/2856) [PR #2865](https://github.com/PHPOffice/PhpSpreadsheet/pull/2865) [PR #2872](https://github.com/PHPOffice/PhpSpreadsheet/pull/2872) [PR #2879](https://github.com/PHPOffice/PhpSpreadsheet/pull/2879) [PR #2898](https://github.com/PHPOffice/PhpSpreadsheet/pull/2898) [PR #2906](https://github.com/PHPOffice/PhpSpreadsheet/pull/2906) [PR #2922](https://github.com/PHPOffice/PhpSpreadsheet/pull/2922) [PR #2923](https://github.com/PHPOffice/PhpSpreadsheet/pull/2923)
- Adjust both coordinates for two-cell anchors when rows/columns are added/deleted. [Issue #2908](https://github.com/PHPOffice/PhpSpreadsheet/issues/2908) [PR #2909](https://github.com/PHPOffice/PhpSpreadsheet/pull/2909)
- Keep calculated string results below 32K. [PR #2921](https://github.com/PHPOffice/PhpSpreadsheet/pull/2921)
- Filter out illegal Unicode char values FFFE/FFFF. [Issue #2897](https://github.com/PHPOffice/PhpSpreadsheet/issues/2897) [PR #2910](https://github.com/PHPOffice/PhpSpreadsheet/pull/2910)
- Better handling of REF errors and propagation of all errors in Calculation engine. [PR #2902](https://github.com/PHPOffice/PhpSpreadsheet/pull/2902)
- Calculating Engine regexp for Column/Row references when there are multiple quoted worksheet references in the formula [Issue #2874](https://github.com/PHPOffice/PhpSpreadsheet/issues/2874) [PR #2899](https://github.com/PHPOffice/PhpSpreadsheet/pull/2899)

## 1.23.0 - 2022-04-24

### Added

- Ods Writer support for Freeze Pane [Issue #2013](https://github.com/PHPOffice/PhpSpreadsheet/issues/2013) [PR #2755](https://github.com/PHPOffice/PhpSpreadsheet/pull/2755)
- Ods Writer support for setting column width/row height (including the use of AutoSize) [Issue #2346](https://github.com/PHPOffice/PhpSpreadsheet/issues/2346) [PR #2753](https://github.com/PHPOffice/PhpSpreadsheet/pull/2753)
- Introduced CellAddress, CellRange, RowRange and ColumnRange value objects that can be used as an alternative to a string value (e.g. `'C5'`, `'B2:D4'`, `'2:2'` or `'B:C'`) in appropriate contexts.
- Implementation of the FILTER(), SORT(), SORTBY() and UNIQUE() Lookup/Reference (array) functions.
- Implementation of the ISREF() Information function.
- Added support for reading "formatted" numeric values from Csv files; although default behaviour of reading these values as strings is preserved.

  (i.e a value of "12,345.67" can be read as numeric `12345.67`, not simply as a string `"12,345.67"`, if the `castFormattedNumberToNumeric()` setting is enabled.

  This functionality is locale-aware, using the server's locale settings to identify the thousands and decimal separators.

- Support for two cell anchor drawing of images. [#2532](https://github.com/PHPOffice/PhpSpreadsheet/pull/2532) [#2674](https://github.com/PHPOffice/PhpSpreadsheet/pull/2674)
- Limited support for Xls Reader to handle Conditional Formatting:

  Ranges and Rules are read, but style is currently limited to font size, weight and color; and to fill style and color.

- Add ability to suppress Mac line ending check for CSV [#2623](https://github.com/PHPOffice/PhpSpreadsheet/pull/2623)
- Initial support for creating and writing Tables (Xlsx Writer only) [PR #2671](https://github.com/PHPOffice/PhpSpreadsheet/pull/2671)

  See `/samples/Table` for examples of use.

  Note that PreCalculateFormulas needs to be disabled when saving spreadsheets containing tables with formulae (totals or column formulae).

### Changed

- Gnumeric Reader now loads number formatting for cells.
- Gnumeric Reader now correctly identifies selected worksheet and selected cells in a worksheet.
- Some Refactoring of the Ods Reader, moving all formula and address translation from Ods to Excel into a separate class to eliminate code duplication and ensure consistency.
- Make Boolean Conversion in Csv Reader locale-aware when using the String Value Binder.

  This is determined by the Calculation Engine locale setting.

  (i.e. `"Vrai"` wil be converted to a boolean `true` if the Locale is set to `fr`.)
- Allow `psr/simple-cache` 2.x

### Deprecated

- All Excel Function implementations in `Calculation\Functions` (including the Error functions) have been moved to dedicated classes for groups of related functions. See the docblocks against all the deprecated methods for details of the new methods to call instead. At some point, these old classes will be deleted.
- Worksheet methods that reference cells "byColumnandRow". All such methods have an equivalent that references the cell by its address (e.g. '`E3'` rather than `5, 3`).

  These functions now accept either a cell address string (`'E3')` or an array with columnId and rowId (`[5, 3]`) or a new `CellAddress` object as their `cellAddress`/`coordinate` argument.
  This includes the methods:
  - `setCellValueByColumnAndRow()` use the equivalent `setCellValue()`
  - `setCellValueExplicitByColumnAndRow()` use the equivalent `setCellValueExplicit()`
  - `getCellByColumnAndRow()` use the equivalent `getCell()`
  - `cellExistsByColumnAndRow()` use the equivalent `cellExists()`
  - `getStyleByColumnAndRow()` use the equivalent `getStyle()`
  - `setBreakByColumnAndRow()` use the equivalent `setBreak()`
  - `mergeCellsByColumnAndRow()` use the equivalent `mergeCells()`
  - `unmergeCellsByColumnAndRow()` use the equivalent `unmergeCells()`
  - `protectCellsByColumnAndRow()` use the equivalent `protectCells()`
  - `unprotectCellsByColumnAndRow()` use the equivalent `unprotectCells()`
  - `setAutoFilterByColumnAndRow()` use the equivalent `setAutoFilter()`
  - `freezePaneByColumnAndRow()` use the equivalent `freezePane()`
  - `getCommentByColumnAndRow()` use the equivalent `getComment()`
  - `setSelectedCellByColumnAndRow()` use the equivalent `setSelectedCells()`

  This change provides more consistency in the methods (not every "by cell address" method has an equivalent "byColumnAndRow" method);
  and the "by cell address" methods often provide more flexibility, such as allowing a range of cells, or referencing them by passing the defined name of a named range as the argument.

### Removed

- Nothing

### Fixed

- Make allowance for the AutoFilter dropdown icon in the first row of an Autofilter range when using Autosize columns. [Issue #2413](https://github.com/PHPOffice/PhpSpreadsheet/issues/2413) [PR #2754](https://github.com/PHPOffice/PhpSpreadsheet/pull/2754)
- Support for "chained" ranges (e.g. `A5:C10:C20:F1`) in the Calculation Engine; and also support for using named ranges with the Range operator (e.g. `NamedRange1:NamedRange2`) [Issue #2730](https://github.com/PHPOffice/PhpSpreadsheet/issues/2730) [PR #2746](https://github.com/PHPOffice/PhpSpreadsheet/pull/2746)
- Update Conditional Formatting ranges and rule conditions when inserting/deleting rows/columns [Issue #2678](https://github.com/PHPOffice/PhpSpreadsheet/issues/2678) [PR #2689](https://github.com/PHPOffice/PhpSpreadsheet/pull/2689)
- Allow `INDIRECT()` to accept row/column ranges as well as cell ranges [PR #2687](https://github.com/PHPOffice/PhpSpreadsheet/pull/2687)
- Fix bug when deleting cells with hyperlinks, where the hyperlink was then being "inherited" by whatever cell moved to that cell address.
- Fix bug in Conditional Formatting in the Xls Writer that resulted in a broken file when there were multiple conditional ranges in a worksheet.
- Fix Conditional Formatting in the Xls Writer to work with rules that contain string literals, cell references and formulae.
- Fix for setting Active Sheet to the first loaded worksheet when bookViews element isn't defined [Issue #2666](https://github.com/PHPOffice/PhpSpreadsheet/issues/2666) [PR #2669](https://github.com/PHPOffice/PhpSpreadsheet/pull/2669)
- Fixed behaviour of XLSX font style vertical align settings [PR #2619](https://github.com/PHPOffice/PhpSpreadsheet/pull/2619)
- Resolved formula translations to handle separators (row and column) for array functions as well as for function argument separators; and cleanly handle nesting levels.

  Note that this method is used when translating Excel functions between `en_us` and other locale languages, as well as when converting formulae between different spreadsheet formats (e.g. Ods to Excel).

  Nor is this a perfect solution, as there may still be issues when function calls have array arguments that themselves contain function calls; but it's still better than the current logic.
- Fix for escaping double quotes within a formula [Issue #1971](https://github.com/PHPOffice/PhpSpreadsheet/issues/1971) [PR #2651](https://github.com/PHPOffice/PhpSpreadsheet/pull/2651)
- Change open mode for output from `wb+` to `wb` [Issue #2372](https://github.com/PHPOffice/PhpSpreadsheet/issues/2372) [PR #2657](https://github.com/PHPOffice/PhpSpreadsheet/pull/2657)
- Use color palette if supplied [Issue #2499](https://github.com/PHPOffice/PhpSpreadsheet/issues/2499) [PR #2595](https://github.com/PHPOffice/PhpSpreadsheet/pull/2595)
- Xls reader treat drawing offsets as int rather than float [PR #2648](https://github.com/PHPOffice/PhpSpreadsheet/pull/2648)
- Handle booleans in conditional styles properly [PR #2654](https://github.com/PHPOffice/PhpSpreadsheet/pull/2654)
- Fix for reading files in the root directory of a ZipFile, which should not be prefixed by relative paths ("./") as dirname($filename) does by default.
- Fix invalid style of cells in empty columns with columnDimensions and rows with rowDimensions in added external sheet. [PR #2739](https://github.com/PHPOffice/PhpSpreadsheet/pull/2739)
- Time Interval Formatting [Issue #2768](https://github.com/PHPOffice/PhpSpreadsheet/issues/2768) [PR #2772](https://github.com/PHPOffice/PhpSpreadsheet/pull/2772)

## 1.22.0 - 2022-02-18

### Added

- Namespacing phase 2 - styles.
[PR #2471](https://github.com/PHPOffice/PhpSpreadsheet/pull/2471)

- Improved support for passing of array arguments to Excel function implementations to return array results (where appropriate). [Issue #2551](https://github.com/PHPOffice/PhpSpreadsheet/issues/2551)

  This is the first stage in an ongoing process of adding array support to all appropriate function implementations,
- Support for the Excel365 Math/Trig SEQUENCE() function [PR #2536](https://github.com/PHPOffice/PhpSpreadsheet/pull/2536)
- Support for the Excel365 Math/Trig RANDARRAY() function [PR #2540](https://github.com/PHPOffice/PhpSpreadsheet/pull/2540)

  Note that the Spill Operator is not yet supported in the Calculation Engine; but this can still be useful for defining array constants.
- Improved support for Conditional Formatting Rules [PR #2491](https://github.com/PHPOffice/PhpSpreadsheet/pull/2491)
  - Provide support for a wider range of Conditional Formatting Rules for Xlsx Reader/Writer:
    - Cells Containing (cellIs)
    - Specific Text (containing, notContaining, beginsWith, endsWith)
    - Dates Occurring (all supported timePeriods)
    - Blanks/NoBlanks
    - Errors/NoErrors
    - Duplicates/Unique
    - Expression
  - Provision of CF Wizards (for all the above listed rule types) to help create/modify CF Rules without having to manage all the combinations of types/operators, and the complexities of formula expressions, or the text/timePeriod attributes.

    See [documentation](https://phpspreadsheet.readthedocs.io/en/latest/topics/conditional-formatting/) for details

  - Full support of the above CF Rules for the Xlsx Reader and Writer; even when the file being loaded has CF rules listed in the `<extLst><ext><ConditionalFormattings>` element for the worksheet rather than the `<ConditionalFormatting>` element.
  - Provision of a CellMatcher to identify if rules are matched for a cell, and which matching style will be applied.
  - Improved documentation and examples, covering all supported CF rule types.
  - Add support for one digit decimals (FORMAT_NUMBER_0, FORMAT_PERCENTAGE_0). [PR #2525](https://github.com/PHPOffice/PhpSpreadsheet/pull/2525)
  - Initial work enabling Excel function implementations for handling arrays as arguments when used in "array formulae" [#2562](https://github.com/PHPOffice/PhpSpreadsheet/issues/2562)
  - Enable most of the Date/Time functions to accept array arguments [#2573](https://github.com/PHPOffice/PhpSpreadsheet/issues/2573)
  - Array ready functions - Text, Math/Trig, Statistical, Engineering and Logical [#2580](https://github.com/PHPOffice/PhpSpreadsheet/issues/2580)

### Changed

- Additional Russian translations for Excel Functions (courtesy of aleks-samurai).
- Improved code coverage for NumberFormat. [PR #2556](https://github.com/PHPOffice/PhpSpreadsheet/pull/2556)
- Extract some methods from the Calculation Engine into dedicated classes [#2537](https://github.com/PHPOffice/PhpSpreadsheet/issues/2537)
- Eliminate calls to `flattenSingleValue()` that are no longer required when we're checking for array values as arguments [#2590](https://github.com/PHPOffice/PhpSpreadsheet/issues/2590)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Fixed `ReferenceHelper@insertNewBefore` behavior when removing column before last column with null value [PR #2541](https://github.com/PHPOffice/PhpSpreadsheet/pull/2541)
- Fix bug with `DOLLARDE()` and `DOLLARFR()` functions when the dollar value is negative [Issue #2578](https://github.com/PHPOffice/PhpSpreadsheet/issues/2578) [PR #2579](https://github.com/PHPOffice/PhpSpreadsheet/pull/2579)
- Fix partial function name matching when translating formulae from Russian to English [Issue #2533](https://github.com/PHPOffice/PhpSpreadsheet/issues/2533) [PR #2534](https://github.com/PHPOffice/PhpSpreadsheet/pull/2534)
- Various bugs related to Conditional Formatting Rules, and errors in the Xlsx Writer for Conditional Formatting [PR #2491](https://github.com/PHPOffice/PhpSpreadsheet/pull/2491)
- Xlsx Reader merge range fixes. [Issue #2501](https://github.com/PHPOffice/PhpSpreadsheet/issues/2501) [PR #2504](https://github.com/PHPOffice/PhpSpreadsheet/pull/2504)
- Handle explicit "date" type for Cell in Xlsx Reader. [Issue #2373](https://github.com/PHPOffice/PhpSpreadsheet/issues/2373) [PR #2485](https://github.com/PHPOffice/PhpSpreadsheet/pull/2485)
- Recalibrate Row/Column Dimensions after removeRow/Column. [Issue #2442](https://github.com/PHPOffice/PhpSpreadsheet/issues/2442) [PR #2486](https://github.com/PHPOffice/PhpSpreadsheet/pull/2486)
- Refinement for XIRR. [Issue #2469](https://github.com/PHPOffice/PhpSpreadsheet/issues/2469) [PR #2487](https://github.com/PHPOffice/PhpSpreadsheet/pull/2487)
- Xlsx Reader handle cell with non-null explicit type but null value. [Issue #2488](https://github.com/PHPOffice/PhpSpreadsheet/issues/2488) [PR #2489](https://github.com/PHPOffice/PhpSpreadsheet/pull/2489)
- Xlsx Reader fix height and width for oneCellAnchorDrawings. [PR #2492](https://github.com/PHPOffice/PhpSpreadsheet/pull/2492)
- Fix rounding error in NumberFormat::NUMBER_PERCENTAGE, NumberFormat::NUMBER_PERCENTAGE_00. [PR #2555](https://github.com/PHPOffice/PhpSpreadsheet/pull/2555)
- Don't treat thumbnail file as xml. [Issue #2516](https://github.com/PHPOffice/PhpSpreadsheet/issues/2516) [PR #2517](https://github.com/PHPOffice/PhpSpreadsheet/pull/2517)
- Eliminating Xlsx Reader warning when no sz tag for RichText. [Issue #2542](https://github.com/PHPOffice/PhpSpreadsheet/issues/2542) [PR #2550](https://github.com/PHPOffice/PhpSpreadsheet/pull/2550)
- Fix Xlsx/Xls Writer handling of inline strings. [Issue #353](https://github.com/PHPOffice/PhpSpreadsheet/issues/353) [PR #2569](https://github.com/PHPOffice/PhpSpreadsheet/pull/2569)
- Richtext colors were not being read correctly after namespace change [#2458](https://github.com/PHPOffice/PhpSpreadsheet/issues/2458)
- Fix discrepancy between the way markdown tables are rendered in ReadTheDocs and in PHPStorm [#2520](https://github.com/PHPOffice/PhpSpreadsheet/issues/2520)
- Update Russian Functions Text File [#2557](https://github.com/PHPOffice/PhpSpreadsheet/issues/2557)
- Fix documentation, instantiation example [#2564](https://github.com/PHPOffice/PhpSpreadsheet/issues/2564)


## 1.21.0 - 2022-01-06

### Added

- Ability to add a picture to the background of the comment. Supports four image formats: png, jpeg, gif, bmp. New `Comment::setSizeAsBackgroundImage()` to change the size of a comment to the size of a background image. [Issue #1547](https://github.com/PHPOffice/PhpSpreadsheet/issues/1547) [PR #2422](https://github.com/PHPOffice/PhpSpreadsheet/pull/2422)
- Ability to set default paper size and orientation [PR #2410](https://github.com/PHPOffice/PhpSpreadsheet/pull/2410)
- Ability to extend AutoFilter to Maximum Row [PR #2414](https://github.com/PHPOffice/PhpSpreadsheet/pull/2414)

### Changed

- Xlsx Writer will evaluate AutoFilter only if it is as yet unevaluated, or has changed since it was last evaluated [PR #2414](https://github.com/PHPOffice/PhpSpreadsheet/pull/2414)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Rounding in `NumberFormatter` [Issue #2385](https://github.com/PHPOffice/PhpSpreadsheet/issues/2385) [PR #2399](https://github.com/PHPOffice/PhpSpreadsheet/pull/2399)
- Support for themes [Issue #2075](https://github.com/PHPOffice/PhpSpreadsheet/issues/2075) [Issue #2387](https://github.com/PHPOffice/PhpSpreadsheet/issues/2387) [PR #2403](https://github.com/PHPOffice/PhpSpreadsheet/pull/2403)
- Read spreadsheet with `#` in name [Issue #2405](https://github.com/PHPOffice/PhpSpreadsheet/issues/2405) [PR #2409](https://github.com/PHPOffice/PhpSpreadsheet/pull/2409)
- Improve PDF support for page size and orientation [Issue #1691](https://github.com/PHPOffice/PhpSpreadsheet/issues/1691) [PR #2410](https://github.com/PHPOffice/PhpSpreadsheet/pull/2410)
- Wildcard handling issues in text match [Issue #2430](https://github.com/PHPOffice/PhpSpreadsheet/issues/2430) [PR #2431](https://github.com/PHPOffice/PhpSpreadsheet/pull/2431)
- Respect DataType in `insertNewBefore` [PR #2433](https://github.com/PHPOffice/PhpSpreadsheet/pull/2433)
- Handle rows explicitly hidden after AutoFilter [Issue #1641](https://github.com/PHPOffice/PhpSpreadsheet/issues/1641) [PR #2414](https://github.com/PHPOffice/PhpSpreadsheet/pull/2414)
- Special characters in image file name [Issue #1470](https://github.com/PHPOffice/PhpSpreadsheet/issues/1470) [Issue #2415](https://github.com/PHPOffice/PhpSpreadsheet/issues/2415) [PR #2416](https://github.com/PHPOffice/PhpSpreadsheet/pull/2416)
- Mpdf with very many styles [Issue #2432](https://github.com/PHPOffice/PhpSpreadsheet/issues/2432) [PR #2434](https://github.com/PHPOffice/PhpSpreadsheet/pull/2434)
- Name clashes between parsed and unparsed drawings [Issue #1767](https://github.com/PHPOffice/PhpSpreadsheet/issues/1767) [Issue #2396](https://github.com/PHPOffice/PhpSpreadsheet/issues/2396) [PR #2423](https://github.com/PHPOffice/PhpSpreadsheet/pull/2423)
- Fill pattern start and end colors [Issue #2441](https://github.com/PHPOffice/PhpSpreadsheet/issues/2441) [PR #2444](https://github.com/PHPOffice/PhpSpreadsheet/pull/2444)
- General style specified in wrong case [Issue #2450](https://github.com/PHPOffice/PhpSpreadsheet/issues/2450) [PR #2451](https://github.com/PHPOffice/PhpSpreadsheet/pull/2451)
- Null passed to `AutoFilter::setRange()` [Issue #2281](https://github.com/PHPOffice/PhpSpreadsheet/issues/2281) [PR #2454](https://github.com/PHPOffice/PhpSpreadsheet/pull/2454)
- Another undefined index in Xls reader (#2470) [Issue #2463](https://github.com/PHPOffice/PhpSpreadsheet/issues/2463) [PR #2470](https://github.com/PHPOffice/PhpSpreadsheet/pull/2470)
- Allow single-cell checks on conditional styles, even when the style is configured for a range of cells (#) [PR #2483](https://github.com/PHPOffice/PhpSpreadsheet/pull/2483)

## 1.20.0 - 2021-11-23

### Added

- Xlsx Writer Support for WMF Files [#2339](https://github.com/PHPOffice/PhpSpreadsheet/issues/2339)
- Use standard temporary file for internal use of HTMLPurifier [#2383](https://github.com/PHPOffice/PhpSpreadsheet/issues/2383)

### Changed

- Drop support for PHP 7.2, according to https://phpspreadsheet.readthedocs.io/en/latest/#php-version-support
- Use native typing for objects that were already documented as such

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Fixed null conversation for strToUpper [#2292](https://github.com/PHPOffice/PhpSpreadsheet/issues/2292)
- Fixed Trying to access array offset on value of type null (Xls Reader) [#2315](https://github.com/PHPOffice/PhpSpreadsheet/issues/2315)
- Don't corrupt XLSX files containing data validation [#2377](https://github.com/PHPOffice/PhpSpreadsheet/issues/2377)
- Non-fixed cells were not updated if shared formula has a fixed cell [#2354](https://github.com/PHPOffice/PhpSpreadsheet/issues/2354)
- Declare key of generic ArrayObject
- CSV reader better support for boolean values [#2374](https://github.com/PHPOffice/PhpSpreadsheet/pull/2374)
- Some ZIP file could not be read [#2376](https://github.com/PHPOffice/PhpSpreadsheet/pull/2376)
- Fix regression were hyperlinks could not be read [#2391](https://github.com/PHPOffice/PhpSpreadsheet/pull/2391)
- AutoFilter Improvements [#2393](https://github.com/PHPOffice/PhpSpreadsheet/pull/2393)
- Don't corrupt file when using chart with fill color [#589](https://github.com/PHPOffice/PhpSpreadsheet/pull/589)
- Restore imperfect array formula values in xlsx writer [#2343](https://github.com/PHPOffice/PhpSpreadsheet/pull/2343)
- Restore explicit list of changes to PHPExcel migration document [#1546](https://github.com/PHPOffice/PhpSpreadsheet/issues/1546)

## 1.19.0 - 2021-10-31

### Added

- Ability to set style on named range, and validate input to setSelectedCells [Issue #2279](https://github.com/PHPOffice/PhpSpreadsheet/issues/2279) [PR #2280](https://github.com/PHPOffice/PhpSpreadsheet/pull/2280)
- Process comments in Sylk file [Issue #2276](https://github.com/PHPOffice/PhpSpreadsheet/issues/2276) [PR #2277](https://github.com/PHPOffice/PhpSpreadsheet/pull/2277)
- Addition of Custom Properties to Ods Writer, and 32-bit-safe timestamps for Document Properties [PR #2113](https://github.com/PHPOffice/PhpSpreadsheet/pull/2113)
- Added callback to CSV reader to set user-specified defaults for various properties (especially for escape which has a poor PHP-inherited default of backslash which does not correspond with Excel) [PR #2103](https://github.com/PHPOffice/PhpSpreadsheet/pull/2103)
- Phase 1 of better namespace handling for Xlsx, resolving many open issues [PR #2173](https://github.com/PHPOffice/PhpSpreadsheet/pull/2173) [PR #2204](https://github.com/PHPOffice/PhpSpreadsheet/pull/2204) [PR #2303](https://github.com/PHPOffice/PhpSpreadsheet/pull/2303)
- Add ability to extract images if source is a URL [Issue #1997](https://github.com/PHPOffice/PhpSpreadsheet/issues/1997) [PR #2072](https://github.com/PHPOffice/PhpSpreadsheet/pull/2072)
- Support for passing flags in the Reader `load()` and Writer `save()`methods, and through the IOFactory, to set behaviours [PR #2136](https://github.com/PHPOffice/PhpSpreadsheet/pull/2136)
    - See [documentation](https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-and-writing-to-file/#readerwriter-flags) for details
- More flexibility in the StringValueBinder to determine what datatypes should be treated as strings [PR #2138](https://github.com/PHPOffice/PhpSpreadsheet/pull/2138)
- Helper class for conversion between css size Units of measure (`px`, `pt`, `pc`, `in`, `cm`, `mm`) [PR #2152](https://github.com/PHPOffice/PhpSpreadsheet/issues/2145)
- Allow Row height and Column Width to be set using different units of measure (`px`, `pt`, `pc`, `in`, `cm`, `mm`), rather than only in points or MS Excel column width units [PR #2152](https://github.com/PHPOffice/PhpSpreadsheet/issues/2145)
- Ability to stream to an Amazon S3 bucket [Issue #2249](https://github.com/PHPOffice/PhpSpreadsheet/issues/2249)
- Provided a Size Helper class to validate size values (pt, px, em) [PR #1694](https://github.com/PHPOffice/PhpSpreadsheet/pull/1694)

### Changed

- Nothing.

### Deprecated

- PHP 8.1 will deprecate auto_detect_line_endings. As a result of this change, Csv Reader using some release after PHP8.1 will no longer be able to handle a Csv with Mac line endings.

### Removed

- Nothing.

### Fixed

- Unexpected format in Xlsx Timestamp [Issue #2331](https://github.com/PHPOffice/PhpSpreadsheet/issues/2331) [PR #2332](https://github.com/PHPOffice/PhpSpreadsheet/pull/2332)
- Corrections for HLOOKUP [Issue #2123](https://github.com/PHPOffice/PhpSpreadsheet/issues/2123) [PR #2330](https://github.com/PHPOffice/PhpSpreadsheet/pull/2330)
- Corrections for Xlsx Read Comments [Issue #2316](https://github.com/PHPOffice/PhpSpreadsheet/issues/2316) [PR #2329](https://github.com/PHPOffice/PhpSpreadsheet/pull/2329)
- Lowercase Calibri font names [Issue #2273](https://github.com/PHPOffice/PhpSpreadsheet/issues/2273) [PR #2325](https://github.com/PHPOffice/PhpSpreadsheet/pull/2325)
- isFormula Referencing Sheet with Space in Title [Issue #2304](https://github.com/PHPOffice/PhpSpreadsheet/issues/2304) [PR #2306](https://github.com/PHPOffice/PhpSpreadsheet/pull/2306)
- Xls Reader Fatal Error due to Undefined Offset [Issue #1114](https://github.com/PHPOffice/PhpSpreadsheet/issues/1114) [PR #2308](https://github.com/PHPOffice/PhpSpreadsheet/pull/2308)
- Permit Csv Reader delimiter to be set to null [Issue #2287](https://github.com/PHPOffice/PhpSpreadsheet/issues/2287) [PR #2288](https://github.com/PHPOffice/PhpSpreadsheet/pull/2288)
- Csv Reader did not handle booleans correctly [PR #2232](https://github.com/PHPOffice/PhpSpreadsheet/pull/2232)
- Problems when deleting sheet with local defined name [Issue #2266](https://github.com/PHPOffice/PhpSpreadsheet/issues/2266) [PR #2284](https://github.com/PHPOffice/PhpSpreadsheet/pull/2284)
- Worksheet passwords were not always handled correctly [Issue #1897](https://github.com/PHPOffice/PhpSpreadsheet/issues/1897) [PR #2197](https://github.com/PHPOffice/PhpSpreadsheet/pull/2197)
- Gnumeric Reader will now distinguish between Created and Modified timestamp [PR #2133](https://github.com/PHPOffice/PhpSpreadsheet/pull/2133)
- Xls Reader will now handle MACCENTRALEUROPE with or without hyphen [Issue #549](https://github.com/PHPOffice/PhpSpreadsheet/issues/549) [PR #2213](https://github.com/PHPOffice/PhpSpreadsheet/pull/2213)
- Tweaks to input file validation [Issue #1718](https://github.com/PHPOffice/PhpSpreadsheet/issues/1718) [PR #2217](https://github.com/PHPOffice/PhpSpreadsheet/pull/2217)
- Html Reader did not handle comments correctly [Issue #2234](https://github.com/PHPOffice/PhpSpreadsheet/issues/2234) [PR #2235](https://github.com/PHPOffice/PhpSpreadsheet/pull/2235)
- Apache OpenOffice Uses Unexpected Case for General format [Issue #2239](https://github.com/PHPOffice/PhpSpreadsheet/issues/2239) [PR #2242](https://github.com/PHPOffice/PhpSpreadsheet/pull/2242)
- Problems with fraction formatting [Issue #2253](https://github.com/PHPOffice/PhpSpreadsheet/issues/2253) [PR #2254](https://github.com/PHPOffice/PhpSpreadsheet/pull/2254)
- Xlsx Reader had problems reading file with no styles.xml or empty styles.xml [Issue #2246](https://github.com/PHPOffice/PhpSpreadsheet/issues/2246) [PR #2247](https://github.com/PHPOffice/PhpSpreadsheet/pull/2247)
- Xlsx Reader did not read Data Validation flags correctly [Issue #2224](https://github.com/PHPOffice/PhpSpreadsheet/issues/2224) [PR #2225](https://github.com/PHPOffice/PhpSpreadsheet/pull/2225)
- Better handling of empty arguments in Calculation engine [PR #2143](https://github.com/PHPOffice/PhpSpreadsheet/pull/2143)
- Many fixes for Autofilter [Issue #2216](https://github.com/PHPOffice/PhpSpreadsheet/issues/2216) [PR #2141](https://github.com/PHPOffice/PhpSpreadsheet/pull/2141) [PR #2162](https://github.com/PHPOffice/PhpSpreadsheet/pull/2162) [PR #2218](https://github.com/PHPOffice/PhpSpreadsheet/pull/2218)
- Locale generator will now use Unix line endings even on Windows [Issue #2172](https://github.com/PHPOffice/PhpSpreadsheet/issues/2172) [PR #2174](https://github.com/PHPOffice/PhpSpreadsheet/pull/2174)
- Support differences in implementation of Text functions between Excel/Ods/Gnumeric [PR #2151](https://github.com/PHPOffice/PhpSpreadsheet/pull/2151)
- Fixes to places where PHP8.1 enforces new or previously unenforced restrictions [PR #2137](https://github.com/PHPOffice/PhpSpreadsheet/pull/2137) [PR #2191](https://github.com/PHPOffice/PhpSpreadsheet/pull/2191) [PR #2231](https://github.com/PHPOffice/PhpSpreadsheet/pull/2231)
- Clone for HashTable was incorrect [PR #2130](https://github.com/PHPOffice/PhpSpreadsheet/pull/2130)
- Xlsx Reader was not evaluating Document Security Lock correctly [PR #2128](https://github.com/PHPOffice/PhpSpreadsheet/pull/2128)
- Error in COUPNCD handling end of month [Issue #2116](https://github.com/PHPOffice/PhpSpreadsheet/issues/2116) [PR #2119](https://github.com/PHPOffice/PhpSpreadsheet/pull/2119)
- Xls Writer Parser did not handle concatenation operator correctly [PR #2080](https://github.com/PHPOffice/PhpSpreadsheet/pull/2080)
- Xlsx Writer did not handle boolean false correctly [Issue #2082](https://github.com/PHPOffice/PhpSpreadsheet/issues/2082) [PR #2087](https://github.com/PHPOffice/PhpSpreadsheet/pull/2087)
- SUM needs to treat invalid strings differently depending on whether they come from a cell or are used as literals [Issue #2042](https://github.com/PHPOffice/PhpSpreadsheet/issues/2042) [PR #2045](https://github.com/PHPOffice/PhpSpreadsheet/pull/2045)
- Html reader could have set illegal coordinates when dealing with embedded tables [Issue #2029](https://github.com/PHPOffice/PhpSpreadsheet/issues/2029) [PR #2032](https://github.com/PHPOffice/PhpSpreadsheet/pull/2032)
- Documentation for printing gridlines was wrong [PR #2188](https://github.com/PHPOffice/PhpSpreadsheet/pull/2188)
- Return Value Error - DatabaseAbstruct::buildQuery() return null but must be string [Issue #2158](https://github.com/PHPOffice/PhpSpreadsheet/issues/2158) [PR #2160](https://github.com/PHPOffice/PhpSpreadsheet/pull/2160)
- Xlsx reader not recognize data validations that references another sheet [Issue #1432](https://github.com/PHPOffice/PhpSpreadsheet/issues/1432) [Issue #2149](https://github.com/PHPOffice/PhpSpreadsheet/issues/2149) [PR #2150](https://github.com/PHPOffice/PhpSpreadsheet/pull/2150) [PR #2265](https://github.com/PHPOffice/PhpSpreadsheet/pull/2265)
- Don't calculate cell width for autosize columns if a cell contains a null or empty string value [Issue #2165](https://github.com/PHPOffice/PhpSpreadsheet/issues/2165) [PR #2167](https://github.com/PHPOffice/PhpSpreadsheet/pull/2167)
- Allow negative interest rate values in a number of the Financial functions (`PPMT()`, `PMT()`, `FV()`, `PV()`, `NPER()`, etc) [Issue #2163](https://github.com/PHPOffice/PhpSpreadsheet/issues/2163) [PR #2164](https://github.com/PHPOffice/PhpSpreadsheet/pull/2164)
- Xls Reader changing grey background to black in Excel template [Issue #2147](https://github.com/PHPOffice/PhpSpreadsheet/issues/2147) [PR #2156](https://github.com/PHPOffice/PhpSpreadsheet/pull/2156)
- Column width and Row height styles in the Html Reader when the value includes a unit of measure [Issue #2145](https://github.com/PHPOffice/PhpSpreadsheet/issues/2145).
- Data Validation flags not set correctly when reading XLSX files [Issue #2224](https://github.com/PHPOffice/PhpSpreadsheet/issues/2224) [PR #2225](https://github.com/PHPOffice/PhpSpreadsheet/pull/2225)
- Reading XLSX files without styles.xml throws an exception [Issue #2246](https://github.com/PHPOffice/PhpSpreadsheet/issues/2246)
- Improved performance of `Style::applyFromArray()` when applied to several cells [PR #1785](https://github.com/PHPOffice/PhpSpreadsheet/issues/1785).
- Improve XLSX parsing speed if no readFilter is applied (again) - [#772](https://github.com/PHPOffice/PhpSpreadsheet/issues/772)

## 1.18.0 - 2021-05-31

### Added

- Enhancements to CSV Reader, allowing options to be set when using `IOFactory::load()` with a callback to set delimiter, enclosure, charset etc [PR #2103](https://github.com/PHPOffice/PhpSpreadsheet/pull/2103) - See [documentation](https://github.com/PHPOffice/PhpSpreadsheet/blob/master/docs/topics/reading-and-writing-to-file.md#csv-comma-separated-values) for details.
- Implemented basic AutoFiltering for Ods Reader and Writer [PR #2053](https://github.com/PHPOffice/PhpSpreadsheet/pull/2053)
- Implemented basic AutoFiltering for Gnumeric Reader [PR #2055](https://github.com/PHPOffice/PhpSpreadsheet/pull/2055)
- Improved support for Row and Column ranges in formulae [Issue #1755](https://github.com/PHPOffice/PhpSpreadsheet/issues/1755) [PR #2028](https://github.com/PHPOffice/PhpSpreadsheet/pull/2028)
- Implemented URLENCODE() Web Function
- Implemented the CHITEST(), CHISQ.DIST() and CHISQ.INV() and equivalent Statistical functions, for both left- and right-tailed distributions.
- Support for ActiveSheet and SelectedCells in the ODS Reader and Writer [PR #1908](https://github.com/PHPOffice/PhpSpreadsheet/pull/1908)
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
- Fixed issue where array key check for existince before accessing arrays in Xlsx.php [PR #1970](https://github.com/PHPOffice/PhpSpreadsheet/pull/1970)
- Fixed issue with quoted strings in number format mask rendered with toFormattedString() [Issue 1972#](https://github.com/PHPOffice/PhpSpreadsheet/issues/1972) [PR #1978](https://github.com/PHPOffice/PhpSpreadsheet/pull/1978)
- Fixed issue with percentage formats in number format mask rendered with toFormattedString() [Issue 1929#](https://github.com/PHPOffice/PhpSpreadsheet/issues/1929) [PR #1928](https://github.com/PHPOffice/PhpSpreadsheet/pull/1928)
- Fixed issue with _ spacing character in number format mask corrupting output from toFormattedString() [Issue 1924#](https://github.com/PHPOffice/PhpSpreadsheet/issues/1924) [PR #1927](https://github.com/PHPOffice/PhpSpreadsheet/pull/1927)
- Fix for [Issue #1887](https://github.com/PHPOffice/PhpSpreadsheet/issues/1887) - Lose Track of Selected Cells After Save
- Fixed issue with Xlsx@listWorksheetInfo not returning any data
- Fixed invalid arguments triggering mb_substr() error in LEFT(), MID() and RIGHT() text functions [Issue #640](https://github.com/PHPOffice/PhpSpreadsheet/issues/640)
- Fix for [Issue #1916](https://github.com/PHPOffice/PhpSpreadsheet/issues/1916) - Invalid signature check for XML files
- Fix change in `Font::setSize()` behavior for PHP8 [PR #2100](https://github.com/PHPOffice/PhpSpreadsheet/pull/2100)

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
- Fix problem resulting from  literal dot inside quotes in number format masks [PR #1830](https://github.com/PHPOffice/PhpSpreadsheet/pull/1830)
- Resolve Google Sheets Xlsx charts issue. Google Sheets uses oneCellAnchor positioning and does not include *Cache values in the exported Xlsx [PR #1761](https://github.com/PHPOffice/PhpSpreadsheet/pull/1761)
- Fix for Xlsx Chart axis titles mapping to correct X or Y axis label when only one is present [PR #1760](https://github.com/PHPOffice/PhpSpreadsheet/pull/1760)
- Fix For Null Exception on ODS Read of Page Settings. [#1772](https://github.com/PHPOffice/PhpSpreadsheet/issues/1772)
- Fix Xlsx reader overriding manually set number format with builtin number format [PR #1805](https://github.com/PHPOffice/PhpSpreadsheet/pull/1805)
- Fix Xlsx reader cell alignment [PR #1710](https://github.com/PHPOffice/PhpSpreadsheet/pull/1710)
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

- Fixed issue with absolute path in worksheets' Target [PR #1769](https://github.com/PHPOffice/PhpSpreadsheet/pull/1769)
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

### Changed
- Replace ezyang/htmlpurifier (LGPL2.1) with voku/anti-xss (MIT)
