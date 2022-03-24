<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\MatrixFunctions;

class SequenceTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSEQUENCE
     *
     * @param mixed[] $arguments
     * @param mixed[]|string $expectedResult
     */
    public function testSEQUENCE(array $arguments, $expectedResult): void
    {
        if (count($arguments) === 0) {
            $result = MatrixFunctions::sequence();
        } elseif (count($arguments) === 1) {
            $result = MatrixFunctions::sequence($arguments[0]);
        } elseif (count($arguments) === 2) {
            $result = MatrixFunctions::sequence($arguments[0], $arguments[1]);
        } elseif (count($arguments) === 3) {
            $result = MatrixFunctions::sequence($arguments[0], $arguments[1], $arguments[2]);
        } else {
            $result = MatrixFunctions::sequence($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
        }
        self::assertEquals($expectedResult, $result);
    }

    public function providerSEQUENCE(): array
    {
        return require 'tests/data/Calculation/MathTrig/SEQUENCE.php';
    }

    public function testSequenceAsCellFormula(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('B2')->setValue('=SEQUENCE(3,3,9,-1)', true, 'B2:D4');

        $result = $sheet->getCell('B2')->getCalculatedValue();
        self::assertSame(9, $result);

        // Check that spillage area has been populated
        self::assertSame(5, $sheet->getCell('C3')->getCalculatedValue());
    }

    public function testSequenceAsCellFormulaAsArray(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('B2')->setValue('=SEQUENCE(3,3,9,-1)', true, 'B2:D4');

        $result = $sheet->getCell('B2')->getCalculatedValue(true);
        self::assertSame([[9, 8, 7], [6, 5, 4], [3, 2, 1]], $result);

        // Check that spillage area has been populated
        self::assertSame(5, $sheet->getCell('C3')->getCalculatedValue());
    }
}
