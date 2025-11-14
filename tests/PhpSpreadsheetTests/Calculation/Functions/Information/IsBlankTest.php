<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IsBlankTest extends TestCase
{
    public function testIsBlankNoArgument(): void
    {
        $result = Value::isBlank();
        self::assertTrue($result);
    }

    #[DataProvider('providerIsBlank')]
    public function testIsBlank(bool $expectedResult, mixed $value): void
    {
        $result = Value::isBlank($value);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsBlank(): array
    {
        return require 'tests/data/Calculation/Information/IS_BLANK.php';
    }

    #[DataProvider('providerIsBlankArray')]
    public function testIsBlankArray(array $expectedResult, string $values): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ISBLANK({$values})";
        $result = $calculation->calculateFormula($formula);
        self::assertSame($expectedResult, $result);
    }

    public static function providerIsBlankArray(): array
    {
        return [
            'vector' => [
                [[false, true, false]],
                '{12, NULL, ""}',
            ],
        ];
    }
}
