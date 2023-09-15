<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class XlsBugPr3734Test extends TestCase
{
    /**
     * Test XLS file including data with missing fonts?
     */
    public function testXlsFileWithNoFontIndex(): void
    {
        $fileName = dirname(__DIR__, 3) . '/data/Reader/XLS/bug-pr-3734.xls';
        $file = new SplFileInfo($fileName);

        IOFactory::load($file);

        // If no error occurs on load, test is passed!
        self::assertTrue(true);
    }
}
