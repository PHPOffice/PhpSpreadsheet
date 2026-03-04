<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class PageSetupTest extends AbstractFunctional
{
    private const MARGIN_PRECISION = 0.00000001;

    private const MARGIN_UNIT_CONVERSION = 2.54; // Inches to cm

    public function testPageSetup(): void
    {
        $filename = 'tests/data/Reader/XLS/PageSetup.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $assertions = $this->pageSetupAssertions();

        $sheetCount = 0;
        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            ++$sheetCount;
            if (!array_key_exists($worksheet->getTitle(), $assertions)) {
                self::fail('Unexpected worksheet ' . $worksheet->getTitle());
            }

            $sheetAssertions = $assertions[$worksheet->getTitle()];
            foreach ($sheetAssertions as $test => $expectedResult) {
                $testMethodName = 'get' . ucfirst($test);
                $actualResult = $worksheet->getPageSetup()->$testMethodName();
                self::assertSame(
                    $expectedResult,
                    $actualResult,
                    "Failed assertion for Worksheet '{$worksheet->getTitle()}' {$test}"
                );
            }
        }
        self::assertCount($sheetCount, $assertions);
        $spreadsheet->disconnectWorksheets();
    }

    public function testPageMargins(): void
    {
        $filename = 'tests/data/Reader/XLS/PageSetup.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $assertions = $this->pageMarginAssertions();

        $sheetCount = 0;
        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            ++$sheetCount;
            if (!array_key_exists($worksheet->getTitle(), $assertions)) {
                self::fail('Unexpected worksheet ' . $worksheet->getTitle());
            }

            $sheetAssertions = $assertions[$worksheet->getTitle()];
            foreach ($sheetAssertions as $test => $expectedResult) {
                $testMethodName = 'get' . ucfirst($test);
                $actualResult = $worksheet->getPageMargins()->$testMethodName();
                self::assertEqualsWithDelta(
                    $expectedResult,
                    $actualResult,
                    self::MARGIN_PRECISION,
                    "Failed assertion for Worksheet '{$worksheet->getTitle()}' {$test} margin"
                );
            }
        }
        self::assertCount($sheetCount, $assertions);
        $spreadsheet->disconnectWorksheets();
    }

    /** @return array<array{orientation: string, scale: int, horizontalCentered: bool, verticalCentered: bool, pageOrder: string}> */
    private function pageSetupAssertions(): array
    {
        return [
            'Sheet1' => [
                'orientation' => PageSetup::ORIENTATION_PORTRAIT,
                'scale' => 75,
                'horizontalCentered' => true,
                'verticalCentered' => false,
                'pageOrder' => PageSetup::PAGEORDER_DOWN_THEN_OVER,
            ],
            'Sheet2' => [
                'orientation' => PageSetup::ORIENTATION_LANDSCAPE,
                'scale' => 100,
                'horizontalCentered' => false,
                'verticalCentered' => true,
                'pageOrder' => PageSetup::PAGEORDER_OVER_THEN_DOWN,
            ],
            'Sheet3' => [
                'orientation' => PageSetup::ORIENTATION_PORTRAIT,
                'scale' => 90,
                'horizontalCentered' => true,
                'verticalCentered' => true,
                'pageOrder' => PageSetup::PAGEORDER_DOWN_THEN_OVER,
            ],
            'Sheet4' => [
                // Default Settings
                'orientation' => PageSetup::ORIENTATION_DEFAULT,
                'scale' => 100,
                'horizontalCentered' => false,
                'verticalCentered' => false,
                'pageOrder' => PageSetup::PAGEORDER_DOWN_THEN_OVER,
            ],
        ];
    }

    /** @return array<array{top: float, header: float, left: float, right: float, bottom: float, footer: float}> */
    private function pageMarginAssertions(): array
    {
        return [
            'Sheet1' => [
                // Here the values are in cm, so we convert to inches for comparison with internal uom
                'top' => 2.4 / self::MARGIN_UNIT_CONVERSION,
                'header' => 0.8 / self::MARGIN_UNIT_CONVERSION,
                'left' => 1.3 / self::MARGIN_UNIT_CONVERSION,
                'right' => 1.3 / self::MARGIN_UNIT_CONVERSION,
                'bottom' => 1.9 / self::MARGIN_UNIT_CONVERSION,
                'footer' => 0.8 / self::MARGIN_UNIT_CONVERSION,
            ],
            'Sheet2' => [
                // Here the values are in cm, so we convert to inches for comparison with internal uom
                'top' => 1.9 / self::MARGIN_UNIT_CONVERSION,
                'header' => 0.8 / self::MARGIN_UNIT_CONVERSION,
                'left' => 1.8 / self::MARGIN_UNIT_CONVERSION,
                'right' => 1.8 / self::MARGIN_UNIT_CONVERSION,
                'bottom' => 1.9 / self::MARGIN_UNIT_CONVERSION,
                'footer' => 0.8 / self::MARGIN_UNIT_CONVERSION,
            ],
            'Sheet3' => [
                // Here the values are in cm, so we convert to inches for comparison with internal uom
                'top' => 2.4 / self::MARGIN_UNIT_CONVERSION,
                'header' => 1.3 / self::MARGIN_UNIT_CONVERSION,
                'left' => 1.8 / self::MARGIN_UNIT_CONVERSION,
                'right' => 1.8 / self::MARGIN_UNIT_CONVERSION,
                'bottom' => 2.4 / self::MARGIN_UNIT_CONVERSION,
                'footer' => 1.3 / self::MARGIN_UNIT_CONVERSION,
            ],
            'Sheet4' => [
                // Default Settings (already in inches for comparison)
                'top' => 0.75,
                'header' => 0.3,
                'left' => 0.7,
                'right' => 0.7,
                'bottom' => 0.75,
                'footer' => 0.3,
            ],
        ];
    }

    public function testRepeats2(): void
    {
        $spreadsheet = new Spreadsheet();

        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet1');
        $setup = $sheet1->getPageSetup();
        $setup->setRowsToRepeatAtTop([1, 1]);
        $data = 0;
        for ($row = 2; $row < 100; ++$row) {
            $colStr = 'A';
            $sheet1->setCellValue("A$row", 'LEFT');
            for ($col = 1; $col < 100; ++$col) {
                StringHelper::stringIncrement($colStr);
                if ($row === 2) {
                    $sheet1->setCellValue("{$colStr}1", 'TOP');
                }
                ++$data;
                $sheet1->setCellValue("$colStr$row", $data);
            }
        }
        $cells = $sheet1->toArray();

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet2');
        $setup = $sheet2->getPageSetup();
        $setup->setColumnsToRepeatAtLeft(['A', 'A']);
        $sheet2->fromArray($cells);

        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Sheet3');
        $setup = $sheet3->getPageSetup();
        $setup->setRowsToRepeatAtTop([1, 1]);
        $setup->setColumnsToRepeatAtLeft(['A', 'A']);
        $sheet3->fromArray($cells);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();

        $rsheet1 = $reloadedSpreadsheet->getSheetByNameOrThrow('Sheet1');
        $rsetup1 = $rsheet1->getPageSetup();
        self::assertSame([1, 1], $rsetup1->getRowsToRepeatAtTop());
        self::assertSame(['', ''], $rsetup1->getColumnsToRepeatAtLeft());

        $rsheet2 = $reloadedSpreadsheet->getSheetByNameOrThrow('Sheet2');
        $rsetup2 = $rsheet2->getPageSetup();
        self::assertSame([0, 0], $rsetup2->getRowsToRepeatAtTop());
        self::assertSame(['A', 'A'], $rsetup2->getColumnsToRepeatAtLeft());

        $rsheet3 = $reloadedSpreadsheet->getSheetByNameOrThrow('Sheet3');
        $rsetup3 = $rsheet3->getPageSetup();
        self::assertSame([1, 1], $rsetup3->getRowsToRepeatAtTop());
        self::assertSame(['A', 'A'], $rsetup3->getColumnsToRepeatAtLeft());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
