<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class HarMeanTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerHARMEAN')]
    public function testHARMEAN(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCases('HARMEAN', $expectedResult, ...$args);
    }

    public static function providerHARMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/HARMEAN.php';
    }
}
