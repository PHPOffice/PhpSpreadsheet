<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class PDurationTest extends TestCase
{
    /**
     * @dataProvider providerPDURATION
     *
     * @param mixed $expectedResult
     */
    public function testPDURATION($expectedResult, array $args): void
    {
        if (count($args) === 0) {
            $result = Financial::PDURATION();
        } elseif (count($args) === 1) {
            $result = Financial::PDURATION($args[0]);
        } elseif (count($args) === 2) {
            $result = Financial::PDURATION($args[0], $args[1]);
        } else {
            $result = Financial::PDURATION($args[0], $args[1], $args[2]);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPDURATION(): array
    {
        return require 'tests/data/Calculation/Financial/PDURATION.php';
    }
}
