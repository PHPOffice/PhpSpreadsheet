# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.0.0] - 2017-12-25

### Added

- Support to write merged cells in ODS format [#287](https://github.com/PHPOffice/PhpSpreadsheet/issues/287)
- Able to set the `topLeftCell` in freeze panes [#261](https://github.com/PHPOffice/PhpSpreadsheet/pull/261) 
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

- Start following [SemVer](http://semver.org) properly.

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
