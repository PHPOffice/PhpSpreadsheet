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

        // Set the workbook bookbiews to non-default values
        foreach (self::$bookViewAttributes as $attr => $value) {
            $workbook->setWorkbookViewAttribute($attr, $value);
        }

        Settings::setLibXmlLoaderOptions(null); // reset to default options

        $targetFilename = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($workbook);
        $writer->save($targetFilename);

        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $workbook2 = $reader->load($targetFilename);

            foreach (self::$bookViewAttributes as $attr => $value) {
                $this->assertEquals($value, $workbook2->getWorkbookViewAttribute($attr));
            }
        } finally {
            unlink($targetFilename);
        }
    }
}
