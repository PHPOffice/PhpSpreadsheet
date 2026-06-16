<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PHPUnit\Framework\Attributes\DataProvider;

class PvTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerPV')]
    public function testPV(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('PV', $expectedResult, $args);
    }

    public static function providerPV(): array
    {
        return require 'tests/data/Calculation/Financial/PV.php';
    }
}
