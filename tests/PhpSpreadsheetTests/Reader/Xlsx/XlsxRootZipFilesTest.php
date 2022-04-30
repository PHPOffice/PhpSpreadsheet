<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PHPUnit\Framework\TestCase;

class XlsxRootZipFilesTest extends TestCase
{
    /**
     * @var string
     */
    private static $testbook = 'tests/data/Reader/XLSX/rootZipFiles.xlsx';

    public function testXlsxRootZipFiles(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $value = $sheet->getCell('A1')->getValue();
        self::assertSame('TEST CELL', $value->getPlainText());
    }
}
