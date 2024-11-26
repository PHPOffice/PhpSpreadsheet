<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;

class SumIfsTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerSUMIFS')]
    public function testSUMIFS(mixed $expectedResult, mixed ...$args): void
    {
        $result = Statistical\Conditional::SUMIFS(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerSUMIFS(): array
    {
        return require 'tests/data/Calculation/MathTrig/SUMIFS.php';
    }
}
