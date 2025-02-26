<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class AverageTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerAVERAGE')]
    public function testAVERAGE(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('AVERAGE', $expectedResult, ...$args);
    }

    public static function providerAVERAGE(): array
    {
        return require 'tests/data/Calculation/Statistical/AVERAGE.php';
    }
}
