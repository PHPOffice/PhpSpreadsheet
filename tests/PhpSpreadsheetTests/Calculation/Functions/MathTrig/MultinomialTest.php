<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

class MultinomialTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMULTINOMIAL
     */
    public function testMULTINOMIAL(mixed $expectedResult, mixed ...$args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $row = 0;
        $excelArg = '';
        foreach ($args as $arg) {
            ++$row;
            $excelArg = "A1:A$row";
            if ($arg !== null) {
                $sheet->getCell("A$row")->setValue($arg);
            }
        }
        $sheet->getCell('B1')->setValue("=MULTINOMIAL($excelArg)");
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerMULTINOMIAL(): array
    {
        return require 'tests/data/Calculation/MathTrig/MULTINOMIAL.php';
    }
}
