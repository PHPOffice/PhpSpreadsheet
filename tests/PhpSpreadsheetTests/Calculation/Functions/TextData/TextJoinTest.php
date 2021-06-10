<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

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

    public function providerTEXTJOIN(): array
    {
        return require 'tests/data/Calculation/TextData/TEXTJOIN.php';
    }
}
