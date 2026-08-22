<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Issue4800Test extends TestCase
{
    private static string $filename = 'tests/data/Reader/XLSX/issue.4800.xlsx';

    public function testHeaderFooterImageDimensions(): void
    {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load(self::$filename);

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $images = $sheet->getHeaderFooter()->getImages();
            self::assertCount(1, $images);

            $image = reset($images);
            self::assertSame(100, $image->getWidth());
            self::assertSame(100, $image->getHeight());
        }

        $spreadsheet->disconnectWorksheets();
    }
}
