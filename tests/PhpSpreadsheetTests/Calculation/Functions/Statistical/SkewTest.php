<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;

class SkewTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerSKEW')]
    public function testSKEW(mixed $expectedResult, array $args): void
    {
        $this->returnArrayAs = Calculation::RETURN_ARRAY_AS_VALUE;
        $this->runTestCaseReference('SKEW', $expectedResult, ...$args);
    }

    public static function providerSKEW(): array
    {
        return require 'tests/data/Calculation/Statistical/SKEW.php';
    }
}
