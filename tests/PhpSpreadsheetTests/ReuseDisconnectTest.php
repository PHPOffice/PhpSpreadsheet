<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ReuseDisconnectTest extends TestCase
{
    public function testDisconnectThenReuse(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->disconnectWorksheets();
        self::assertSame(0, $spreadsheet->getSheetCount());
        self::assertSame(-1, $spreadsheet->getActiveSheetIndex());

        $sheet = $spreadsheet->createSheet(0);
        self::assertSame(1, $spreadsheet->getSheetCount());
        self::assertSame(0, $spreadsheet->getActiveSheetIndex());

        // Confirm calculation engine and style supervisor are instact
        $sheet->getCell('A1')->setValue('=1+2');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        self::assertSame(3, $sheet->getCell('A1')->getCalculatedValue());
        self::assertTrue(
            $sheet->getStyle('A1')->getFont()->getBold()
        );

        $spreadsheet->disconnectWorksheets();
    }
}
