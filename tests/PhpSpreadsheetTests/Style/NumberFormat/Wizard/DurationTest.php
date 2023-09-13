<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Duration;
use PHPUnit\Framework\TestCase;

class DurationTest extends TestCase
{
    /**
     * @dataProvider providerTime
     *
     * @param null|string|string[] $separators
     * @param string[] $formatBlocks
     */
    public function testTime(string $expectedResult, string|array|null $separators = null, array $formatBlocks = []): void
    {
        $wizard = new Duration($separators, ...$formatBlocks);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerTime(): array
    {
        return [
            ['[h]:mm:ss', Duration::SEPARATOR_COLON, [Duration::HOURS_DURATION, Duration::MINUTES_LONG, Duration::SECONDS_LONG]],
            ['[h]:mm', Duration::SEPARATOR_COLON, [Duration::HOURS_DURATION, Duration::MINUTES_LONG]],
            ['[m]:ss', Duration::SEPARATOR_COLON, [Duration::MINUTES_DURATION, Duration::SECONDS_DURATION]],
            ['[h]:mm:ss', Duration::SEPARATOR_COLON, [Duration::HOURS_LONG, Duration::MINUTES_LONG, Duration::SECONDS_LONG]],
            ['[h]:mm', Duration::SEPARATOR_COLON, [Duration::HOURS_LONG, Duration::MINUTES_LONG]],
            ["d\u{a0}h:mm", [Duration::SEPARATOR_SPACE_NONBREAKING, Duration::SEPARATOR_COLON], [Duration::DAYS_DURATION, Duration::HOURS_DURATION, Duration::MINUTES_LONG]],
            ['[h]:mm:ss', null, [Duration::HOURS_DURATION, Duration::MINUTES_LONG, Duration::SECONDS_LONG]],
            ['[h]:mm:ss'],
        ];
    }
}
