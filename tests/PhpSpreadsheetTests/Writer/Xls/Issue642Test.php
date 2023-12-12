<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xls;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PHPUnit\Framework\TestCase;

class Issue642Test extends TestCase
{
    public function testCharOutsideBMP(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $stringUtf8 = "Hello\u{1f600}goodbye";
        self::assertSame(13, mb_strlen($stringUtf8));
        $stringUtf16 = (string) iconv('UTF-8', 'UTF-16LE', $stringUtf8);
        self::assertSame(28, strlen($stringUtf16)); // each character requires 2 bytes except for non-BMP which requires 4
        $sheet->getCell('A1')->setValue($stringUtf8);
        $outputFilename = File::temporaryFilename();
        $writer = new Xls($spreadsheet);
        $writer->save($outputFilename);
        $spreadsheet->disconnectWorksheets();
        $contents = (string) file_get_contents($outputFilename);
        unlink($outputFilename);
        $expected = "\x00\x0e\x00\x01" . $stringUtf16; // length is 14 (0e), not 13
        self::assertStringContainsString($expected, $contents);
        $unexpected = "\x00\x0d\x00\x01" . $stringUtf16; // length is 14 (0e), not 13
        self::assertStringNotContainsString($unexpected, $contents);
    }
}
