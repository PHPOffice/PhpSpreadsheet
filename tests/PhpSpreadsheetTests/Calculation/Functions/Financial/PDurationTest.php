<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PDurationTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPDURATION
     */
    public function testPDURATION(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('PDURATION', $expectedResult, $args);
    }

    public static function providerPDURATION(): array
    {
        return require 'tests/data/Calculation/Financial/PDURATION.php';
    }
}
