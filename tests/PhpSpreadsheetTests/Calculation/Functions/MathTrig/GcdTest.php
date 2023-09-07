<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class GcdTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerGCD
     */
    public function testGCD(mixed $expectedResult, mixed ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            if ($arg !== null) {
                $sheet->getCell("A$row")->setValue($arg);
            }
        }
        if ($row < 1) {
            $sheet->getCell('B1')->setValue('=GCD()');
        } else {
            $sheet->getCell('B1')->setValue("=GCD(A1:A$row)");
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerGCD(): array
    {
        return require 'tests/data/Calculation/MathTrig/GCD.php';
    }
}
