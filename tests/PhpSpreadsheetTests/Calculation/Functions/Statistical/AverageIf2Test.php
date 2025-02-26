<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class AverageIf2Test extends TestCase
{
    public function testAVERAGEIF(): void
    {
        $table = [
            ['n', 1],
            ['', 2],
            ['y', 3],
            ['', 4],
            ['n', 5],
            ['n', 6],
            ['n', 7],
            ['', 8],
            [null, 9],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($table, null, 'A1', true);
        $sheet->getCell('C1')->setValue('=AVERAGEIF(A:A,"",B:B)');
        $sheet->getCell('C2')->setValue('=AVERAGEIF(A:A,,B:B)');
        $sheet->getCell('C3')->setValue('=AVERAGEIF(A:A,"n",B:B)');
        $sheet->getCell('C4')->setValue('=AVERAGEIF(A:A,"y",B:B)');
        $sheet->getCell('C5')->setValue('=AVERAGEIF(A:A,"x",B:B)');

        self::assertEqualsWithDelta(5.75, $sheet->getCell('C1')->getCalculatedValue(), 1E-12);
        self::assertEquals('#DIV/0!', $sheet->getCell('C2')->getCalculatedValue());
        self::assertEqualsWithDelta(4.75, $sheet->getCell('C3')->getCalculatedValue(), 1E-12);
        self::assertEqualsWithDelta(3, $sheet->getCell('C4')->getCalculatedValue(), 1E-12);
        self::assertEquals('#DIV/0!', $sheet->getCell('C5')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
