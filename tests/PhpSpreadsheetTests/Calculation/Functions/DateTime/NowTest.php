<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeImmutable;

class NowTest extends AllSetupTeardown
{
    public function testNow(): void
    {
        $sheet = $this->getSheet();
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same second.
        do {
            $dtStart = new DateTimeImmutable();
            $startSecond = $dtStart->format('s');
            $sheet->setCellValue('A1', '=NOW()');
            $dtEnd = new DateTimeImmutable();
            $endSecond = $dtEnd->format('s');
        } while ($startSecond !== $endSecond);
        $sheet->setCellValue('B1', '=YEAR(A1)');
        $sheet->setCellValue('C1', '=MONTH(A1)');
        $sheet->setCellValue('D1', '=DAY(A1)');
        $sheet->setCellValue('E1', '=HOUR(A1)');
        $sheet->setCellValue('F1', '=MINUTE(A1)');
        $sheet->setCellValue('G1', '=SECOND(A1)');
        self::assertEquals($dtStart->format('Y'), $sheet->getCell('B1')->getCalculatedValue());
        self::assertEquals($dtStart->format('m'), $sheet->getCell('C1')->getCalculatedValue());
        self::assertEquals($dtStart->format('d'), $sheet->getCell('D1')->getCalculatedValue());
        self::assertEquals($dtStart->format('H'), $sheet->getCell('E1')->getCalculatedValue());
        self::assertEquals($dtStart->format('i'), $sheet->getCell('F1')->getCalculatedValue());
        self::assertEquals($dtStart->format('s'), $sheet->getCell('G1')->getCalculatedValue());
    }
}
