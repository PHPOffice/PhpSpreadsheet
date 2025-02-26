<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PHPUnit\Framework\TestCase;

class XlsxRootZipFilesTest extends TestCase
{
    private static string $testbook = 'tests/data/Reader/XLSX/rootZipFiles.xlsx';

    public function testXlsxRootZipFiles(): void
    {
        $filename = self::$testbook;
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        /** @var RichText */
        $value = $sheet->getCell('A1')->getValue();
        self::assertSame('TEST CELL', $value->getPlainText());
    }
}
