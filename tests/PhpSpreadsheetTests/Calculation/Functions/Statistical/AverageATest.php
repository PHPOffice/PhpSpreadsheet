<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class AverageATest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerAVERAGEA')]
    public function testAVERAGEA(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('AVERAGEA', $expectedResult, ...$args);
    }

    public static function providerAVERAGEA(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGEA.php';
    }
}
