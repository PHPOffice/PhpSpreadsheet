<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class TextAfterTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerTEXTAFTER
     */
    public function testTextAfter(string $expectedResult, array $arguments): void
    {
        $text = $arguments[0];
        $delimiter = $arguments[1];

        $args = 'A1, A2';
        $args .= (isset($arguments[2])) ? ", {$arguments[2]}" : ',';
        $args .= (isset($arguments[3])) ? ", {$arguments[3]}" : ',';
        $args .= (isset($arguments[4])) ? ", {$arguments[4]}" : ',';

        $worksheet = $this->getSheet();
        $worksheet->getCell('A1')->setValue($text);
        $worksheet->getCell('A2')->setValue($delimiter);
        $worksheet->getCell('B1')->setValue("=TEXTAFTER({$args})");

        $result = $worksheet->getCell('B1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerTEXTAFTER(): array
    {
        return require 'tests/data/Calculation/TextData/TEXTAFTER.php';
    }

    public function testTextAfterWithArray(): void
    {
        $calculation = Calculation::getInstance();

        $text = "Red Riding Hood's red riding hood";
        $delimiter = 'red';

        $args = "\"{$text}\", \"{$delimiter}\", 1, {0;1}";

        $formula = "=TEXTAFTER({$args})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals([[' riding hood'], [" Riding Hood's red riding hood"]], $result);
    }
}
