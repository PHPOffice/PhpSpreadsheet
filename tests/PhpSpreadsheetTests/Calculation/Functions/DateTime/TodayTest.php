<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeImmutable;

class TodayTest extends AllSetupTeardown
{
    public function testToday(): void
    {
        $sheet = $this->getSheet();
        $row = 0;
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same second.
        do {
            ++$row;
            $dtStart = new DateTimeImmutable();
            $startSecond = $dtStart->format('s');
            $sheet->setCellValue("A$row", '=TODAY()');
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
        self::assertSame((int) $dtStart->format('Y'), $sheet->getCell("B$row")->getCalculatedValue());
        self::assertSame((int) $dtStart->format('m'), $sheet->getCell("C$row")->getCalculatedValue());
        self::assertSame((int) $dtStart->format('d'), $sheet->getCell("D$row")->getCalculatedValue());
        self::assertSame(0, $sheet->getCell("E$row")->getCalculatedValue());
        self::assertSame(0, $sheet->getCell("F$row")->getCalculatedValue());
        self::assertSame(0, $sheet->getCell("G$row")->getCalculatedValue());
    }
}
