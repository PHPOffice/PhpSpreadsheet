<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class HyperlinkMutationTest extends TestCase
{
    public function testSaveLeavesHyperlinksAlone(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'plain');
        $sheet->setCellValue('A2', 'also plain');
        $sheet->setCellValue('A3', 'link');
        $sheet->getCell('A3')->getHyperlink()->setUrl('https://example.org');

        $writer = new OdsWriter($spreadsheet);
        $data = (new OdsWriter\Content($writer))->write();
        self::assertStringContainsString('xlink:href="https://example.org"', $data);
        self::assertSame(['A3'], array_keys($sheet->getHyperlinkCollection()));

        // The Xlsx writer, saving the same spreadsheet next, writes the hyperlink of A3 and no other
        $file = File::temporaryFilename();
        (new XlsxWriter($spreadsheet))->save($file);
        $zip = new ZipArchive();
        self::assertTrue($zip->open($file));
        $sheetXml = (string) $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        unlink($file);
        self::assertSame(1, preg_match_all('/<hyperlink /', $sheetXml));
        self::assertStringContainsString('<hyperlink ref="A3"', $sheetXml);
        $spreadsheet->disconnectWorksheets();
    }
}
