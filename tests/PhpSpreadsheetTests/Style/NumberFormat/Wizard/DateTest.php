<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    /**
     * @dataProvider providerDate
     *
     * @param null|string|string[] $separators
     * @param string[] $formatBlocks
     */
    public function testDate(string $expectedResult, $separators = null, array $formatBlocks = []): void
    {
        $wizard = new Date($separators, ...$formatBlocks);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerDate(): array
    {
        return [
            ['yyyy-mm-dd', Date::SEPARATOR_DASH, [Date::YEAR_FULL, Date::MONTH_NUMBER_LONG, Date::DAY_NUMBER_LONG]],
            ['mm/dd/yyyy', Date::SEPARATOR_SLASH, [Date::MONTH_NUMBER_LONG, Date::DAY_NUMBER_LONG, Date::YEAR_FULL]],
            ['dd.mm.yyyy', Date::SEPARATOR_DOT, [Date::DAY_NUMBER_LONG, Date::MONTH_NUMBER_LONG, Date::YEAR_FULL]],
            ['dd-mmm-yyyy', Date::SEPARATOR_DASH, [Date::DAY_NUMBER_LONG, Date::MONTH_NAME_SHORT, Date::YEAR_FULL]],
            ['dd-mmm yyyy', [Date::SEPARATOR_DASH, Date::SEPARATOR_SPACE], [Date::DAY_NUMBER_LONG, Date::MONTH_NAME_SHORT, Date::YEAR_FULL]],
            ['dddd dd.mm.yyyy', [Date::SEPARATOR_SPACE, Date::SEPARATOR_DOT], [Date::WEEKDAY_NAME_LONG, Date::DAY_NUMBER_LONG, Date::MONTH_NUMBER_LONG, Date::YEAR_FULL]],
            ['dd-mm "in the year" yyyy', [Date::SEPARATOR_DASH, Date::SEPARATOR_SPACE], [Date::DAY_NUMBER_LONG, Date::MONTH_NUMBER_LONG, 'in the year', Date::YEAR_FULL]],
            ["yyyy-mm-dd\u{a0}(ddd)", [Date::SEPARATOR_DASH, Date::SEPARATOR_DASH, Date::SEPARATOR_SPACE_NONBREAKING, null], [Date::YEAR_FULL, Date::MONTH_NUMBER_LONG, Date::DAY_NUMBER_LONG, '(', Date::WEEKDAY_NAME_SHORT, ')']],
            ['yyyy-mm-dd', null, [Date::YEAR_FULL, Date::MONTH_NUMBER_LONG, Date::DAY_NUMBER_LONG]],
            ['yyyy-mm-dd'],
        ];
    }
}
