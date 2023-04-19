<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TextJoinTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTEXTJOIN
     *
     * @param mixed $expectedResult
     */
    public function testTEXTJOIN($expectedResult, array $args): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $b1Formula = '=TEXTJOIN(';
        $comma = '';
        $row = 0;
        foreach ($args as $arg) {
            ++$row;
            $this->setCell("A$row", $arg);
            $b1Formula .= $comma . "A$row";
            $comma = ',';
        }
        $b1Formula .= ')';
        $this->setCell('B1', $b1Formula);
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public static function providerTEXTJOIN(): array
    {
        return require 'tests/data/Calculation/TextData/TEXTJOIN.php';
    }

    /**
     * @dataProvider providerTextjoinArray
     */
    public function testTextjoinArray(array $expectedResult, string $delimiter, string $blanks, string $texts): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=TEXTJOIN({$delimiter}, {$blanks}, {$texts})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerTextjoinArray(): array
    {
        return [
            'row vector #1' => [[['AB,CD,EF', 'AB;CD;EF']], '{",", ";"}', 'FALSE', '"AB", "CD", "EF"'],
            'column vector #1' => [[['AB--CD--EF'], ['AB|CD|EF']], '{"--"; "|"}', 'FALSE', '"AB", "CD", "EF"'],
            'matrix #1' => [[['AB,CD,EF', 'AB;CD;EF'], ['AB-CD-EF', 'AB|CD|EF']], '{",", ";"; "-", "|"}', 'FALSE', '"AB", "CD", "EF"'],
        ];
    }
}
