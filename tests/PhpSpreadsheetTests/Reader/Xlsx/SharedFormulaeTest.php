<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class SharedFormulaeTest extends TestCase
{
    public function testSharedFormulae(): void
    {
        // Boolean functions were not handled correctly.
        $filename = 'tests/data/Reader/XLSX/sharedformulae.xlsx';
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $expected = [
            [1, '=A1+1', '=A1>3', '="x"&A1'],
            [2, '=A2+1', '=A2>3', '="x"&A2'],
            [3, '=A3+1', '=A3>3', '="x"&A3'],
            [4, '=A4+1', '=A4>3', '="x"&A4'],
            [5, '=A5+1', '=A5>3', '="x"&A5'],
        ];
        self::assertSame($expected, $sheet->toArray(null, false, false));
        $expected = [
            [1, 2, false, 'x1'],
            [2, 3, false, 'x2'],
            [3, 4, false, 'x3'],
            [4, 5, true, 'x4'],
            [5, 6, true, 'x5'],
        ];
        self::assertSame($expected, $sheet->toArray(null, true, false));
        $spreadsheet->disconnectWorksheets();
    }
}
