<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Settings;
use PHPUnit\Framework\TestCase;

class HiddenTabsTest extends TestCase
{
    /**
     * Test that the worksheet tabs remain hidden when reading and writing a XLSX document
     * with hidden worksheets tabs.
     */
    public function testUpdateWithHiddenTabs()
    {
        $sourceFilename = __DIR__ . '/../../../data/Writer/XLSX/hidden_tabs.xlsx';
        Settings::setLibXmlLoaderOptions(null); // reset to default options
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $excel = $reader->load($sourceFilename);

        $targetFilename = tempnam(sys_get_temp_dir(), 'tst');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        $writer->save($targetFilename);

        try {
            $excel2 = $reader->load($targetFilename);
            $this->assertEquals('0', $excel2->getWorkbookViewAttribute('showSheetTabs'));
        } finally {
            @unlink($targetFilename);
        }
    }
}
