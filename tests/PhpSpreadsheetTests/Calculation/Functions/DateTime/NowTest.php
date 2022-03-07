<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeImmutable;

class NowTest extends AllSetupTeardown
{
    public function testNow(): void
    {
        $sheet = $this->getSheet();
        $row = 0;
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same second.
        do {
            ++$row;
            $dtStart = new DateTimeImmutable();
            $startSecond = $dtStart->format('s');
            $sheet->setCellValue("A$row", '=NOW()');
            // cache result for later assertions
            $sheet->getCell("A$row")->getCalculatedValue();
            $dtEnd = new DateTimeImmutable();
            $endSecond = $dtEnd->format('s');
        } while ($startSecond !== $endSecond);
        $sheet->setCellValue("B$row", "=YEAR(A$row)");
        $sheet->setCellValue("C$row", "=MONTH(A$row)");
        $sheet->setCellValue("D$row", "=DAY(A$row)");
        $sheet->setCellValue("E$row", "=HOUR(A$row)");
        $sheet->setCellValue("F$row", "=MINUTE(A$row)");
        $sheet->setCellValue("G$row", "=SECOND(A$row)");
        self::assertEquals($dtStart->format('Y'), $sheet->getCell("B$row")->getCalculatedValue());
        self::assertEquals($dtStart->format('m'), $sheet->getCell("C$row")->getCalculatedValue());
        self::assertEquals($dtStart->format('d'), $sheet->getCell("D$row")->getCalculatedValue());
        self::assertEquals($dtStart->format('H'), $sheet->getCell("E$row")->getCalculatedValue());
        self::assertEquals($dtStart->format('i'), $sheet->getCell("F$row")->getCalculatedValue());
        self::assertEquals($dtStart->format('s'), $sheet->getCell("G$row")->getCalculatedValue());
    }
}
