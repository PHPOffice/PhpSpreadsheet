<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class MatchTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerMATCH
     *
     * @param mixed $expectedResult
     * @param mixed $input
     * @param mixed $type
     */
    public function testMATCH($expectedResult, $input, array $array, $type = null): void
    {
        if (is_array($expectedResult)) {
            $expectedResult = $expectedResult[0];
        }
        if ($expectedResult === 'incomplete') {
            self::markTestIncomplete('Undefined behavior with different results in Excel and PhpSpreadsheet');
        }
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $maxRow = count($array);
        $formulaArray = '';
        for ($row = 1; $row <= $maxRow; ++$row) {
            $this->setCell("A$row", $array[$row - 1]);
            $formulaArray = "A1:A$row";
        }
        $this->setCell('B1', $input);
        if ($type === null) {
            $formula = "=MATCH(B1,$formulaArray)";
        } else {
            $formula = "=MATCH(B1, $formulaArray, $type)";
        }
        $sheet->getCell('D1')->setValue($formula);

        $result = $sheet->getCell('D1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider providerMATCH
     *
     * @param mixed $expectedResult
     * @param mixed $input
     * @param mixed $type
     */
    public function testMATCHLibre($expectedResult, $input, array $array, $type = null): void
    {
        $this->setOpenOffice();
        if (is_array($expectedResult)) {
            $expectedResult = $expectedResult[1];
        }
        if ($expectedResult === 'incomplete') {
            self::markTestIncomplete('Undefined behavior with different results in Excel and PhpSpreadsheet');
        }
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $maxRow = count($array);
        $formulaArray = '';
        for ($row = 1; $row <= $maxRow; ++$row) {
            $this->setCell("A$row", $array[$row - 1]);
            $formulaArray = "A1:A$row";
        }
        $this->setCell('B1', $input);
        if ($type === null) {
            $formula = "=MATCH(B1,$formulaArray)";
        } else {
            $formula = "=MATCH(B1, $formulaArray, $type)";
        }
        $sheet->getCell('D1')->setValue($formula);

        $result = $sheet->getCell('D1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerMATCH(): array
    {
        return require 'tests/data/Calculation/LookupRef/MATCH.php';
    }

    /**
     * @dataProvider providerMatchArray
     */
    public function testMatchArray(array $expectedResult, string $values, string $selections): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=MATCH({$values}, {$selections}, 0)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerMatchArray(): array
    {
        return [
            'row vector' => [
                [[2, 5, 3]],
                '{"Orange", "Blue", "Yellow"}',
                '{"Red", "Orange", "Yellow", "Green", "Blue"}',
            ],
        ];
    }
}
