<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class WorkbookViewAttributesTest extends AbstractFunctional
{
    public static function providerFormats(): array
    {
        return [
            ['Xlsx'],
        ];
    }

    /**
     * Test that workbook bookview attributes such as 'showSheetTabs',
     * (the attribute controlling worksheet tabs visibility,)
     * are preserved when xlsx documents are read and written.
     *
     * @see https://github.com/PHPOffice/PhpSpreadsheet/issues/523
     *
     * @dataProvider providerFormats
     */
    public function testPreserveWorkbookViewAttributes(string $format): void
    {
        // Create a dummy workbook with two worksheets
        $workbook = new Spreadsheet();
        $worksheet1 = $workbook->getActiveSheet();
        $worksheet1->setTitle('Tweedledee');
        $worksheet1->setCellValue('A1', 1);
        $worksheet2 = $workbook->createSheet();
        $worksheet2->setTitle('Tweeldedum');
        $worksheet2->setCellValue('A1', 2);

        // Check that the bookview attributes return default values
        self::assertTrue($workbook->getShowHorizontalScroll());
        self::assertTrue($workbook->getShowVerticalScroll());
        self::assertTrue($workbook->getShowSheetTabs());
        self::assertTrue($workbook->getAutoFilterDateGrouping());
        self::assertFalse($workbook->getMinimized());
        self::assertSame(0, $workbook->getFirstSheetIndex());
        self::assertSame(600, $workbook->getTabRatio());
        self::assertSame(Spreadsheet::VISIBILITY_VISIBLE, $workbook->getVisibility());

        // Set the bookview attributes to non-default values
        $workbook->setShowHorizontalScroll(false);
        $workbook->setShowVerticalScroll(false);
        $workbook->setShowSheetTabs(false);
        $workbook->setAutoFilterDateGrouping(false);
        $workbook->setMinimized(true);
        $workbook->setFirstSheetIndex(1);
        $workbook->setTabRatio(700);
        $workbook->setVisibility(Spreadsheet::VISIBILITY_HIDDEN);

        // Check that bookview attributes were set properly
        self::assertFalse($workbook->getShowHorizontalScroll());
        self::assertFalse($workbook->getShowVerticalScroll());
        self::assertFalse($workbook->getShowSheetTabs());
        self::assertFalse($workbook->getAutoFilterDateGrouping());
        self::assertTrue($workbook->getMinimized());
        self::assertSame(1, $workbook->getFirstSheetIndex());
        self::assertSame(700, $workbook->getTabRatio());
        self::assertSame(Spreadsheet::VISIBILITY_HIDDEN, $workbook->getVisibility());

        $workbook2 = $this->writeAndReload($workbook, $format);

        // Check that the read spreadsheet has the right bookview attributes
        self::assertFalse($workbook2->getShowHorizontalScroll());
        self::assertFalse($workbook2->getShowVerticalScroll());
        self::assertFalse($workbook2->getShowSheetTabs());
        self::assertFalse($workbook2->getAutoFilterDateGrouping());
        self::assertTrue($workbook2->getMinimized());
        self::assertSame(1, $workbook2->getFirstSheetIndex());
        self::assertSame(700, $workbook2->getTabRatio());
        self::assertSame(Spreadsheet::VISIBILITY_HIDDEN, $workbook2->getVisibility());
    }
}
