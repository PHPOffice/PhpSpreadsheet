<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class CountIfTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerCOUNTIF
     *
     * @param mixed $expectedResult
     */
    public function testCOUNTIF($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('COUNTIF', $expectedResult, ...$args);
    }

    public function testMultipleRows(): void
    {
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['apples', 'oranges', 'peaches', 'apples'],
            ['bananas', 'mangoes', 'grapes', 'cherries'],
        ]);
        $sheet->getCell('Z99')->setValue('=COUNTIF(A1:D2,"*p*e*")');
        self::assertSame(4, $sheet->getCell('Z99')->getCalculatedValue());
    }

    public static function providerCOUNTIF(): array
    {
        return require 'tests/data/Calculation/Statistical/COUNTIF.php';
    }
}
