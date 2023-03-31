<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Theme as SpreadsheetTheme;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ThemeColorsTest extends AbstractFunctional
{
    public function testOffice2013Theme(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getTheme()->setThemeColorName(SpreadsheetTheme::COLOR_SCHEME_2013_PLUS_NAME);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame('Office 2013+', $reloadedSpreadsheet->getTheme()->getThemeColorName());
        self::assertSame('FFC000', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testOffice2007Theme(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getTheme()->setThemeColorName(SpreadsheetTheme::COLOR_SCHEME_2007_2010_NAME);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame('Office 2007-2010', $reloadedSpreadsheet->getTheme()->getThemeColorName());
        self::assertSame('8064A2', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testDefaultTheme(): void
    {
        $spreadsheet = new Spreadsheet();
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame('Office', $reloadedSpreadsheet->getTheme()->getThemeColorName());
        self::assertSame('8064A2', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testGalleryTheme(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load('tests/data/Writer/XLSX/gallerytheme.xlsx');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        self::assertSame('Gallery', $reloadedSpreadsheet->getTheme()->getThemeColorName());
        self::assertSame('795FAF', $reloadedSpreadsheet->getTheme()->getThemeColors()['accent4']);
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
