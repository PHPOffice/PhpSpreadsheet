<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class RankTest extends AllSetupTeardown
{
    /**
     * @param mixed[] $valueSet
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerRANK')]
    public function testRANK(mixed $expectedResult, mixed $value, array $valueSet, mixed $order = null): void
    {
        if ($order === null) {
            $this->runTestCaseReference('RANK', $expectedResult, $value, $valueSet);
        } else {
            $this->runTestCaseReference('RANK', $expectedResult, $value, $valueSet, $order);
        }
    }

    public static function providerRANK(): array
    {
        return require 'tests/data/Calculation/Statistical/RANK.php';
    }
}
