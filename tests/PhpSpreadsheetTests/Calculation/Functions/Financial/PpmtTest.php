<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PpmtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPPMT
     */
    public function testPPMT(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('PPMT', $expectedResult, $args);
    }

    public static function providerPPMT(): array
    {
        return require 'tests/data/Calculation/Financial/PPMT.php';
    }
}
