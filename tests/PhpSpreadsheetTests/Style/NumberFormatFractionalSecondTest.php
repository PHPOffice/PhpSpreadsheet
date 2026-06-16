<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style;

use DateTime;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\DateFormatter;
use PHPUnit\Framework\TestCase;

class NumberFormatFractionalSecondTest extends TestCase
{
    public function testRounded(): void
    {
        $dt = new DateTime('2025-01-02', new DateTimeZone('UTC'));
        $dt->setTime(10, 59, 58, 999999);
        $excelTime = Date::PHPToExcel($dt);
        self::assertNotFalse($excelTime);
        $result1 = DateFormatter::format($excelTime, 'hh:mm:ss.000');
        self::assertSame('10:59:59.000', $result1);
        $result2 = DateFormatter::format($excelTime, 'hh:mm:ss.00');
        self::assertSame('10:59:59.00', $result2);
        $result3 = DateFormatter::format($excelTime, 'hh:mm:ss.0');
        self::assertSame('10:59:59.0', $result3);
    }

    public function testNotRounded(): void
    {
        $dt = new DateTime('2025-01-02', new DateTimeZone('UTC'));
        $dt->setTime(10, 59, 58, 234111);
        $excelTime = Date::PHPToExcel($dt);
        self::assertNotFalse($excelTime);
        $result1 = DateFormatter::format($excelTime, 'hh:mm:ss.000');
        self::assertSame('10:59:58.234', $result1);
        $result2 = DateFormatter::format($excelTime, 'hh:mm:ss.00');
        self::assertSame('10:59:58.23', $result2);
        $result3 = DateFormatter::format($excelTime, 'hh:mm:ss.0');
        self::assertSame('10:59:58.2', $result3);
    }
}
