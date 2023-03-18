<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\DateParts;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\TimeParts;
use PHPUnit\Framework\TestCase;

class TodayTest extends TestCase
{
    /**
     * @param mixed $result
     */
    private function assertions(DateTimeImmutable $dtStart, $result): void
    {
        self::assertEquals($dtStart->format('Y'), DateParts::year($result));
        self::assertEquals($dtStart->format('m'), DateParts::month($result));
        self::assertEquals($dtStart->format('d'), DateParts::day($result));
        self::assertEquals(0, TimeParts::hour($result));
        self::assertEquals(0, TimeParts::minute($result));
        self::assertEquals(0, TimeParts::second($result));
    }

    public function testDirectCallToToday(): void
    {
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same second.
        do {
            $dtStart = new DateTimeImmutable();
            $startSecond = $dtStart->format('s');
            $result = Current::today();
            $endSecond = (new DateTimeImmutable('now'))->format('s');
        } while ($startSecond !== $endSecond);

        $this->assertions($dtStart, $result);
    }

    public function testTodayAsFormula(): void
    {
        $calculation = Calculation::getInstance();
        $formula = '=TODAY()';

        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same second.
        do {
            $dtStart = new DateTimeImmutable();
            $startSecond = $dtStart->format('s');
            $result = $calculation->_calculateFormulaValue($formula);
            $endSecond = (new DateTimeImmutable('now'))->format('s');
        } while ($startSecond !== $endSecond);

        $this->assertions($dtStart, $result);
    }
}
