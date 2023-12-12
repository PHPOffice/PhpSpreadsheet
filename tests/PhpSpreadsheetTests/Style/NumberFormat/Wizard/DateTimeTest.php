<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\DateTime;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Time;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    /**
     * @dataProvider providerDateTime
     *
     * @param null|string|string[] $separators
     * @param string[] $formatBlocks
     */
    public function testDateTime(string $expectedResult, string|null|array $separators, array $formatBlocks): void
    {
        $wizard = new DateTime($separators, ...$formatBlocks);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerDateTime(): array
    {
        return [
            ['yyyy-mm-dd "at" hh:mm:ss', ' ', [new Date('-', 'yyyy', 'mm', 'dd'), 'at', new Time(':', 'hh', 'mm', 'ss')]],
            ['dddd \à hh "heures"', ' ', [new Date(null, 'dddd'), 'à', new Time(null, 'hh'), 'heures']],
        ];
    }
}
