<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig\AllSetupTeardown;

class InfoTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerINFO')]
    public function testINFO(mixed $expectedResult, string $typeText): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue($typeText);
        $sheet->getCell('B1')->setValue('=INFO(A1)');
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerINFO(): array
    {
        return require 'tests/data/Calculation/Information/INFO.php';
    }
}
