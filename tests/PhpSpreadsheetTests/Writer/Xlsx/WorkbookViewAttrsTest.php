<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class WorkbookViewAttrsTest extends TestCase
{
    /**
     * Copy of PhpOffice\PhpSpreadsheet\Writer\Xlsx\Workbook::$bookViewAttributes
     * except that the values are purposefully set to values different from the
     * default values.
     *
     * @var array
     */
    private static $bookViewAttributes = [
        'autoFilterDateGrouping' => '0',
        'firstSheet' => '1',
        'minimized' => '1',
        'showHorizontalScroll' => '0',
        'showSheetTabs' => '0',
        'showVerticalScroll' => '0',
        'tabRatio' => '601',
        'visibility' => 'hidden',
    ];

    /**
     * Test that workbook bookview attributes such as 'showSheetTabs',
     * (the attribute controlling worksheet tabs visibility,)
     * are preserved when xlsx documents are read and written.
     *
     * @see https://github.com/PHPOffice/PhpSpreadsheet/issues/523
     */
    public function testPreserveWorkbookViewAttributes()
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

        // Write then read the spreadsheet
        Settings::setLibXmlLoaderOptions(null); // reset to default options

        $targetFilename = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($workbook);
        $writer->save($targetFilename);

        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $workbook2 = $reader->load($targetFilename);

            // Check that the read spreadsheet has the right bookview attributes
            $this->assertFalse($workbook2->getShowHorizontalScroll());
            $this->assertFalse($workbook2->getShowVerticalScroll());
            $this->assertFalse($workbook2->getShowSheetTabs());
            $this->assertFalse($workbook2->getAutoFilterDateGrouping());
            $this->assertTrue($workbook2->getMinimized());
            $this->assertSame(1, $workbook2->getFirstSheetIndex());
            $this->assertSame(700, $workbook2->getTabRatio());
            $this->assertSame(Spreadsheet::VISIBILITY_HIDDEN, $workbook2->getVisibility());
        } finally {
            unlink($targetFilename);
        }
    }
}
