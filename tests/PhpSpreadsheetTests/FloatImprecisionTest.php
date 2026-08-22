<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FloatImprecisionTest extends TestCase
{
    #[DataProvider('providerFloats')]
    public function testCompareFloats(float $float1, float $float2): void
    {
        self::assertSame($float1, $float2);
    }

    public static function providerFloats(): array
    {
        return [
            [12345.6789, 12345.67890000000079],
        ];
    }
}
