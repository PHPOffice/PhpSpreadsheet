# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added

### Changed

### Fixed


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

For a comprehensive list of all class changes, and a semi-automated migration path, read the [migration guide](./docs/Migration-from-PHPExcel.md).

- Dropped `PHPExcel_Calculation_Functions::VERSION()`. Composer or git should be used to know the version.
- Dropped `PHPExcel_Settings::setPdfRenderer()` and `PHPExcel_Settings::setPdfRenderer()`. Composer should be used to autoload PDF libs.
- Dropped support for HHVM

## Previous versions of PHPExcel

The changelog for the project when it was called PHPExcel is [still available](./CHANGELOG.PHPExcel.md).
