<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class DrawingsTest extends AbstractFunctional
{
    /**
     * @var int
     */
    protected $prevValue;

    protected function setUp(): void
    {
        $this->prevValue = Settings::getLibXmlLoaderOptions();

        // Disable validating XML with the DTD
        Settings::setLibXmlLoaderOptions($this->prevValue & ~LIBXML_DTDVALID & ~LIBXML_DTDATTR & ~LIBXML_DTDLOAD);
    }

    protected function tearDown(): void
    {
        Settings::setLibXmlLoaderOptions($this->prevValue);
    }

    /**
     * Test save and load XLSX file with drawing on 2nd worksheet.
     */
    public function testSaveLoadWithDrawingOn2ndWorksheet(): void
    {
        // Read spreadsheet from file
        $inputFilename = 'tests/data/Writer/XLSX/drawing_on_2nd_page.xlsx';
        $reader = new Xlsx();
        $spreadsheet = $reader->load($inputFilename);

        // Save spreadsheet to file and read it back
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');

        // Fake assert. The only thing we need is to ensure the file is loaded without exception
        self::assertNotNull($reloadedSpreadsheet);
    }
}
