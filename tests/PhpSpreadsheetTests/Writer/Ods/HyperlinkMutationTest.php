<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

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

        // An empty hyperlink on every string cell made the Xlsx writer throw "Invalid parameters passed."
        $file = File::temporaryFilename();
        (new XlsxWriter($spreadsheet))->save($file);
        self::assertFileExists($file);
        unlink($file);
        $spreadsheet->disconnectWorksheets();
    }
}
