<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class WorkbookViewAttributesTest extends AbstractFunctional
{
    public function providerFormats()
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
     *
     * @param string $format
     */
    public function testPreserveWorkbookViewAttributes($format)
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
        $this->assertTrue($workbook->getShowHorizontalScroll());
        $this->assertTrue($workbook->getShowVerticalScroll());
        $this->assertTrue($workbook->getShowSheetTabs());
        $this->assertTrue($workbook->getAutoFilterDateGrouping());
        $this->assertFalse($workbook->getMinimized());
        $this->assertSame(0, $workbook->getFirstSheetIndex());
        $this->assertSame(600, $workbook->getTabRatio());
        $this->assertSame(Spreadsheet::VISIBILITY_VISIBLE, $workbook->getVisibility());

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
        $this->assertFalse($workbook->getShowHorizontalScroll());
        $this->assertFalse($workbook->getShowVerticalScroll());
        $this->assertFalse($workbook->getShowSheetTabs());
        $this->assertFalse($workbook->getAutoFilterDateGrouping());
        $this->assertTrue($workbook->getMinimized());
        $this->assertSame(1, $workbook->getFirstSheetIndex());
        $this->assertSame(700, $workbook->getTabRatio());
        $this->assertSame(Spreadsheet::VISIBILITY_HIDDEN, $workbook->getVisibility());

        $workbook2 = $this->writeAndReload($workbook, $format);

        // Check that the read spreadsheet has the right bookview attributes
        $this->assertFalse($workbook2->getShowHorizontalScroll());
        $this->assertFalse($workbook2->getShowVerticalScroll());
        $this->assertFalse($workbook2->getShowSheetTabs());
        $this->assertFalse($workbook2->getAutoFilterDateGrouping());
        $this->assertTrue($workbook2->getMinimized());
        $this->assertSame(1, $workbook2->getFirstSheetIndex());
        $this->assertSame(700, $workbook2->getTabRatio());
        $this->assertSame(Spreadsheet::VISIBILITY_HIDDEN, $workbook2->getVisibility());
    }
}
