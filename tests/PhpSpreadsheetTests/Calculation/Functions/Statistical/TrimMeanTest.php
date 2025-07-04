<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PHPUnit\Framework\Attributes\DataProvider;

class TrimMeanTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerTRIMMEAN')]
    public function testTRIMMEAN(mixed $expectedResult, array $args, mixed $percentage): void
    {
        $this->runTestCaseReference('TRIMMEAN', $expectedResult, $args, $percentage);
    }

    public static function providerTRIMMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/TRIMMEAN.php';
    }
}
